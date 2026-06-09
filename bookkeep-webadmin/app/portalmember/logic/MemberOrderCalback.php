<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberOrderor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员订单回调管理=》逻辑层
 */
class MemberOrderCalback extends PortalmemberBase
{
	/**会员订单=支付回调
	 * @param array $where
	 * @param bool $field
	 * @return
	 */
	public function getMemberOrderPayCalback($data = [])
	{

        $time=format_time();

        dlog('getMemberOrderPayCalback:开始用时间：'.$time);
        
        $result= $this->servicePay->driverWxpay->notify();//返回回调地址

        $datastring=json_encode($data);
        $resultstring=json_encode($result);


        dlog('传入的数据'.$data);
        dlog($data);
        dlog('解析的数据'.$result);
        dlog($result);


        //回调返回结果
        if($result['result_code']=='SUCCESS' && $result['return_code']=='SUCCESS'){

            $orderinfo = $this->modelMemberOrder->getInfo(['id' => $data['order_id']]);

            if($orderinfo){

                //签名回调验证
                $diy_data=[
                    'order_id'=>$orderinfo['id'],
                    'order_code'=>$orderinfo['order_code'],
                    'order_amount'=>$orderinfo['order_amount'],
                    'create_time'=>$orderinfo['create_time'],
                ];
                $diy_sgin=data_md5_key($diy_data);

                if($diy_sgin==$data['diy']){//自定义签名
                    $updata=[
                        'pay_name'=>$data['pay_name'],
                        'pay_transaction_no'=>$result['transaction_id'],//交易单号
                        'payment_status'=>'1',
                        'pay_time'=>$time,
                        'pay_details'=>$resultstring,
                    ];

                    //更新订单状态
                    $where['id']=$data['order_id'];
                    $this->modelMemberOrder->updateInfo($where,$updata);

                    //更新订单关联的业务
                    if ($orderinfo['bus_type'] == 'integral') {

                        $res = $this->integralPayCalback($orderinfo);

                    } else if ($orderinfo['bus_type'] == 'level') {

                        $res = $this->levelPayCalback($orderinfo);

                    } else if ($orderinfo['bus_type'] == 'infotop') {

                        $res = $this->istopPayCalback($orderinfo);

                    } else if ($orderinfo['bus_type'] == 'member_ad') {

                        $res = $this->adsPayCalback($orderinfo);
                    }

                    dlog('更新订单参数'.$updata);
                    dlog('更新订单条件'.$where);
                    dlog('更新后结果'.$res);

                }else{
                    dlog('回调订单参数非法，签名不通过');
                    dlog('CC='.$diy_sgin);
                    dlog('GG='.$data['diy']);
                }
            }else{
                dlog('回调订单数据不存在');
            }
        }
        dlog('本次结束时间'.$time);
        echo '<xml> <return_code><![CDATA[SUCCESS]]></return_code> <return_msg><![CDATA[OK]]></return_msg> </xml>';
        exit;
	}

	/**会员订单=支付回调=>积分更改
	 * @param array $where
	 * @param bool $field
	 * @return
	 */
	public function integralPayCalback($orderinfo = [])
	{
        $integral = $this->modelMemberProductIntegral->getInfo(['id' => $orderinfo['bus_id']]);
        $member = $this->modelMember->getInfo(['id' => $orderinfo['member_id']]);

        //会员新积分
        $member_integral = $integral['integral'] + $integral['integral_give'] + $member['member_integral'];
        //增加会员积分记录
        $intodata = [
            'member_id' => $orderinfo['member_id'],
            'integral' => $integral['integral'] + $integral['integral_give'],
            'member_integral' => $member_integral,
            'cause' => '购买' . $orderinfo['name'],
            'cause_type' =>'buy-order-'.$orderinfo['id'].':'.$orderinfo['order_code'],
        ];
        $result = $this->modelMemberIntegral->setInfo($intodata);

        //更新用户数据
        $member_updata=[
            'member_integral'=>$member_integral,
            'is_recharge'=>'1',//充值过
        ];

        //判断是否有赠送vip的天数
        $vip_days=$integral['days'];
        if($vip_days){
            //表示目前是VIP会员
            if ($member['level_id'] > 1) {
                if ($member['expire_level_time'] > time()) {
                    $member_updata['expire_level_time'] = strtotime(" + $vip_days day", $member['expire_level_time']);
                }
            } else {
                $member_updata['expire_level_time'] = strtotime(" + $vip_days day", time());
                $member_updata['level_id'] =$integral['level_id'];
            }
        }
        $result && $this->modelMember->updateInfo(['id' => $orderinfo['member_id']], $member_updata);

		return $result;
	}

	/**会员订单=支付回调=>会员升级
	 * @param array $where
	 * @param bool $field
	 * @return
	 */
	public function levelPayCalback($orderinfo = [])
	{
        $member = $this->modelMember->getValue(['id' => $orderinfo['member_id']], 'member_integral');
        $level  = $this->modelMemberProductLevel->getInfo(['id' => $orderinfo['bus_id']], 'period,level_id');
        $period = $level['period'];
		//表示目前是会员
		if ($member['level_id'] > 1) {
			if ($member['expire_level_time'] > time()) {
				$updata['expire_level_time'] = strtotime(" + $period day", $member['expire_level_time']);
			}else{
                $updata['expire_level_time'] = strtotime(" + $period day", time());
            }
		} else {
			$updata['expire_level_time'] = strtotime(" + $period day", time());
		}

		$updata['level_id']=$level['level_id'];
		$updata['is_recharge']=1;
		$result = $this->modelMember->updateInfo(['id' => $orderinfo['member_id']], $updata);
		return $result;
	}

	/**会员订单=支付回调=>购买广告
	 * @param array $where
	 * @param bool $field
	 * @return
	 */
	public function adsPayCalback($orderinfo = [])
	{
		$result = $this->modelMemberAdvDis->setFieldValue(['id' => $orderinfo['bus_id']], 'status', '1');
		return $result;
	}

}
