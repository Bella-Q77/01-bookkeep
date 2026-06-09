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
 * 转账记录明细=》模型
 */
class BkTransfer extends PortalmemberBase
{

    public function addTransferBalance($fromAccountId, $toAccountId, $amount)
    {
        $fromAmount = $amount * (-1);
        $toAmount = $amount * (1);
        $this->modelBkAccount->addAccountBalance($fromAccountId, $fromAmount);
        $this->modelBkAccount->addAccountBalance($toAccountId, $toAmount);
    }

    public function editTransferBalance($transferId, $newAmount)
    {
        $info = $this->getInfo(['id' => $transferId], 'amount,from_account_id,to_account_id');
        $uptAmount = round(($newAmount - $info['amount']), 2);
        $this->modelBkAccount->addAccountBalance($info['from_account_id'], $uptAmount * (-1));
        $this->modelBkAccount->addAccountBalance($info['to_account_id'], $uptAmount);
    }

    public function delTransferBalance($transferId)
    {
        $info = $this->getInfo(['id' => $transferId], 'amount,from_account_id,to_account_id');
        $amount = round(($info['amount']), 2);

        $fromAmount = $amount * (1);
        $toAmount = $amount * (-1);
        $this->modelBkAccount->addAccountBalance($info['from_account_id'], $fromAmount);
        $this->modelBkAccount->addAccountBalance($info['to_account_id'], $toAmount);
    }
}
