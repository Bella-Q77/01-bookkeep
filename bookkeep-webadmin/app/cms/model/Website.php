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
 * 网站配置=》模型
 */
class Website extends CmsBase
{

    //类型方式
    public static function getType($sType = '')
    {
        $arr = array('bool' => '判断', 'varchar' => '字符', 'int' => '数字', 'textarea' => '文本');
        if (!empty($sType)) {
            if (!in_array($sType[0], array_keys($arr))) {
                return $sType;
            } else {
                return $arr[$sType[0]];
            }
        }
        return $arr;
    }

    //类型方式
    public static function getGroup($sType = '')
    {
        $arr = array('1' => '网站配置', '2' => '核心设置', '3' => '附件设置', '4' => '接口设置');
//        $arr = array('1' => '网站配置');
        if (!empty($sType)) {
            if (!in_array($sType[0], array_keys($arr))) {
                return $sType;
            } else {
                return $arr[$sType[0]];
            }
        }
        return $arr;
    }

}
