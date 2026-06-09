<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkBudgetor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 预算管理=》逻辑层
 */
class BkBudget extends PortalmemberBookkeepBase
{
    //获取预算数据
    public function getBkBudgetData($data = [])
    {
        //1、获取预算数据
        if (empty($data['period_type'])) {
            return [RESULT_ERROR, '请选择预算类型'];
        }
        if (!empty($data['book_id'])) {
            $where['book_id'] = ['=', $data['book_id']];
            $bookId=$data['book_id'];
        } else {
            $where['book_id'] = ['=', $this->bookId];
            $bookId=$this->bookId;
        }

        $where['budget_type'] = ['=', '0'];//预算类型,0=总体预算，1=分类预算

        // 类型日期
        $periodDate = date('Y-m-d');
        if (!empty($data['period_date'])) {
            $periodDate = $data['period_date'];
        }
        switch ($data['period_type']) {
            case 'week':
                $where['period_type'] = ['=', 'week'];
                $rangeDate = getWeekStartEndTime($periodDate);
                break;
            case 'month':
                $where['period_type'] = ['=', 'month'];
                $rangeDate = getMonthStartEndTime($periodDate);
                break;
            case 'year':
                $where['period_type'] = ['=', 'year'];
                $rangeDate = getYearStartEndTime($periodDate);
                break;
            case 'quarter':
                $where['period_type'] = ['=', 'quarter'];
                $rangeDate = getQuarterStartEndTime($periodDate);
                break;
        }
        $budgetAmount = $this->modelBkBudget->getValue($where, 'amount');

        //分类预算
        $where['budget_type'] = ['=', '1'];
        $budgetCategoryList = $this->modelBkBudget->getList($where, "*", '', false)->toArray();
        foreach ($budgetCategoryList as &$row) {
            $category = $this->modelBkCategory->getInfo(['id' => $row['category_id']]);
            $row['category_name'] = $category['name'];
            $row['category_icon'] = $category['category_icon'];
            //预算使用情况
            $cateUsedAmount = $this->modelBkBudget->getTransactionAmount($bookId, $rangeDate['begin'], $rangeDate['end'], '-1', $row['category_id']);
            $rateArray = $this->modelBkBudget->calcBudgetRate($cateUsedAmount, $row['amount']);
            $row = array_merge($row, $rateArray);
        }
        $rtnData['budget_category_list'] = $budgetCategoryList;

        $expenseWhere['book_id'] = $where['book_id'];
        $expenseWhere['type'] = ['=', '-1'];
        $rtnData['book_category_list'] = $this->modelBkCategory->getList($expenseWhere, true, 'sort  asc', false);

        //总体预算使用情况
        $usedAmount = $this->modelBkBudget->getTransactionAmount($bookId, $rangeDate['begin'], $rangeDate['end'], '-1');
        $usedAmount = abs($usedAmount);

        $rateArray = $this->modelBkBudget->calcBudgetRate($usedAmount, $budgetAmount);
        $rtnData = array_merge($rtnData, $rateArray);

        return [RESULT_SUCCESS, '获取预算数据成功', '', $rtnData];
    }

    //获取预算id
    public function getBkBudgetId($data = [])
    {
        $where['book_id'] = ['=', $data['book_id']];
        $where['member_id'] = ['=', $data['member_id']];
        $where['period_type'] = ['=', $data['period_type']];
        $where['budget_type'] = ['=', $data['budget_type']];
        if (!empty($data['period_type'])) {
            $where['period_type'] = ['=', $data['period_type']];
        }
        if (!empty($data['category_id'])) {
            $where['category_id'] = ['=', $data['category_id']];
        }
        $id = $this->modelBkBudget->getValue($where, 'id');
        return $id;
    }

