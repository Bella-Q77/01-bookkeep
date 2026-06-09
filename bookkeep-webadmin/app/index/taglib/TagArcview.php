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
 * 文章详细
 */
class TagArcview extends Base
{
    public $tid = '';
    public $info = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        /*应用于文档列表*/
        $this->aid = input('param.aid/d', 0);
    }

    /**获得文章详细
     * @param string $aid
     * @param string $addfields
     * @param string $joinaid
     * @return array|bool
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getArcview($aid = '', $addfields = '', $joinaid = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        $joinaid !== '' && $aid = $joinaid;

        if (empty($aid)) {
            return false;
        }

        /*文档信息*/
        $logicArchives = new \app\index\logic\Archives();
        $where['id']=['=',$aid];
        $result=$logicArchives->getArchivesInfo($where);
        if (empty($result)) {
            echo '标签arcview报错：该文档ID('.$aid.')不存在。';
            return false;
        }
        //$result['litpic'] = get_picture_url($result['litpic']); // 默认封面图

        /*栏目信息*/
        $logicArctype = new \app\index\logic\Arctype();
        $typeinfo=$logicArctype->getArctypeInfo(['id'=>$result['type_id']]);
        if($typeinfo){
            $result['typename']=$typeinfo['typename'];
            $result['typeurl']=$typeinfo['typeurl'];
        }

        //print_r($result);exit;
        return $result;
    }

    
}