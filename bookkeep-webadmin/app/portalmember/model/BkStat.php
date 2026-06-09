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

use think\Db;

/**
 * 统计=》模型
 */
class BkStat extends PortalmemberBase
{
    public function __construct()
    {
        parent::__construct();
        $this->memberId = MEMBER_ID;
        $this->bookId = session('default_book_id');
    }

    // 提取公共的交易数据查询方法
    public function getTransactionAmount($bookId, $startTime, $endTime, $type = null)
    {
        $where = [
            'book_id' => $this->bookId,
            'transaction_date' => ['between', [$startTime, $endTime]]
        ];
        if ($type !== null) {
            $where['type'] = $type;
        }
        $usedAmount = $this->modelBkTransaction->stat($where, 'sum', 'amount');
        $usedAmount = abs($usedAmount);
        return $usedAmount;
    }

    // 获取分类交易数据
    public function getCategoryTransactionData($bookId,$startTime, $endTime, $type)
    {
        $result = Db::table(SYS_DB_PREFIX . 'bk_transaction')
            ->alias('t')
            ->join(SYS_DB_PREFIX . 'bk_category c', 't.category_id = c.id')
            ->where('t.transaction_date', '>=', $startTime)
            ->where('t.transaction_date', '<=', $endTime)
            ->where('t.type', $type)
            ->where('t.book_id', $bookId)
            ->group('c.id, c.name')
            ->order('total_amount', $type == 1 ? 'DESC' : 'ASC')
            ->field('c.name as category_name, SUM(t.amount) as total_amount,COUNT(t.id) as total_count')
            ->select();
        return $result;
    }

    // 获取成员交易数据
    //成员表bk_person
    public function getPersonTransactionData($startTime, $endTime, $type)
    {
        $result = Db::table(SYS_DB_PREFIX . 'bk_transaction')
            ->alias('t')
            ->join(SYS_DB_PREFIX . 'bk_person p', 't.person_id = p.id')
            ->where('t.transaction_date', '>=', $startTime)
            ->where('t.transaction_date', '<=', $endTime)
            ->where('t.type', $type)
            ->where('t.book_id', $this->bookId)
            ->group('p.id, p.name')
            ->order('total_amount', $type == 1 ? 'DESC' : 'ASC')
            ->field('p.name as person_name,p.name as category_name, SUM(t.amount) as total_amount,COUNT(t.id) as total_count')
            ->select();
        return $result;
    }
    // 获取项目交易数据
    //项目表bk_project
    public function getProjectTransactionData($startTime, $endTime, $type)
    {
        $result = Db::table(SYS_DB_PREFIX . 'bk_transaction')
            ->alias('t')
            ->join(SYS_DB_PREFIX . 'bk_project p', 't.project_id = p.id')
            ->where('t.transaction_date', '>=', $startTime)
            ->where('t.transaction_date', '<=', $endTime)
            ->where('t.type', $type)
            ->where('t.book_id', $this->bookId)
            ->group('p.id, p.name')
            ->order('total_amount', $type == 1 ? 'DESC' : 'ASC')
            ->field('p.name as project_name,p.name as category_name, SUM(t.amount) as total_amount,COUNT(t.id) as total_count')
            ->select();
        return $result;
    }
    // 获取商家交易数据
    //商家表bk_shop
    public function getShopTransactionData($startTime, $endTime, $type)
    {
        $result = Db::table(SYS_DB_PREFIX . 'bk_transaction')
            ->alias('t')
            ->join(SYS_DB_PREFIX . 'bk_shop p', 't.shop_id = p.id')
            ->where('t.transaction_date', '>=', $startTime)
            ->where('t.transaction_date', '<=', $endTime)
            ->where('t.type', $type)
            ->where('t.book_id', $this->bookId)
            ->group('p.id, p.name')
            ->order('total_amount', $type == 1 ? 'DESC' : 'ASC')
            ->field('p.name as shop_name,p.name as category_name, SUM(t.amount) as total_amount,COUNT(t.id) as total_count')
            ->select();
        return $result;
    }

    // 获取趋势数据
    public function getTrendSeriesData($bookId, $statField, $start_time, $end_time)
    {
        $sys_db_prefix = SYS_DB_PREFIX;

        $where2create = " AND book_id = '" . $bookId . "'";
        // 收入数据
        $incomeSql = "
        SELECT {$statField}, SUM(amount) as total_amount 
        FROM {$sys_db_prefix}bk_transaction
        WHERE transaction_date >= '$start_time' AND transaction_date <= '$end_time' AND type = '1' {$where2create}
        GROUP BY xdate
        ORDER BY xdate ASC
    ";
        $incomeList = Db::query($incomeSql);
        $incomeData = array_column($incomeList, 'total_amount', 'xdate');

        // 支出数据
        $expenseSql = "
        SELECT {$statField}, SUM(amount) as total_amount 
        FROM {$sys_db_prefix}bk_transaction
        WHERE transaction_date >= '$start_time' AND transaction_date <= '$end_time' AND type = '-1' {$where2create}
        GROUP BY xdate
        ORDER BY xdate ASC
    ";
        $expenseList = Db::query($expenseSql);
        $expenseData = array_column($expenseList, 'total_amount', 'xdate');

        // 结余数据
        $balanceSql = "
        SELECT {$statField}, SUM(amount) as total_amount 
        FROM {$sys_db_prefix}bk_transaction
        WHERE transaction_date >= '$start_time' AND transaction_date <= '$end_time' {$where2create}
        GROUP BY xdate
        ORDER BY xdate ASC
    ";
        $balanceList = Db::query($balanceSql);
        $balanceData = array_column($balanceList, 'total_amount', 'xdate');
        return [
            'income' => $incomeData,
            'expense' => $expenseData,
            'balance' => $balanceData
        ];
    }
}