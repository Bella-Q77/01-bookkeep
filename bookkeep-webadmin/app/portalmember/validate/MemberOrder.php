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
 * 会员订单积分=》验证器
 */
class MemberOrder extends PortalmemberBase
{

    // 验证规则
    protected $rule =   [

        'member_id'      => 'require',
        'order_amount'      => 'require',
        'bus_id'      => 'require',
        'bus_type'      => 'require',

    ];

    // 验证提示
    protected $message  =   [
        'member_id.require'      => '购买会员编号不能为空',
        'order_amount.require'      => '购买金额不能为空',
        'bus_id.require'      => '购买业务编号不能为空',
        'bus_type.require'      => '购买业务类型不能为空',
    ];

    // 应用场景
    protected $scene = [
        'add'       =>  ['member_id','order_amount','bus_id','bus_type'],
    ];

}
