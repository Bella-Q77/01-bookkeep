<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberAdvor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员购买广告管理=》逻辑层
 */
class MemberAdv extends PortalmemberBase
{
    /**
     * 会员购买广告列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberAdvList($where = [], $field = 'a.*', $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $this->modelMemberAdv->alias('a');

        $list= $this->modelMemberAdv->getList($where, $field, $order, $paginate);

        return $list;
    }

    /**会员自助广告信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberAdvInfo($where = [], $field = true)
    {
        $info=$this->modelMemberAdv->getInfo($where, $field);
        return $info;
    }

    /**
     * 会员购买广告=>购买
     * @param array $data
     * @return array
     */
    public function memberAdvBuy($data = [])
    {
        $info=$this->modelMemberAdv->getInfo(['id'=>$data['id']]);
        if($info){
            $money=(float)$data['price']*(float)$data['days'];
            $start_date=$data['start_date'];
            $stop_date=date_calc($data['start_date'],'+'.$data['days']);

            //更新订单信息
            $adv_data=[
                'adv_id'=>$data['id'],
                'member_id'=>MEMBER_ID,
                'money'=>$money,
                'start_date'=>$start_date,
                'stop_date'=>$stop_date,
                'title'=>$data['title'],
                'litpic'=>$data['litpic'],
                'links'=>$data['links'],
                'body'=>$data['body'],
                'period'=>$data['days'],
            ];
            $adsid=$this->modelMemberAdvDis->setInfo($adv_data);

            $order_code='GG-'.$info['id'].'-'.date("ymdHis");
            $order_data=[
                'order_code'=>$order_code,
                'member_id'=>empty($data['MEMBER_ID'])?MEMBER_ID:$data['MEMBER_ID'],
                'bus_id'=>$adsid,
                'bus_type'=>'member_ad',
                'order_amount'=>$money,
                'name'=>'购买广告：'.$info['name'].'，周期：'.$data['days'].'天',
            ];
            $orderid = $this->logicMemberOrder->memberOrderAdd($order_data);

            //关联订单
            $updisdata['order_id']=$orderid;
            $updisdata['order_code']=$order_code;
            $result = $this->modelMemberAdvDis->updateInfo(['id' => $adsid],$updisdata);
        }

        $url = url('portalmember/MemberOrder/pay',array('id'=>$orderid));
        return $result ? [RESULT_SUCCESS, '下单成功', $url] : [RESULT_ERROR, $this->modelMemberAdv->getError()];
    }

}
