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
 * 会员图片管理=》验证器
 */
class MemberPicture extends MemberBase
{

    // 验证规则
    protected $rule =   [

        'id'      => 'require',
        'status'      => 'require',
    ];

    // 验证提示
    protected $message  =   [

        'id.require'      => 'id不能为空',
        'status.unique'      => '操作值状态不能为空',
    ];

    // 应用场景
    protected $scene = [

        'add'       =>  ['level_name','level_level','discount'],
        'edit'       =>  ['level_name','level_level','discount'],
        'audit'       =>  ['id','status'],
    ];

}
