<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkTransferor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;

/**
 * 转账记录管理=》逻辑层
 */
class BkTransfer extends BookkeepBase
{

    /**
     * 转账记录列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkTransferList($where = [], $field = '', $order = '', $paginate = DB_LIST_ROWS)
    {
        $field = 'a.*,fc.name as from_account_name, ft.name as to_account_name';
        $order = "a.id desc";
        $join = [
            ['bk_account fc', 'fc.id = a.from_account_id', 'LEFT'],
            ['bk_account ft', 'ft.id = a.to_account_id', 'LEFT'],
        ];
        $this->modelBkTransfer->alias('a');
        $this->modelBkTransfer->join = $join;
        $list = $this->modelBkTransfer->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 转账记录添加
     * @param array $data
     * @return array
     */
    public function bkTransferAdd($data = [])
    {
        $validate_result = $this->validateBkTransfer->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkTransfer->getError()];
        }
        $data['book_id'] = $this->bookId;
        $data['member_id'] = $this->memberId;
        $result = $this->modelBkTransfer->setInfo($data);

        //添加账户余额
        $result && $this->modelBkTransfer->addTransferBalance($data['from_account_id'], $data['to_account_id'], $data['amount']);

        $url = url('show');
        $msg = '转账成功，转出账户：' . $data['from_account_name'] . '，转入账户：' . $data['to_account_name'] . '，金额：' . $data['amount'];
        $result && action_log('新增', '新增转账记录：' . $msg);

        session('history_transaction', $data);

        return $result ? [RESULT_SUCCESS, '' . lang('添加成功') . '', $url] : [RESULT_ERROR, $this->modelBkTransfer->getError()];
    }

    /**
     * 转账记录编辑
     * @param array $data
     * @return array
     */
    public function bkTransferEdit($data = [])
    {
        $validate_result = $this->validateBkTransfer->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkTransfer->getError()];
        }
        //更新账户余额
        $this->modelBkTransfer->editTransferBalance($data['id'], $data['amount']);

        $url = url('show');
        $where['id'] = ['=', $data['id']];
        $result = $this->modelBkTransfer->updateInfo($where, $data);
        $msg = '编辑转账记录，转出账户：' . $data['from_account_name'] . '，转入账户：' . $data['to_account_name'] . '，金额：' . $data['amount'];
        $result && action_log('编辑', $msg);
        return $result ? [RESULT_SUCCESS, '' . lang('编辑成功') . '', $url] : [RESULT_ERROR, $this->modelBkTransfer->getError()];
    }

    /**
     * 转账记录删除
     * @param array $where
     * @return array
     */
    public function bkTransferDel($data = [])
    {
        $where['id'] = ['=', $data['id']];
        $this->modelBkTransfer->delTransferBalance($data['id']);
        $result = $this->modelBkTransfer->deleteInfo($where, true);
        $result && action_log('删除', '删除转账记录，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('删除成功') . ''] : [RESULT_ERROR, $this->modelBkTransfer->getError()];
    }

    /**
     * 转账记录信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkTransferInfo($where = [], $field = true)
    {
        $field = 'a.*,fc.name as from_account_name, ft.name as to_account_name';
        $join = [
            ['bk_account fc', 'fc.id = a.from_account_id', 'LEFT'],
            ['bk_account ft', 'ft.id = a.to_account_id', 'LEFT'],
        ];
        $this->modelBkTransfer->alias('a');
        $this->modelBkTransfer->join = $join;
        return $this->modelBkTransfer->getInfo($where, $field);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
        $where = [];
        if (!empty($data['member_id'])) {
            $where['a.member_id'] = ['=', $data['member_id']];
        }
        if (!empty($data['book_id'])) {
            $where['a.book_id'] = ['=', $data['book_id']];
        }
        //关键字查
        !empty($data['keywords']) && $where['a.remark'] = ['like', '%' . $data['keywords'] . '%'];

        //时间类型，默认为当月
        if(!empty($data['period_date'])){
            $rangedate = rangedate2arr($data['period_date']);
            $where['a.transfer_date'] = ['between', [$rangedate[0], $rangedate[1]]];
        }

        return $where;
    }

    /**
     * 获取排序条件
     */
    public function getOrderBy($data = [])
    {
        $order_by = "id asc";
        //排序操作
        if (!empty($data['orderField'])) {
            $orderField = $data['orderField'];
            $orderDirection = $data['orderDirection'];
            switch ($orderField) {
                case 'sort':
                    $order_by = "sort $orderDirection";
                    break;
                case 'name':
                    $order_by = "name $orderDirection";
                    break;
            }
        }
        return $order_by;
    }

    /**
     * @param $where
     * @return mixed
     */
    public function getWhere1($where)
    {
        $where['a.type'] = ['=', '1'];
        return $where;
    }
}
