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
 * 注册验证器
 */
class Register extends MemberBase
{
    // 验证规则
    protected $rule =   [
        'username'      => 'require|unique:member',
        'password'=>'require|min:6|confirm',
        'verify'    => 'require|captcha',
    ];

    // 验证提示
    protected $message  =   [
        'username.require'      => '用户名不能为空',
        'username.unique'       => '用户名已存在',
        'password.require' => '密码必填',
        'password.min' => '密码必须6位以上',
        'password.confirm' => '两次密码不一致',//confirm自动相互验证
    ];

    // 应用场景
    protected $scene = [
        'register'   =>  ['username','password'],
    ];

}
