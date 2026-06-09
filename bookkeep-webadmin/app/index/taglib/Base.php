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

use think\Config;
use think\Cookie;
use think\Db;

/**
 * 基类
 */
class Base
{
    /**
     * 主体语言（语言列表中最早一条）
     */
    public $main_lang = 'cn';

    /**
     * 前台当前语言
     */
    public $home_lang = 'cn';

    /**
     * 子目录
     */
    public $root_dir = '';

    //构造函数
    function __construct()
    {
        // 控制器初始化
        $this->_initialize();
    }

    // 初始化
    protected function _initialize()
    {
        // 子目录安装路径
        $this->root_dir = ROOT_PATH;
    }

    /**
     * 在typeid传值为目录名称的情况下，获取栏目ID
     */
    public function getTrueTypeid($typeid = '')
    {
        /*tid为目录名称的情况下*/
        if (!empty($typeid) && strval($typeid) != strval(intval($typeid))) {
            $typeid = Db::name('arctype')
				->where(['typedir'   => $typeid])
				->cache(true,CACHE_TIME,"arctype")
                ->value('id');
        }
        /*--end*/

        return $typeid;
    }
}