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
 * 会员订单管理=》逻辑层
 */
class MemberOrder extends PortalmemberBase
{

    /**
     * 会员订单添加=>成功返回订单信息
     * @param array $data
     * @return array
     */
    public function memberOrderAdd($data = [])
    {
        $validate_result = $this->validateMemberOrder->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberOrder->getError()];
        }
        $result = $this->modelMemberOrder->setInfo($data);
        return $result;
    }


    /**
     * 会员订单删除
     * @param array $where
     * @return array
     */
    public function memberOrderDel($data = [])
    {
        $where['id'] = ['in', $data['id']];
        $result = $this->modelMemberOrder->deleteInfo($where, true);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '删除成功', $url] : [RESULT_ERROR, $this->modelMemberOrder->getError()];
    }

    /**
     * 会员订单列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberOrderList($where = [], $field = true, $order = 'id desc', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelMemberOrder->getList($where, $field, $order, $paginate);
        foreach ($list as &$row) {
            $row['bus_info'] = $this->modelMemberOrder->bus_type($row['bus_type']);
            $row['payment_info'] = $this->modelMemberOrder->payment_status($row['payment_status']);
        }
        return $list;
    }

    /**会员订单信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberOrderInfo($where = [], $field = true)
    {
        $info = $this->modelMemberOrder->getInfo($where, $field);
        $info['bus_info'] = $this->modelMemberOrder->bus_type($info['bus_type']);
        return $info;
    }


    /**
     * 会员订单=支付
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberOrderPay($data = [])
    {
        $info = $this->modelMemberOrder->getInfo(['id' => $data['id']]);
        $orderinfo['body'] = $info['name'];
        $orderinfo['order_amount'] = $info['order_amount'];
        $orderinfo['order_sn'] = $info['order_code'];
        $diy_data=[
            'order_id'=>$info['id'],
            'order_code'=>$info['order_code'],
            'order_amount'=>$info['order_amount'],
            'create_time'=>$info['create_time'],
        ];
        $diy_sgin=data_md5_key($diy_data);
        $orderinfo['body'] = $info['name'];
        $orderinfo['order_amount'] = $info['order_amount'];
        $orderinfo['order_sn'] = $info['order_code'];
        $orderinfo['notify_url'] = DOMAIN.url('portalmember/MemberOrderCalback/order_pay_notify',array('order_code'=>$info['order_code'],'order_id'=>$info['id'],'payway'=>'weixin','diy'=>$diy_sgin));
        //发起支付
        $result=$this->servicePay->driverWxpay->pay($orderinfo);

        if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS'){
            $code_url = $result["code_url"];//从统一支付接口获取到code_url
            $dirname=PATH_PUBLIC.'qrcode'.DS;
            $qrcode=create_qrcode($data = $code_url, $path = $dirname, $ecc = 'H', $size = 10);
            if($qrcode){
                $pay_qrcode=DOMAIN.'/qrcode/'.$qrcode['name'];
            }
            return [RESULT_SUCCESS, $pay_qrcode, '',$pay_qrcode];
        }else{
            $url=url('portalmember/MemberOrder/show');
            $msg=empty($result['err_code_des'])?'':$result['err_code_des'];
            $msg='支付失败:'.$msg;
            return [RESULT_ERROR, $msg, $url];
        }
    }

    /**
     * 会员订单是否支付检查
     * @param array $data
     * @return array
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/1/17 0017 17:17
     */
    public function memberOrderCheck($data = [])
    {
        $info = $this->modelMemberOrder->getInfo(['id' => $data['id']]);
        if($info){
            if($info['payment_status']==1 && !empty($info['pay_transaction_no'])){
                $url=url('portalmember/MemberOrder/show');
                return [RESULT_SUCCESS, '支付成功',$url];
            }
        }

        return [RESULT_ERROR, '未支付'];
    }

    /**
     * 会员订单=支付回调
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberOrderPayCalback($data = [])
    {

        $orderinfo = $this->modelMemberOrder->getInfo(['id' => $data['id']]);

        if ($orderinfo['bus_type'] == 'integral') {

            $res = $this->integralPayCalback($orderinfo);

        } else if ($orderinfo['bus_type'] == 'level') {

            $res = $this->levelPayCalback($orderinfo);

        } else if ($orderinfo['bus_type'] == 'infotop') {

            $res = $this->istopPayCalback($orderinfo);

        }else if ($orderinfo['bus_type'] == 'member_ad') {

            $res = $this->adsPayCalback($orderinfo);
        }

        //更新订单
        $updata = [
            'pay_time' => format_time(),
            'pay_name' => 'weixin',
            'payment_status' => '1',
        ];

        if ($res) {
            $result = $this->modelMemberOrder->updateInfo(['id' => $data['id']], $updata);
        }

        return $result ? [RESULT_SUCCESS, '删除成功', ''] : [RESULT_ERROR, $this->modelMemberOrder->getError()];

    }

    /**会员订单=支付回调=>积分更改
     * @param array $where
     * @param bool $field
     * @return
     */
    public function integralPayCalback($orderinfo = [])
    {
        $integral = $this->modelMemberProductIntegral->getValue(['id' => $orderinfo['bus_id']], 'integral');
        $member_integral = $this->modelMember->getValue(['id' => MEMBER_ID], 'member_integral');
        //增加记录
        $member_integral = $integral + $member_integral;
        $intodata = [
            'member_id' => MEMBER_ID,
            'integral' => $integral,
            'member_integral' => $member_integral,
            'cause' => '购买'.$orderinfo['name'],
            'cause_type' => 'buy',
        ];
        $result = $this->modelMemberIntegral->setInfo($intodata);
        $result && $this->modelMember->setFieldValue(['id' => MEMBER_ID], 'member_integral', $member_integral);
        return $result;
    }

    /**会员订单=支付回调=>置顶信息
     * 0=未推广，1=待支付，2=推广中
     * @param array $where
     * @param bool $field
     * @return
     */
    public function istopPayCalback($orderinfo = [])
    {
        $info = $this->modelInfo->getInfo(['id' => $orderinfo['bus_id']]);
        $result = $this->modelInfo->setFieldValue(['id' => $info['id']], 'istop', '2');
        return $result;
    }

    /**会员订单=支付回调=>会员升级
     * @param array $where
     * @param bool $field
     * @return
     */
    public function levelPayCalback($orderinfo = [])
    {
        $level_id = $this->modelMemberProductLevel->getValue(['id' => $orderinfo['bus_id']], 'level_id');
        $result = $this->modelMember->setFieldValue(['id' => MEMBER_ID], 'level_id', $level_id);
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
