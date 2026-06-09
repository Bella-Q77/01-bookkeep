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
class TagArclist extends Base
{
    public $tid = '';
    public $currentstyle = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();

        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');

        /*应用于文档列表*/
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            //引用文档逻辑
            $this->logicArchives= new \app\index\logic\Archives();
            $this->tid = $this->logicArchives->getArchivesFieldValue(['id'=>$aid],'type_id');
        }
        /*--end*/

        /*tid为目录名称的情况下*/
        $this->tid = $this->getTrueTypeid($this->tid);
        /*--end*/
    }

    /**
     *  arclist解析函数
     *
     * @author wengxianhu by 2018-4-20
     * @access    public
     * @param     array  $param  查询数据条件集合
     * @param     int  $row  调用行数
     * @param     string  $orderby  排列顺序
     * @param     string  $addfields  附加表字段，以逗号隔开
     * @param     string  $orderway  排序方式
     * @param     string  $tagid  标签id
     * @param     string  $tag  标签属性集合
     * @param     string  $pagesize  分页显示条数
     * @param     string  $thumb  是否开启缩略图
     * @return    array
     */
    public function getArclist($param = array(),  $row = 15, $orderby = '', $addfields = '', $orderway = '', $tagid = '', $tag = '', $pagesize = 0, $thumb = '')
    {
        $result = false;

        $param['channelid'] = ("" != $param['channelid'] && is_numeric($param['channelid'])) ? intval($param['channelid']) : '';

        $param['typeid'] = !empty($param['typeid']) ? $param['typeid'] : $this->tid;

        empty($orderway) && $orderway = 'desc';

        $pagesize = empty($pagesize) ? intval($row) : intval($pagesize);


        if(!empty($param['typeid2'])){
            $param['typeid']='';//启用了副栏目，主栏目失效果
            $param['typeid2']= $this->tid;
        }

        //频道显示
        if (!empty($param['channelid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['channelid'])) {
                echo '标签arclist报错：channelid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            // 过滤channelid中含有空值的栏目ID
            $channelidArr_tmp = explode(',', $param['channelid']);
            $channelidArr_tmp = array_unique($channelidArr_tmp);
            foreach($channelidArr_tmp as $k => $v){
                if (empty($v)) unset($channelidArr_tmp[$k]);
            }
            $param['channelid'] = implode(',', $channelidArr_tmp);
            // end
        }

        //文章按地区显示
        if (!empty($param['cityid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['cityid'])) {
                echo '标签arclist报错：cityid属性值语法错误，请正确填写城市ID。';
                return false;
            }
            // 过滤cityid中含有空值的栏目ID
            $channelidArr_tmp = explode(',', $param['cityid']);
            $channelidArr_tmp = array_unique($channelidArr_tmp);
            foreach($channelidArr_tmp as $k => $v){
                if (empty($v)) unset($channelidArr_tmp[$k]);
            }
            $param['cityid'] = implode(',', $channelidArr_tmp);
            // end
        }

        //文章类别
        if (!empty($param['typeid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['typeid'])) {
                echo '标签arclist报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            // 过滤typeid中含有空值的栏目ID
            $typeidArr_tmp = explode(',', $param['typeid']);
            $typeidArr_tmp = array_unique($typeidArr_tmp);
            $typeidArr_son = [];//得到子级栏目
            $logicArctype = new \app\index\logic\Arctype();
            foreach ($typeidArr_tmp as $k => $v) {
                if (empty($v)) {
                    unset($typeidArr_tmp[$k]);
                }else{
                    $typeid_son=$logicArctype->getArctypeAllSon($v);
                    $typeid_son && $typeidArr_son=array_merge($typeidArr_son,$typeid_son);
                }
            }

            $typeidArr_tmp = array_merge($typeidArr_tmp,$typeidArr_son);

            $param['typeid'] = implode(',', $typeidArr_tmp);
            // end
        }

        $where = [];
        if(!empty($param['channelid'])){
            $where['a.channel_id']=['in',$param['channelid']];
            $randMap['channel_id']=['in',$param['channelid']];
        }

        if(!empty($param['cityid'])){
            $where['a.sys_area_id']=['in',$param['cityid']];
            $randMap['sys_area_id']=['in',$param['cityid']];
        }

        if(!empty($param['typeid'])){
            $where['a.type_id']=['in',$param['typeid']];
            $randMap['type_id']=['in',$param['typeid']];
        }

		if(!empty($param['notypeid'])){
			$where['a.type_id']=['not in',$param['notypeid']];
			$randMap['type_id']=['not in',$param['notypeid']];
		}

        if(!empty($param['typeid2'])){
            $where['a.type_id2']=['in',$param['typeid2']];
            $randMap['type_id2']=['in',$param['typeid2']];
        }

        if(!empty($param['flag'])){
            $reg_txt=str_replace(",","|",$param['flag']);
            $where['a.flag']=['exp',Db::raw("REGEXP '(^|,)($reg_txt)(,|$)'")];
            $randMap['flag']=['exp',Db::raw("REGEXP '(^|,)($reg_txt)(,|$)'")];
        }

        /*获取文档列表*/
        $logicArchives = new \app\index\logic\Archives();

        //排序=>随机特殊处理
        switch ($orderby) {
            case 'rand':
                $rand_ids=$logicArchives->getArchivesColumn($randMap,'id');
                $rand_cnt=count($rand_ids);
                $number=(count($rand_ids)>15)?'15':$rand_cnt;
                $rand_id=array_rand_value($rand_ids,$number);
                $where['a.id'] = array('in', $rand_id);
                $orderby = 'a.create_time DESC';
                break;
            default:
                $orderby =$logicArchives->getOrderBy($orderby,$orderway);
                break;
        }

		//判断是否查询指定频道扩展关联表
		if (!empty($param['channelexttable'])) {
			$result = $logicArchives->getArchivesExtableList($where, 'a.*,e.*', $orderby, $pagesize,$param['channelexttable']);
		}else{
			$result = $logicArchives->getArchivesList($where, true, $orderby,$pagesize);
		}

        //获取文档栏目信息
        $logicArctype = new \app\index\logic\Arctype();
        foreach ($result['data'] as &$row){
            $typeinfo=$logicArctype->getArctypeInfo(['id'=>$row['type_id']]);
            if($typeinfo){
                $row['typename']=$typeinfo['typename'];
                $row['typeurl']=$typeinfo['typeurl'];
                $row['typepic']=$typeinfo['litpic'];
            }
        }

        //是引用扩展字段
        if($addfields){
            foreach ($result['data'] as &$row) {
                $row=$logicArchives->getArchivesInfo(['id'=>$row['id']]);
            }
        }

        $data=[
            "list"=>$result['data'],
            "tag"=>'',
        ];
        return $data;
    }


}