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
 * 广告位列表
 */
class TagAds extends Base
{

    //初始化
    protected function _initialize()
    {
        parent::_initialize();

    }


    /**ads解析函数
     * @param string $aid
     * Author: lingqifei created by at 2020/2/28 0028
     */
    public function getAds($aid='')
    {
        $where['visible']=['=',1];
        if($aid){
            $where['id']=['in',$aid];
        }
        /*获取文档列表*/
        $logicAds = new \app\index\logic\Ads();
        $result = $logicAds->getAdsList($where, true, '',false);
        if($result['data']){
            return $result['data'];
        }
        return ;
    }
}