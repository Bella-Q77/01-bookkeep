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
 * 模型验证器
 */
class GuestbookField extends CmsBase
{

    // 验证规则
    protected $rule =   [

        'field_name'      => 'require',
        'nid'      => 'require|unique:guestbook',

    ];

    // 验证提示
    protected $message  =   [

        'field_name.require'      => '字段名称不能为空',
        'show_name.require'      => '表单提示文字不能为空',
    ];

    // 应用场景
    protected $scene = [

        'add'       =>  ['field_name','show_name'],
        'edit'       =>  ['field_name','show_name'],
    ];
    
}
