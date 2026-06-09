<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Channelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\logic;

use \think\Db;

/**
 * 频道栏目管理=》逻辑层
 */
class Arcext extends IndexBase
{

    /**文章扩展列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return array
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getArcextList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $map['id']=$where['eid'];
        $info = $this->modelArcext->getInfo($map, $field);
        if ($info) {
            is_object($info) && $info = $info->ToArray();
            $addtable = $info['addtable'];
            $condtion['archives_id']=$where['aid'];
            $condtion['arcext_id']=['=',$info['id']];

            $ext_list_result = Db::name($addtable)
                ->alias('a')
                ->where($condtion)
                ->order($order)
                ->limit($paginate)
                ->select();
            if ($ext_list_result) {
                return array('data'=>$ext_list_result);
            } else {
                return array('data'=>'');
            }
        }
    }

    /**排序条件组合
     * @param $orderby
     * @param $orderWay
     * @param bool $isrand
     * @return string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getOrderBy($orderby, $orderWay, $isrand = false)
    {
        switch ($orderby) {
            case 'hot':
            case 'click':
                $orderby = "a.click {$orderWay}";
                break;
            case 'id':
                $orderby = "a.id {$orderWay}";
                break;

            case 'now':
            case 'new': // 兼容织梦的写法
            case 'pubdate': // 兼容织梦的写法
            case 'create_time':
                $orderby = "a.create_time {$orderWay}";
                break;

            case 'sortrank': // 兼容织梦的写法
            case 'sort':
                $orderby = "a.sort {$orderWay}";
                break;

            case 'rand':
                if (true === $isrand) {
                    $orderby = "rand()";
                } else {
                    $orderby = "a.aid {$orderWay}";
                }
                break;

            default:
            {
                $orderby = "a.pubdate  desc";
                break;
            }
        }
        return $orderby;
    }


}
