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
class MemberAdv extends MemberBase
{
    /**广告类型 广告类型0=图片，1=文本，2=html
     * @param string $sType
     * @return array|mixed
     * Author: lingqifei created by at 2020/4/15 0015
     */
    public  function ad_type($key = '')
    {
        $data = array(
            "0" => array(
                'id' => '0',
                'name' => '图片',
                'html' => '<span class="label label-warning">图片<span>',
                'action' => array(
                ),
            ),
            "1" => array(
                'id' => '1',
                'name' => '文本',
                'html' => '<span class="label label-success blue">文本<span>',
                'action' => array(
                ),
            ),
            "2" => array(
                'id' => '2',
                'name' => 'html',
                'html' => '<span class="label label-info blue">html<span>',
                'action' => array(
                ),
            ),
        );
        return (array_key_exists($key,$data))?$data[$key]:$data;
    }
}
?>