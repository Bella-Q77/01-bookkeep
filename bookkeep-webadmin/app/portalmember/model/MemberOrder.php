<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2021 07FLY Network Technology Co,LTD.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/
namespace app\portalmember\model;


/**
 * 模块基类
 */
class MemberOrder extends PortalmemberBase
{

    /**支付状态状态
     * @param string $sType
     * @return array|mixed
     * Author: lingqifei created by at 2020/4/15 0015
     */
    public  function payment_status($key = '')
    {
        $data = array(
            "0" => array(
                'name' => '未支付',
                'html' => '<span class="label label-warning yellow">未支付<span>',
                'action' => array(
                ),
            ),
            "1" => array(
                'name' => '已支付',
                'html' => '<span class="label label-success blue">已支付<span>',
                'action' => array(
                ),
            ),
        );
        return (array_key_exists($key,$data))?$data[$key]:$data;
    }

    /**订单类型状态状态
     * @param string $sType
     * @return array|mixed
     * Author: lingqifei created by at 2020/4/15 0015
     */
    public  function bus_type($key = '')
    {
        $data = array(
            "integral" => array(
                'id' => 'integral',
                'name' => '积分充值',
                'html' => '<span class="label label-warning">积分充值<span>',
                'action' => array(
                ),
            ),
            "level" => array(
                'id' => 'level',
                'name' => '会员升级',
                'html' => '<span class="label label-success">会员升级<span>',
                'action' => array(
                ),
            ),
            "infotop" => array(
                'infotop' => 'infotop',
                'name' => '信息置顶',
                'html' => '<span class="label label-success">信息置顶<span>',
                'action' => array(
                ),
            ),
            "member_ad" => array(
                'infotop' => 'infotop',
                'name' => '自助广告',
                'html' => '<span class="label label-success">自助广告<span>',
                'action' => array(
                ),
            ),
        );
        return (array_key_exists($key,$data))?$data[$key]:$data;
    }
}
?>