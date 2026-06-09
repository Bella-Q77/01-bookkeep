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
 * 账本明细=》模型
 */
class BkAccount extends PortalmemberBase
{

// 账本类型
    public function account_type()
    {
        // is_bebt :0=资产  1=负债
        //资产类型：储蓄卡,现金，支付宝，微信，投资账户
        //负债类型：信用卡，贷款，其它负债
        $rtnArray = [
            1 => ['is_debt' => '0', 'name' => '现金', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            2 => ['is_debt' => '0', 'name' => '支付宝', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            3 => ['is_debt' => '0', 'name' => '微信', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            4 => ['is_debt' => '0', 'name' => '投资账户', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            5 => ['is_debt' => '0', 'name' => '银行卡', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            6 => ['is_debt' => '1', 'name' => '信用卡', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            7 => ['is_debt' => '1', 'name' => '贷款', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
            8 => ['is_debt' => '1', 'name' => '其它负债', 'icon' => 'fa fa-arrow-up', 'color' => '#00a65a',],
        ];
        return $rtnArray;
    }

    // 账本类型
    // id
    /**
     * @param $id
     * @return string
     */
    public function account_type_name($id)
    {
        $rtnArray = $this->account_type();
        if (!isset($rtnArray[$id])) {
            return '';
        }
        return $rtnArray[$id]['name'];
    }

    public function account_type_debt($id)
    {
        $rtnArray = $this->account_type();
        if (!isset($rtnArray[$id])) {
            return '';
        }
        return $rtnArray[$id]['is_debt'];
    }

    // 同步账户余额
    //
    public function syncAccountBalance($accountId)
    {
        // 交易金额
        $transactionAmount = $this->modelBkTransaction->stat(['account_id' => $accountId], 'sum', 'amount');

        // 转入金额
        $transferInAmount = $this->modelBkTransfer->stat(['to_account_id' => $accountId], 'sum', 'amount');
        //转出金额
        $transferOutAmount = $this->modelBkTransfer->stat(['from_account_id' => $accountId], 'sum', 'amount');

//        d($transactionAmount);
//        d($transferInAmount);
//        d($transferOutAmount);

        // 总余额 = 交易金额 + 转入金额 - 转出金额
        $totalAmount = $transactionAmount + $transferInAmount - $transferOutAmount;

        $this->updateInfo(['id' => $accountId], ['balance' => $totalAmount]);
    }

    public function addAccountBalance($accountId, $money)
    {
        $where['id'] = ['=', $accountId];
        $updata['balance'] = ['inc', $money];
        $this->setCalc($where, $updata);
    }

    // 账本流水统计
    // 账本ID
    // 时间范围，如果不传入时间为，账户总金额
    public function getAccountFlowStat($accId, $start_time = 0, $end_time = 0, $bookId = 0)
    {
        // 1. 正交易流入
        $where1['account_id'] = ['=', $accId];
        if (!empty($start_time) && !empty($end_time)) {
            $where1['transaction_date'] = ['between', [$start_time, $end_time]];
        }
        if (!empty($bookId)) {
            $where1['book_id'] = ['=', $bookId];
        }
        $inflowTx = Db::name('bk_transaction')
            ->where($where1)
            ->where('type', '=', '1')  //收入
            ->sum('amount');   // 正数

        // 2. 转入转账流入
        $where2['to_account_id'] = ['=', $accId];
        if (!empty($start_time) && !empty($end_time)) {
            $where2['transfer_date'] = ['between', [$start_time, $end_time]];
        }
        if (!empty($bookId)) {
            $where2['book_id'] = ['=', $bookId];
        }
        $inflowTf = Db::name('bk_transfer')
            ->where($where2)
            ->sum('amount');   // 正数

        // 3. 负交易流出
        $where3['account_id'] = ['=', $accId];
        if (!empty($start_time) && !empty($end_time)) {
            $where3['transaction_date'] = ['between', [$start_time, $end_time]];
        }
        if (!empty($bookId)) {
            $where3['book_id'] = ['=', $bookId];
        }
        $outflowTx = Db::name('bk_transaction')
            ->where($where3)
            ->where('type', '=', -1)
            ->sum('amount');   // 负数，取绝对值即可

        // 4. 转出转账流出
        $where4['from_account_id'] = ['=', $accId];
        if (!empty($start_time) && !empty($end_time)) {
            $where4['transfer_date'] = ['between', [$start_time, $end_time]];
        }
        if (!empty($bookId)) {
            $where4['book_id'] = ['=', $bookId];
        }
        $outflowTf = Db::name('bk_transfer')
            ->where($where4)
            ->sum('amount');   // 正数，取绝对值即可

        return [
            'total_inflow' => $inflowTx + $inflowTf,
            'total_outflow' => abs($outflowTx) + $outflowTf,
            'net_flow' => ($inflowTx + $inflowTf) - abs($outflowTx) - $outflowTf,
        ];
    }

}