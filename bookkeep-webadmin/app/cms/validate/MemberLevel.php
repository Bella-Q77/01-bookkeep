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

namespace app\cms\validate;

/**
 * 授权域名管理=》验证器
 */
class MemberLevel extends CmsBase
{

    // 验证规则
    protected $rule =   [

        'level_name'      => 'require|unique:member_level',
        'level_value'      => 'require',
        'discount'      => 'require',

    ];

    // 验证提示
    protected $message  =   [

        'level_name.require'      => '级别名称不能为空',
        'level_name.unique'      => '级别名称不能重复',
        'level_level.require'      => '会员等级值不能为空',
        'discount.require'      => '折扣率不能为空',
    ];

    // 应用场景
    protected $scene = [

        'add'       =>  ['level_name','level_level','discount'],
        'edit'       =>  ['level_name','level_level','discount'],
    ];

}
