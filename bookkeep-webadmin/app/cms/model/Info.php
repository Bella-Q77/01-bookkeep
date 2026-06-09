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
namespace app\cms\model;

/**
 * 信息管理=》模型
 */
class Info extends CmsBase
{
    /**状态状态
     * @param string $sType
     * @return array|mixed
     * Author: lingqifei created by at 2020/4/15 0015
     */
    public  function status($key = '')
    {
        $data = array(
            "0" => array(
                'name' => '待审核',
                'html' => '<span class="label label-warning">待审核<span>',
                'action' => array(
                ),
            ),
            "1" => array(
                'name' => '已审核',
                'html' => '<span class="label label-success">已审核<span>',
                'action' => array(
                ),
            ),
            "2" => array(
                'name' => '已拒绝',
                'html' => '<span class="label label-danger">已拒绝<span>',
                'action' => array(
                ),
            ),
        );
        return (array_key_exists($key,$data))?$data[$key]:$data;
    }

    /**
     * 推广状态
     *0=未推广，1=待支付，2=推广中
     * @param string $sType
     * @return array|mixed
     * Author: lingqifei created by at 2020/4/15 0015
     */
    public  function istop($key = '')
    {
        $data = array(
            "0" => array(
                'name' => '未推广',
                'html' => '<span class="label label-default">未推广</span>',
                'action' => array(
                ),
            ),
            "1" => array(
                'name' => '待支付',
                'html' => '<span class="label label-warning">待支付</span>',
                'action' => array(
                ),
            ),
            "2" => array(
                'name' => '推广中',
                'html' => '<span class="label label-success">推广中</span>',
                'action' => array(
                ),
            ),
        );
        return (array_key_exists($key,$data))?$data[$key]:$data;
    }

}
