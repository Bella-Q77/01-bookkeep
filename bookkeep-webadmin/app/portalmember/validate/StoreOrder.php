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
 * 会员图片管理=》验证器
 */
class Info extends PortalmemberBase
{

    // 验证规则
    protected $rule =   [

        'type_id'      => 'require',
        'type_id2'      => 'require',
        'title'      => 'require',
        'desctription'      => 'require',
        'member_id'      => 'gt:0',
        'province_id'      => 'gt:0',
        'city_id'      => 'gt:0',
        'county_id'      => 'gt:0',
    ];

    // 验证提示
    protected $message  =   [

        'type_id.require'      => '请选择分类',
        'type_id2.require'      => '请选择子类别',
        'title.require'      => '标题不能为空',
        'description.require'      => '简介不能为空',
        'content.require'      => '内容不能为空',
        'member_id.gt'      => '发布的会员不能为空',
        'province_id.gt'      => '请先完善宣传资料,设置机构所在省市区',
        'city_id.gt'      => '请先完善宣传资料,设置机构所在省市区',
        'county_id.gt'      => '请先完善宣传资料,设置机构所在省市区',
    ];

    // 应用场景
    protected $scene = [
        'add'       =>  ['type_id','type_id2','title','description','content','member_id','province_id','city_id','county_id'],
        'edit'       =>  ['type_id','type_id2','title','description','content','member_id','province_id','city_id','county_id'],

    ];

}
