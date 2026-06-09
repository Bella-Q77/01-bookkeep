<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberProductIntegralor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员积分管理=》逻辑层
 */
class MemberProductIntegral extends PortalmemberBase
{
    /**
     * 会员积分列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberProductIntegralList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list= $this->modelMemberProductIntegral->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 会员积分添加
     * @param array $data
     * @return array
     */
    public function memberProductIntegralAdd($data = [])
    {

        $validate_result = $this->validateMemberProductIntegral->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberProductIntegral->getError()];
        }
        $result = $this->modelMemberProductIntegral->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员积分：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberProductIntegral->getError()];
    }


    /**
     * 会员积分=>购买
     * @param array $data
     * @return array
     */
    public function memberProductIntegralBuy($data = [])
    {
        $info=$this->modelMemberProductIntegral->getInfo(['id'=>$data['id']]);
        if($info){
            $order_data=[
                'order_code'=>'JF-'.$info['id'].'-'.date("ymdHis"),
                'member_id'=>MEMBER_ID,
                'bus_id'=>$info['id'],
                'bus_type'=>'integral',
                'order_amount'=>$info['price'],
                'name'=>$info['name'].'实际到帐积分'.$info['integral'],
            ];
            $orderid = $this->logicMemberOrder->memberOrderAdd($order_data);
        }
        $url = url('portalmember/MemberOrder/pay',array('id'=>$orderid));
        return $orderid ? [RESULT_SUCCESS, '下单成功', $url] : [RESULT_ERROR, $this->modelMemberProductIntegral->getError()];
    }

}
