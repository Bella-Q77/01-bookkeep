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

namespace app\member\logic;

/**
 * 会员等级升级管理=》逻辑层
 */
class MemberOrder extends MemberBase
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


        $total_money=$this->modelMemberOrder->stat($where, 'sum', 'order_amount');

        $list = $this->modelMemberOrder->getList($where, $field, $order, $paginate);
		foreach ($list as &$row) {
			$row['bus_info'] = $this->modelMemberOrder->bus_type($row['bus_type']);
			$row['member_name'] = $this->modelMember->getValue(['id'=>$row['member_id']],'username');
			$row['payment_info'] = $this->modelMemberOrder->payment_status($row['payment_status']);
			$row['payment_method_info'] = $this->modelMemberOrder->payment_method($row['payment_method']);
		}

        $list=$list->toArray();
        $list['page_total_money']=get_2arr_sum($list['data'],'order_amount');

        $list['all_total_money']=$total_money;

		return $list;
	}

	/**
	 * 查询条件组合
	 * @param array $data
	 * @return array|mixed
	 * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/6 0006
	 */
	public function getWhere($data = [])
	{
		$where = [];
		if (!empty($data['keywords'])) {
			$where['name|order_code'] = ['like', '%' . $data['keywords'] . '%'];
		}

		if (!empty($data['pay_status']) || is_numeric($data['pay_status'])) {
			$where['payment_status'] = ['=', $data['payment_status']];
		}
		if (!empty($data['bus_type']) || is_numeric($data['bus_type'])) {
			$where['bus_type'] = ['=', $data['bus_type']];
		}
		return $where;
	}

	/**
	 * 会员订单支付状态
	 * @param array $where
	 * @return array
	 */
	public function getPayStatus($key = '')
	{
		return $this->modelMemberOrder->payment_status($key);
	}

	/**
	 * 会员订单支付方式
	 * @param array $where
	 * @return array
	 */
	public function getPayMethod($key = '')
	{
		return $this->modelMemberOrder->payment_method($key);
	}

	/**
	 * 会员订单=>类型
	 * @param array $where
	 * @return array
	 */
	public function getBusType($key = '')
	{
		return $this->modelMemberOrder->bus_type($key);
	}

}
