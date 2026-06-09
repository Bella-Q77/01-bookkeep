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
 * 预算=》验证器
 */
class BkBudget extends PortalmemberBase
{
    // 验证规则
    protected $rule = [
        'amount' => 'require',
        'start_date' => 'require',
        'end_date' => 'require',
    ];

    // 验证提示
    protected $message = [
        'amount.require' => '金额不能为空',
        'start_date.require' => '开始日期不能为空',
        'end_date.require' => '结束日期不能为空',
    ];

    // 应用场景
    protected $scene = [

        'add' => ['amount', 'start_date', 'end_date'],
        'edit' => ['amount', 'start_date', 'end_date'],
    ];

}
