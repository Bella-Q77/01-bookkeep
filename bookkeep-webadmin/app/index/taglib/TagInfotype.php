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
class TagInfotype extends Base
{
    public $tid = '';
    public $currentstyle = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->tid = input("param.tid/s", ''); // 应用于栏目列表
        /*应用于文档列表*/
        $aid = Input('param.aid/d', 0);
        if ($aid > 0) {
            $cacheKey = 'tagChannel_'.strtolower('index_'.CONTROLLER_NAME.'_'.ACTION_NAME);
            $cacheKey .= "_{$aid}";
            $this->tid = cache($cacheKey);
            if ($this->tid == false) {
                /*文档信息*/
                $logicInfo = new \app\index\logic\Info();
                $map['id']=['=',$aid];
                $result=$logicInfo->getInfoInfo($map);
                $this->tid = $result['type_id'];
                cache($cacheKey, $this->tid);
            }
        }
        /*--end*/
        /*tid为目录名称的情况下*/
        $this->tid = $this->getTrueInfoTypeid($this->tid);
        /*--end*/
    }

    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     * @author 07fly by 2018-4-26
     */
    public  function getInfotype($typeid = '', $type = 'top', $currentstyle = '', $notypeid = '')
    {
        $this->currentstyle = $currentstyle;
        $result_array=[];
        if (!empty($typeid)) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $typeid)) {
                echo '标签Infotype报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            // 过滤typeid中含有空值的栏目ID
            $typeidArr_tmp = explode(',', $typeid);
            foreach ($typeidArr_tmp as $tid ){
                $result = $this->getSwitchType($tid, $type, $notypeid);
                $result_array = array_merge($result_array,$result);
            }
        }else{
            $result_array = $this->getSwitchType($typeid, $type, $notypeid);
        }
        return $result_array;
    }

    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     * @author 07fly by 2018-4-26
     */
    public function getSwitchType($typeid = '', $type = 'top', $notypeid = '')
    {
        $result = array();
        switch ($type) {
            case 'son': // 下级栏目
                $typeid  = !empty($typeid) ? $typeid : $this->tid;
                $result = $this->getSon($typeid, true);
                break;
            case 'self': // 同级栏目
                $typeid  = !empty($typeid) ? $typeid : $this->tid;
                $result = $this->getSelf($typeid);
                break;

            case 'top': // 顶级栏目
                $result = $this->getTop($typeid,$notypeid);
                break;

            case 'sonself': // 下级、同级栏目
                $typeid  = !empty($typeid) ? $typeid : $this->tid;
                $result = $this->getSon($typeid, true);
                break;

            case 'first': // 第一级栏目
                $typeid  = !empty($typeid) ? $typeid : $this->tid;
                $result = $this->getFirst($typeid);
                break;
        }
        return $result;
    }


    /**
     * @param $typeid
     * @param bool $self
     * @return array|string
     * Author: lingqifei created by at 2020/2/24 0024
     */
    public function getSon($typeid, $self = false)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        /*获取所有栏目*/
        $logicInfoType = new \app\index\logic\InfoType();

        $typeidSon=$logicInfoType->getInfoTypeAllSon($typeid);//所有下级id

        $map['visible']=['=','1'];//显示
        $map['id']=['in',$typeidSon];//查询下级栏目

        $list = $logicInfoType->getInfoTypeList($map, true, 'sort asc',false);
        /*--end*/

        //数据整理
        if (count($list['data']) > 0) {
            foreach ($list['data'] as $key => $val) {
                //处理栏目标识
                $topTypeid=$this->getTopTypeid($this->tid);
                if (in_array($val['id'],$topTypeid) || $val['id']==$this->tid) {
                    $val['currentstyle'] = $this->currentstyle;
                }else{
                    $val['currentstyle'] ='';
                }
                $result[$key] = $val;
            }
        }
        $result= list2tree($result,$typeid,0,'id','parent_id','typename');//把所以树形展示



        /*--end*/
        /*没有子栏目时，获取同级栏目*/
        if (empty($result) && $self == true) {
            $result = $this->getSelf($typeid);
        }
        /*--end*/
        return $result;
    }

    /**
     * 获取同级栏目
     * @param $typeid
     * @return array
     * @author 07fly by 2020-02-20
     */
    private function getSelf($typeid)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        /*获取所有栏目*/
        $logicInfoType = new \app\index\logic\InfoType();
        $parent_id=$logicInfoType->getInfoTypeSelf($typeid);//所有下级id
        $map['visible']=['=','1'];//显示
        $map['parent_id']=['in',$parent_id];//查询下级栏目
        $list = $logicInfoType->getInfoTypeList($map, true, 'sort asc',false);
        /*--end*/

        //数据整理
        if (count($list['data']) > 0) {
            foreach ($list['data'] as $key => $val) {
                //处理栏目标识
                $topTypeid=$this->getTopTypeid($this->tid);
                if (in_array($val['id'],$topTypeid) || $val['id']==$this->tid) {
                    $val['currentstyle'] = $this->currentstyle;
                }else{
                    $val['currentstyle'] ='';
                }
                $result[$key] = $val;
            }
        }
        /*--end*/
        return $result;
    }

    /**
     * 获取顶级栏目
     * @param string $notypeid
     * @return array
     * @author 07fly by 2020-02-24
     */
    private function getTop($typeid='',$notypeid = '')
    {
        $result = array();
        /*获取所有栏目*/
        $logicInfoType = new \app\index\logic\InfoType();
        $map['visible']=['=','1'];//显示
        //$map['parent_id']=['=','0'];//只显示顶级栏目
        !empty($typeid) && $map['id'] = ['In', $typeid]; // 指定ID
        !empty($notypeid) && $map['id'] = ['NOTIN', $notypeid]; // 排除指定栏目ID
        $list = $logicInfoType->getInfoTypeList($map, true, 'sort asc',false);
        /*--end*/
        if (count($list['data']) > 0) {
            foreach ($list['data'] as $key => $val) {
                //处理栏目标识
                $topTypeid=$this->getTopTypeid($this->tid);
                if (in_array($val['id'],$topTypeid) || $val['id']==$this->tid) {
                    $val['currentstyle'] = $this->currentstyle;
                }else{
                    $val['currentstyle'] ='';
                }
                $result[$key] = $val;
            }
        }
        $result= list2tree($result,0,0,'id','parent_id','typename');//把所以树形展示
        return $result;
    }

    /**
     * 获取所有父级栏目ID
     */
    public function getTopTypeid($typeid)
    {
        $pids=[];
        if ($typeid > 0) {
            $logicInfoType = new \app\index\logic\InfoType();
            $pids = $logicInfoType->getInfoTypeAllPid($typeid);// 当前栏目往上一级级父栏目
        }
        return $pids;
    }
}