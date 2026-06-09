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
 * 栏目详细
 */
class TagType extends Base
{
    public $tid = '';
    public $info = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        /*应用于文档列表*/
        $this->tid = input('param.tid/d', 0);
    }

    /**获得栏目详细
     * @param string $typeid
     * @param string $addfields
     * @param string $jointid
     * @return array|bool
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getType($typeid = '', $addfields = '', $jointid = '')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        $jointid !== '' && $typeid = $jointid;

        if (empty($typeid)) {
            return false;
        }
        /*栏目信息*/
        $logicArctype = new \app\index\logic\Arctype();
        $where['id']=['=',$typeid];
        $result=$logicArctype->getArctypeInfo($where);
        if (empty($result)) {
            echo '标签type报错：该ID('.$typeid.')不存在。';
            return false;
        }
        $result['litpic'] = get_picture_url($result['litpic']); // 默认封面图
        //print_r($result);exit;
        return $result;
    }

    
}