<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberProductLevelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员等级管理=》逻辑层
 */
class MemberProductLevel extends PortalmemberBase
{
    /**
     * 会员等级列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberProductLevelList($where = [], $field = 'a.*,b.level_name', $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $this->modelMemberProductLevel->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'member_level b', 'b.id = a.level_id'],
        ];

        $this->modelMemberProductLevel->join = $join;

        $list= $this->modelMemberProductLevel->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 会员等级=>购买
     * @param array $data
     * @return array
     */
    public function memberProductLevelBuy($data = [])
    {
        $info=$this->modelMemberProductLevel->getInfo(['id'=>$data['id']]);
        if($info){
            $order_data=[
                'order_code'=>'SJ-'.$info['id'].'-'.date("ymdHis"),
                'member_id'=>MEMBER_ID,
                'bus_id'=>$info['id'],
                'bus_type'=>'level',
                'order_amount'=>$info['price'],
                'name'=>$info['name'],
            ];
            $orderid = $this->logicMemberOrder->memberOrderAdd($order_data);
        }
        $url = url('portalmember/MemberOrder/pay',array('id'=>$orderid));
        return $orderid ? [RESULT_SUCCESS, '下单成功', $url] : [RESULT_ERROR, $this->modelMemberProductLevel->getError()];
    }

}
