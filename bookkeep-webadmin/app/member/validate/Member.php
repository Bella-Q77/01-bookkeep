<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Author: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\validate;

/**
 * 会员管理=》验证器
 */
class Member extends MemberBase
{

    // 验证规则
    protected $rule =   [

        'username'      => 'require|unique:member',
        'password'      => 'require',
        'level_id'      => 'require',

    ];

    // 验证提示
    protected $message  =   [

        'username.require'      => '帐号不能为空',
        'username.unique'      => '帐号不能为重复',
        'password.require'      => '密码不能为空',
        'level_id.require'      => '选择会员等级',
    ];

    // 应用场景
    protected $scene = [

        'add'       =>  ['username','password','level_id'],
        'edit'       =>  ['username'],
        'edit_pwd'       =>  ['username','password'],
        'fron_edit'       =>  ['username','level_id'],
    ];

}
