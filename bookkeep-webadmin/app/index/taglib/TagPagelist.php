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
 * 分页函数
 */
class TagPagelist extends Base
{

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**获取列表分页
     * @param string $pages
     * @param string $listitem
     * @param string $listsize
     * @return bool
     * Author: lingqifei created by at 2020/2/28 0028
     */
    public function getPagelist($pages = '', $listitem = '', $listsize = '')
    {
        if (empty($pages)) {
            echo '标签pagelist报错：只适用在标签list之后。';
            return false;
        }
        $listitem = !empty($listitem) ? $listitem : 'info,index,end,pre,next,pageno';
        $listsize = !empty($listsize) ? $listsize : '3';
        $value = $pages->render($listitem, $listsize);
        return $value;
    }
}