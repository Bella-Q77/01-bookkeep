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

namespace app\portalmember\validate;

/**
 * 会员实名升级理=》验证器
 */
class MemberRealname extends PortalmemberBase
{

    // 验证规则
    protected $rule =   [

        'name'      => 'require',
        'cert_code'      => 'require',
        'cert_pic'      => 'require',
        'period'      => 'require',

    ];

    // 验证提示
    protected $message  =   [

        'name.require'      => '真实名称不能为空',
        'cert_code.require'      => '证件号不能为空',
        'cert_pic.require'      => '认证材料不能为空',
        'period.require'      => '周期不能为空',
    ];

    // 应用场景
    protected $scene = [
        'edit'       =>  ['name','cert_code','cert_pic'],
    ];

}
