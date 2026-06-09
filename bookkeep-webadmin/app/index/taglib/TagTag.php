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

use app\index\logic\IndexBase;
use think\Db;
use think\Request;


/**
 * 标签调用
 */
class TagTag extends IndexBase
{
    public $aid = '';
    //初始化
    protected function _initialize()
    {
        parent::_initialize();

        $this->aid = input('param.aid/d', 0);
    }


    /**
     * 获取标签
     * @param int $getall
     * @param string $typeid
     * @param int $aid
     * @param int $row
     * @param string $sort
     * @param string $type
     * @return bool|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * Author: lingqifei created by at 2020/3/5 0005
     */
    public function getTag($getall = 0, $typeid = '', $aid = 0, $row = 30, $sort = 'new', $type = '')
    {

        //表示查询当前ID文档标签
		$this->aid = input('param.aid/d', 0);

        $aid = !empty($aid) ? $aid : $this->aid;

        $getall = intval($getall);

        !empty($typeid) && $getall = 1;

        $result = [];
        $where = array();
        //带aid调用文档标签
        if ($getall == 0 && $aid > 0) {
            $where['aid'] = array('eq', $aid);
            $list = $this->modelTaglist->getList($where, "*, tid AS tagid", true, $row);

        } else {//不带aid就调用所有共性标签

            //限制typeid
            if (!empty($typeid)) {
                $typeid = $this->getTypeids($typeid, $type);
                $where['typeid'] = array('in', $typeid);
            }

            //排序处理
            switch ($sort) {
                case 'rand':
                    $rand_ids=$this->modelTagindex->getColumn('','id');
                    $rand_cnt=count($rand_ids);
                    $number=(count($rand_ids)>15)?'15':$rand_cnt;
                    $radn_id=array_rand_value($rand_ids,$number);
                    $where['id'] = array('in', $radn_id);
                    $orderby = 'create_time DESC';
                    break;
                case 'week':
                    $orderby = 'weekcc desc ';
                    break;
                case 'month':
                    $orderby = 'monthcc desc ';
                    break;
                case 'hot':
                    $orderby = 'count desc ';
                    break;
                case 'total':
                    $orderby = 'total desc ';
                    break;
                default:
                    $orderby = 'create_time desc ';
                    break;
            }
            $list = $this->modelTagindex->getList($where, "*, id AS tagid", $orderby, $row);
        }

        is_object($list) && $list=$list->toArray();

        if(!empty($list['data'])) $result=$list['data'];

        foreach ($result as $key => $val) {
            $val['link'] = url('index/tags/lists', array('tagid'=>$val['tagid']));
            $result[$key] = $val;
        }
        return $result;
    }

    /**
     * 栏目 typeid 处理理
     * @param $typeid
     * @param string $type
     * @return array
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/3 0003
     */
    private function getTypeids($typeid, $type = '')
    {
        $typeidArr = $typeid;
        if (!is_array($typeidArr)) {
            $typeidArr = explode(',', $typeid);
        }
        $typeids = [];

        $logicArctype = new \app\index\logic\Arctype();

        foreach($typeidArr as $key => $tid) {
            $result = [];
            switch ($type) {
                case 'son': // 下级栏目
                    $result = $logicArctype->getArctypeAllSon($tid);
                    break;

                case 'self': // 同级栏目
                    $result =  $logicArctype->getArctypeAllSelf($tid);
                    break;

                case 'top': // 顶级栏目
                    $result =  $logicArctype->getArctypeAllTop($tid);
                    break;

                default:
                    $result = [
                        [
                            'id'    => $tid,
                        ]
                    ];
                    break;
            }
            if (!empty($result)) {
                $typeids = array_merge($typeids, get_arr_column($result, 'id'));
            }
        }

        return $typeids;
    }



}