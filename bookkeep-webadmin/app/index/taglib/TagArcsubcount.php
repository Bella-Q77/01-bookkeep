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
 * 标签主要用于列表和文档调中调用他关联的文档列表
 */
class TagArcsubcount extends Base
{
    public $tid = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();

        /*应用于文档列表*/
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->aid = $aid;
        }
        /*--end*/
    }

    /**
     *  arcsubcount 解析函数
     *
     * @author kaifarensheng by 2018-4-20
     * @access    public
     * @param     array  $param  查询数据条件集合
     * @return    关联文档总数
     */
    public function getArcsubcount($param)
    {
        $result = false;

        if (empty($param['linkfield'])) {
            echo '标签arcsublist报错：linkfield属性值不能为空，请正确填写关联field。';
            return false;
        }

        if (!empty($param['channelid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['channelid'])) {
                echo '标签arcsublist报错：channelid属性值语法错误，请正确填写模块ID。';
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
        }else{
            echo '标签arcsublist报错：channelid属性值为不能为空，请正确填写模块ID。';
            return false;
        }

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


        /*获取文档列表*/
        $where = [];
        if (!empty($param['channelid'])) {

            $where['a.channel_id'] = ['=', $param['channelid']];

            $addtable = Db::name('channel')->where('id', $param['channelid'])->value('addtable');
            $addfield = Db::name('channel_field')->where('ext_table', $addtable)->column('field_name');

            if(in_array($param['linkfield'],$addfield)){
                $linkfield='b.'.$param['linkfield'];
            }else{
                $linkfield='a.'.$param['linkfield'];
            }
            if($param['linkreg']=='like'){
                $where[$linkfield]=['like','%'.$param['linkvalue'].'%'];
            }else if($param['linkreg']=='in'){
                $where[$linkfield]=['in',$param['linkvalue']];
            }else{
                $where[$linkfield]=['=',$param['linkvalue']];
            }
        }

        if(!empty($param['typeid'])){
            $where['type_id']=['in',$param['typeid']];
        }

        $logicArchives = new \app\index\logic\Archives();
        $result = $logicArchives->getArchivesSubList($where, 'a.*,b.*', '',false,$limit='',$addtable);
        return count($result['data']);
    }
}