    /**
     * 总体预算=》添加
     * @param array $data
     * @return array
     */
    public function bkBudgetAdd($data = [])
    {
        if (empty($data['amount'])) {
            return [RESULT_ERROR, '预算金额不能为空'];
        }
        $data['member_id'] = MEMBER_ID;
        if (empty($data['book_id'])) {
            $data['book_id'] = $this->bookId;
        }
        $data['budget_type'] = 0;
        $data['start_date'] = empty($data['start_date']) ? null : $data['start_date'];
        $data['end_date'] = empty($data['end_date']) ? null : $data['end_date'];
        $budgetId = $this->getBkBudgetId($data);
        if (!empty($budgetId)) {
            $result = $this->modelBkBudget->setFieldValue(['id' => $budgetId], 'amount', $data['amount']);
        } else {
            $result = $this->modelBkBudget->setInfo($data);
        }
        $url = url('show');
        $logmsg = '新增预算' . $data['member_id'] . '，金额：' . $data['amount'];
        $result && action_log('新增', $logmsg);
        return $result ? [RESULT_SUCCESS, '' . lang('添加成功') . '', $url] : [RESULT_ERROR, $this->modelBkBudget->getError()];
    }

    //分类预算添加
    public function bkBudgetAddCategory($data = [])
    {
        if (empty($data['category_id'])) {
            return [RESULT_ERROR, '请选择预算分类'];
        }
        if (empty($data['amount'])) {
            return [RESULT_ERROR, '预算金额不能为空'];
        }
        $data['member_id'] = MEMBER_ID;
        if (empty($data['book_id'])) {
            $data['book_id'] = $this->bookId;
        }
        $data['budget_type'] = 1;
        $data['start_date'] = empty($data['start_date']) ? null : $data['start_date'];
        $data['end_date'] = empty($data['end_date']) ? null : $data['end_date'];
        $budgetId = $this->getBkBudgetId($data);
        if (!empty($budgetId)) {
            return [RESULT_ERROR, '分类预算已存在'];
        } else {
            $result = $this->modelBkBudget->setInfo($data);
        }
        $url = url('show');
        $logmsg = '新增预算' . $data['member_id'];
        $result && action_log('新增', $logmsg);
        return $result ? [RESULT_SUCCESS, '' . lang('添加成功') . '', $url] : [RESULT_ERROR, $this->modelBkBudget->getError()];
    }

    /**
     * 预算编辑
     * @param array $data
     * @return array
     */
    public function bkBudgetEdit($data = [])
    {
        $data['start_date'] = empty($data['start_date']) ? null : $data['start_date'];
        $data['end_date'] = empty($data['end_date']) ? null : $data['end_date'];
        $url = url('show');
        $result = $this->modelBkBudget->setInfo($data);
        return $result ? [RESULT_SUCCESS, '' . lang('编辑成功') . '', $url] : [RESULT_ERROR, $this->modelBkBudget->getError()];
    }

    /**
     * 预算删除
     * @param array $where
     * @return array
     */
    public function bkBudgetDel($where = [])
    {
        $result = $this->modelBkBudget->deleteInfo($where, true);
        $result && action_log('删除', '删除预算，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('删除成功') . ''] : [RESULT_ERROR, $this->modelBkBudget->getError()];
    }

    /**
     * 预算信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkBudgetInfo($data = [])
    {
        $where['member_id'] = ['=', MEMBER_ID];
        $where['book_id'] = ['=', $this->bookId];
        if (!empty($data['id'])) {
            $where['id'] = ['=', $data['id']];
        }
        if (!empty($data['period_type'])) {
            $where['period_type'] = ['=', $data['period_type']];
        }
        if (!empty($data['budget_type'])) {
            $where['budget_type'] = ['=', $data['budget_type']];
        }
        if (!empty($data['category_id'])) {
            $where['category_id'] = ['=', $data['category_id']];
        }
        return $this->modelBkBudget->getInfo($where, true);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
        $where['member_id'] = ['=', MEMBER_ID];
        //关键字查
        !empty($data['keywords']) && $where['name'] = ['like', '%' . $data['keywords'] . '%'];

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
}
