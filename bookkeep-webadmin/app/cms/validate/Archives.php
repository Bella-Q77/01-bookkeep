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
 * 内容管理验证器
 */
class Archives extends CmsBase
{

    // 验证规则
    protected $rule =   [
        'title'      => 'require',
        'body'      => 'require',
        'type_id'      => 'require|gt:0',
    ];

    // 验证提示
    protected $message  =   [
        'title.require'      => '标题不能为空',
        'body.require'      => '内容不能为空',
        'type_id.require'      => '主栏目不能为空',
        'type_id.gt'      => '选择主栏目',
    ];

    // 应用场景
    protected $scene = [

        'add'       =>  ['title','body','type_id'],
        'edit'       =>  ['title','body','type_id'],
    ];
    
}
