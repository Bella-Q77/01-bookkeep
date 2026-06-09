<?php
// +----------------------------------------------------------------------
// | 07FLYCRM [基于ThinkPHP5.0开发]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2021 http://www.07fly.xyz
// +----------------------------------------------------------------------
// | Professional because of focus  Persevering because of happiness
// +----------------------------------------------------------------------
// | Author: 开发人生 <goodkfrs@qq.com>
// +----------------------------------------------------------------------
namespace app\portalmember\model;

/**
 * 预算明细=》模型
 */
class BkBudget extends PortalmemberBase
{

    // 获取预算使用率
    //return array budget_amount,budget_used_amount,budget_used_rate,budget_remain_amount
    public function calcBudgetRate($usedAmount, $budgetAmount)
    {
        $usedAmount = abs($usedAmount);
        $rtnData['budget_amount'] = empty($budgetAmount) ? 0 : $budgetAmount;
        $rtnData['budget_used_amount'] = empty($usedAmount) ? 0 : $usedAmount;
        $rtnData['budget_used_rate'] = empty($budgetAmount) ? 0 : round($usedAmount / $budgetAmount * 100, 2);
        $rtnData['budget_remain_amount'] = round(($budgetAmount - $usedAmount), 2);
        return $rtnData;
    }

    //获取预算使用金额
    public function getTransactionAmount($bookId, $startTime, $endTime, $type = null, $categoryId = 0)
    {
        $transWhere['member_id'] = ['=', MEMBER_ID];
        $transWhere['book_id'] = ['=', $bookId];
        $transWhere['transaction_date'] = ['between', [$startTime, $endTime]];
        if (!empty($type)) {
            $transWhere['type'] = ['=', $type];
        }
        if (!empty($categoryId)) {
            $transWhere['category_id'] = ['=', $categoryId];
        }
        $usedAmount = $this->modelBkTransaction->stat($transWhere, 'sum', 'amount');
        $usedAmount = abs($usedAmount);
        return $usedAmount;
    }

    //获取预算金额
    public function getBudgetAmount($bookId, $periodType, $categoryId = 0)
    {
        $where['book_id'] = ['=', $bookId];
        $where['budget_type'] = ['=', '0'];//分类预算,默认为总预算
        if (!empty($categoryId)) {
            $where['category_id'] = ['=', $categoryId];
            $where['budget_type'] = ['=', '1'];
        }
        switch ($periodType) {
            case 'week':
                $where['period_type'] = ['=', 'week'];
                break;
            case 'month':
                $where['period_type'] = ['=', 'month'];
                break;
            case 'year':
                $where['period_type'] = ['=', 'year'];
                break;
            case 'quarter':
                $where['period_type'] = ['=', 'quarter'];
                break;
            default:
                $where['period_type'] = ['=', 'month'];
                break;
        }
        $amount = $this->getValue($where, 'amount');
        return $amount;
    }
}
