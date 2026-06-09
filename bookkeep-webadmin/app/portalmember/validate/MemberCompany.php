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
 * 授权域名管理=》验证器
 */
class MemberCompany extends PortalmemberBase
{

    // 验证规则
    protected $rule =   [

        'name'      => 'require',
        'category_id'      => 'require',
        'province_id'      => 'require',
        'city_id'      => 'require',
        'county_id'      => 'require',
        'tel'      => 'require',

    ];

    // 验证提示
    protected $message  =   [

        'name.require'      => '宣传名称不能为空',
        'category_id.require'      => '选择招生分类',
        'province_id.require'      => '选择所在省',
        'city_id.require'      => '选择所在市',
        'county_id.require'      => '选择所在区',
        'tel.require'      => '联系电话不能为空',
    ];

    // 应用场景
    protected $scene = [

        'add'       =>  ['level_name','level_level','discount'],
        'company_edit'       =>  ['name','category_id','province_id','city_id','county_id','tel'],
    ];

}
