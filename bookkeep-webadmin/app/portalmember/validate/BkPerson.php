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
 * 成员=》验证器
 */
class BkPerson extends PortalmemberBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require',
    ];

    // 验证提示
    protected $message = [
        'name.require' => '名称不能为空',
        'typetag.require' => '分类标识不能为空',
        'typetag.unique' => '分类标识已存在',
    ];

    // 应用场景
    protected $scene = [

        'add' => ['name'],
        'edit' => ['name'],
    ];

}
