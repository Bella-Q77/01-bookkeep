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
 * 收支记录=》验证器
 */
class BkTransaction extends PortalmemberBase
{

    // 验证规则
    protected $rule = [
        'type' => 'require',
        'amount' => 'require',
        'category_id' => 'require',
        'account_id' => 'require',
        'transaction_date' => 'require',
    ];

    // 验证提示
    protected $message = [
        'type.require' => '收支类型不能为空',
        'amount.require' => '金额不能为空',
        'category_id.require' => '收支类别不能为空',
        'account_id.require' => '账户不能为空',
        'transaction_date.require' => '收支日期不能为空',
    ];

    // 应用场景
    protected $scene = [

        'add' => ['type', 'amount', 'category_id', 'transaction_date'],
        'edit' => ['type', 'amount', 'category_id', 'transaction_date'],
    ];

}
