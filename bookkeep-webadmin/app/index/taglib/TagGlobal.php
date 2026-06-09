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

namespace app\index\taglib;

use think\Db;
use think\Request;


/**
 * 栏目列表
 */
class TagGlobal extends Base
{

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }


    /**获得全局变量函数
     * @param $name
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getGlobal($name)
    {
        if (empty($name)) {
            return '标签global报错：缺少属性 name 。';
        }
        $param = explode('|', $name);
        $name = trim($param[0], '$');

        /*获取配置信息*/
        //$value = config($name);
        $logicWebsite = new \app\index\logic\Website();
        $value = $logicWebsite->getWebsiteConfig($name);

        //循环添加函数
        foreach ($param as $key => $val) {
            if ($key == 0) continue;
            $value = $val($value);
        }
        $value = htmlspecialchars_decode($value);
        return $value;

    }
}