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
namespace app\member\model;


/**
 * 模块基类
 */
class MemberAdvDis extends MemberBase
{
    /**状态状态
     * @param string $sType
     * @return array|mixed
     * Author: lingqifei created by at 2020/4/15 0015
     */
    public function status($key = '')
    {
        $data = array(
            "0" => array(
                'name' => '待付款',
                'html' => '<span class="label label-warning yellow">待付款<span>',
                'action' => array(),
            ),
            "1" => array(
                'name' => '待审核',
                'html' => '<span class="label label-success blue">待审核<span>',
                'action' => array(),
            ),
            "2" => array(
                'name' => '待上架',
                'html' => '<span class="label label-info blue">待上架<span>',
                'action' => array(),
            ),
            "3" => array(
                'name' => '展示中',
                'html' => '<span class="label label-info blue">展示中<span>',
                'action' => array(),
            ),
            "4" => array(
                'name' => '已到期',
                'html' => '<span class="label label-default">已到期<span>',
                'action' => array(),
            ),
            "5" => array(
                'name' => '已拒绝',
                'html' => '<span class="label label-danger red">已拒绝<span>',
                'action' => array(),
            ),
        );
        return (array_key_exists($key, $data)) ? $data[$key] : $data;
    }
}
?>