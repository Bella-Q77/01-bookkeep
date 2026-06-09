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
class TagSearchurl extends Base
{

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }


    /**获取搜索表单
     * @return string
     * Author: lingqifei created by at 2020/3/5 0005
     */
    public function getSearchurl()
    {
        $url = url("index/Search/index");
        return $url;
    }
}