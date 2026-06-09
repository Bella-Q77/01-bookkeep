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

namespace app\portalmember\validate;


/**
 * 转账记录=》验证器
 */
class BkTransfer extends PortalmemberBase
{
    // 验证规则
    protected $rule = [
        'amount' => 'require',
        'from_account_id' => 'require|different:to_account_id',
        'to_account_id' => 'require',
        'transfer_date' => 'require',
    ];

    // 验证提示
    protected $message = [
        'amount.require' => '金额不能为空',
        'transfer_date.require' => '日期不能为空',
        'from_account_id.require' => '转出账户不能为空',
        'to_account_id.require' => '转入账户不能为空',
        'from_account_id.different' => '转出账户和转入账户不能相同',
    ];

    // 应用场景
    protected $scene = [
        'add' => ['amount', 'from_account_id', 'to_account_id', 'transfer_date'],
        'edit' => ['amount', 'from_account_id', 'to_account_id', 'transfer_date'],
    ];

}
