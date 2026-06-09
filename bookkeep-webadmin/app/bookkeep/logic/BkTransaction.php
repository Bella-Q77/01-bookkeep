<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkTransactionor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;

/**
 * 收支记录管理=》逻辑层
 */
class BkTransaction extends BookkeepBase
{

    /**
     * 收支记录列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkTransactionList($where = [], $field = '', $order = '', $paginate = DB_LIST_ROWS)
    {
        $field = 'a.*,c.name as category_name,c.category_icon,
                    acc.name as account_name,
                    s.name as shop_name,
                    per.name as person_name,
                    prj.name as project_name';
        $order = "a.id desc";
        $join = [
            ['bk_category c', 'c.id = a.category_id', 'LEFT'],
            ['bk_account acc', 'acc.id = a.account_id', 'LEFT'],
            ['bk_shop s', 's.id = a.shop_id', 'LEFT'],
            ['bk_person per', 'per.id = a.person_id', 'LEFT'],
            ['bk_project prj', 'prj.id = a.project_id', 'LEFT'],
        ];
        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $list = $this->modelBkTransaction->getList($where, $field, $order, $paginate)->toArray();
        return $list;
    }

    //获取收支记录列表统计
    public function getBkTransactionListTotal($where = [])
    {
        $join = [
            ['bk_category c', 'c.id = a.category_id', 'LEFT'],
            ['bk_account acc', 'acc.id = a.account_id', 'LEFT'],
            ['bk_shop s', 's.id = a.shop_id', 'LEFT'],
            ['bk_person per', 'per.id = a.person_id', 'LEFT'],
            ['bk_project prj', 'prj.id = a.project_id', 'LEFT'],
        ];
        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $rtnArray['balance_amount'] = $this->modelBkTransaction->stat($where, 'sum', 'a.amount');

        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $where['a.type'] = ['=', '1'];
        $rtnArray['income_amount'] = $this->modelBkTransaction->stat($where, 'sum', 'a.amount');

        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $where['a.type'] = ['=', '-1'];
        $rtnArray['expense_amount'] = $this->modelBkTransaction->stat($where, 'sum', 'a.amount');
        return $rtnArray;
    }

    /**
     * 收支记录添加
     * @param array $data
     * @return array
     */
    public function bkTransactionAdd($data = [])
    {
        //添加默认数据
        if (empty($data['book_id'])) {
            $data['book_id'] = $this->bookId;
        }
        if (empty($data['member_id'])) {
            $data['member_id'] = $this->memberId;
        }

        $validate_result = $this->validateBkTransaction->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkTransaction->getError()];
        }

        $data['amount'] = $data['amount'] * $data['type'];//收支金额
        $result = $this->modelBkTransaction->setInfo($data);

        //添加账户余额
        $result && $this->modelBkAccount->addAccountBalance($data['account_id'], $data['amount']);

        $url = url('show');

        session('history_transaction', $data);

        return $result ? [RESULT_SUCCESS, '' . lang('添加成功') . '', $url] : [RESULT_ERROR, $this->modelBkTransaction->getError()];
    }

    /**
     * 收支记录编辑
     * @param array $data
     * @return array
     */
    public function bkTransactionEdit($data = [])
    {
        $validate_result = $this->validateBkTransaction->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkTransaction->getError()];
        }
        $data['amount'] = $data['amount'] * $data['type'];//收支金额

        //更新账户余额
        $this->modelBkAccount->syncAccountBalance($data['account_id']);

        $url = url('show');
        $where['id'] = ['=', $data['id']];
        $result = $this->modelBkTransaction->updateInfo($where, $data);
        return $result ? [RESULT_SUCCESS, '' . lang('编辑成功') . '', $url] : [RESULT_ERROR, $this->modelBkTransaction->getError()];
    }

    /**
     * 收支记录删除
     * @param array $where
     * @return array
     */
    public function bkTransactionDel($where = [])
    {
        $result = $this->modelBkTransaction->deleteInfo($where, true);

        $result && action_log('删除', '删除收支记录，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkTransaction->getError()];
    }

    /**
     * 收支记录信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkTransactionInfo($where = [], $field = true)
    {
        $field = 'a.*,c.name as category_name,c.category_icon,
                    acc.name as account_name,
                    s.name as shop_name,
                    per.name as person_name,
                    prj.name as project_name';
        $join = [
            ['bk_category c', 'c.id = a.category_id', 'LEFT'],
            ['bk_account acc', 'acc.id = a.account_id', 'LEFT'],
            ['bk_shop s', 's.id = a.shop_id', 'LEFT'],
            ['bk_person per', 'per.id = a.person_id', 'LEFT'],
            ['bk_project prj', 'prj.id = a.project_id', 'LEFT'],
        ];
        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        return $this->modelBkTransaction->getInfo($where, $field);
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

        if (!empty($data['category_id'])) {
            $where['a.category_id'] = ['=', $data['category_id']];
        }

        if (!empty($data['account_id'])) {
            $where['a.account_id'] = ['=', $data['account_id']];
        }

        if (!empty($data['type'])) {
            $where['a.type'] = ['=', $data['type']];
        }

        //时间类型，默认为当月
        if(!empty($data['period_date'])){
            $rangedate = rangedate2arr($data['period_date']);
            $where['a.transaction_date'] = ['between', [$rangedate[0], $rangedate[1]]];
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

    public function getBkTransactionAddList($data = [])
    {
        if (!empty($data['book_id'])) {
            $where['a.book_id'] = ['=', $data['book_id']];
        } else {
            $where['a.book_id'] = ['=', $this->bookId];
        }
        !empty($data['a.keywords']) && $where['remark'] = ['like', '%' . $data['keywords'] . '%'];

        if (!empty($data['category_id'])) {
            $where['a.category_id'] = ['=', $data['category_id']];
        }
        //时间类型，默认为当月
        $currMonth = getMonthStartEndTime();
        $where['a.transaction_date'] = ['between', [$currMonth['begin'], $currMonth['end']]];

        $periodDate = date('Y-m-d');
        if (!empty($data['period_date'])) {
            $periodDate = $data['period_date'];
        }
        if (!empty($data['period_type'])) {

            if ($data['period_type'] == 'month') {
                $currMonth = getMonthStartEndTime($periodDate);
                $where['a.transaction_date'] = ['between', [$currMonth['begin'], $currMonth['end']]];
            }
            if ($data['period_type'] == 'quarter') {
                $currQuarter = getQuarterStartEndTime($periodDate);
                $where['a.transaction_date'] = ['between', [$currQuarter['begin'], $currQuarter['end']]];
            }
            if ($data['period_type'] == 'year') {
                $currYear = getYearStartEndTime($periodDate);
                $where['a.transaction_date'] = ['between', [$currYear['begin'], $currYear['end']]];
            }
            if ($data['period_type'] == 'diy') {
                $currData = rangedate2arr($data['period_date']);
                $where['a.transaction_date'] = ['between', [$currData[0], $currData[0]]];
            }
        }

        $field = 'a.*,c.name as category_name,acc.name as account_name,s.name as shop_name,per.name as person_name,prj.name as project_name';
        $order = "a.id desc";
        $join = [
            ['bk_category c', 'c.id = a.category_id', 'LEFT'],
            ['bk_account acc', 'acc.id = a.account_id', 'LEFT'],
            ['bk_shop s', 's.id = a.shop_id', 'LEFT'],
            ['bk_person per', 'per.id = a.person_id', 'LEFT'],
            ['bk_project prj', 'prj.id = a.project_id', 'LEFT'],
        ];

        //统计数据=>获取收入
        $where['a.type'] = ['=', '1'];
        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $income = $this->modelBkTransaction->getInfo($where, 'sum(a.amount) as  income_total_amount');
        $incomeAamount = $income['income_total_amount'];

        //统计数据=>获取支出
        $where['a.type'] = ['=', '-1'];
        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $expense = $this->modelBkTransaction->getInfo($where, 'sum(a.amount) as  expense_total_amount');
        $expenseAmount = $expense['expense_total_amount'];
        //获取列表
        unset($where['a.type']);
        $this->modelBkTransaction->alias('a');
        $this->modelBkTransaction->join = $join;
        $list = $this->modelBkTransaction->getList($where, $field, $order, false)->toArray();
        foreach ($list as &$row) {
            // $row['amount'] = $row['type'] * $row['amount'];
        }
        $rtnArray = [
            'code' => 1,
            'data' => [
                'list' => $list,
                'total_nums' => count($list),
                'income_total_amount' => $incomeAamount,
                'expense_total_amount' => $expenseAmount,
                'balance_total_amount' => round($incomeAamount + $expenseAmount, 2),
            ],
        ];
        return $rtnArray;
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
