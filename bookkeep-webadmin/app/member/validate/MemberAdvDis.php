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
 * 会员广告管理=》验证器
 */
class MemberAdvDis extends MemberBase
{

    // 验证规则
    protected $rule =   [

        'title'      => 'require',
        'litpic'      => 'require',
        'links'      => 'require',

    ];

    // 验证提示
    protected $message  =   [

        'name.require'      => '名称不能为空',
        'litpic.unique'      => '图片不能为空',
        'links.unique'      => '链接地址不能为空',
    ];

    // 应用场景
    protected $scene = [
        'add'       =>  ['name','litpic','links'],
        'edit'       =>  ['name','litpic','links'],
    ];

}
