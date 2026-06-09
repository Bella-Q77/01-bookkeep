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
class TagArcextlist extends Base
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
     *  arclist解析函数
     *
     * @author wengxianhu by 2018-4-20
     * @access    public
     * @param     array  $param  查询数据条件集合
     * @param     int  $row  调用行数
     * @param     string  $orderby  排列顺序
     * @param     string  $orderway  排序方式
     * @param     string  $pagesize  分页显示条数
     * @param     string  $thumb  是否开启缩略图
     * @return    array
     */
    public function getArcextlist($param = array(),  $row = 15, $orderby = '', $orderway = '', $pagesize = 20, $thumb = '')
    {
        $result = false;

        $param['aid'] = !empty($param['aid']) ? $param['aid'] : $this->aid;

        empty($orderway) && $orderway = 'desc';

        $pagesize = empty($pagesize) ? intval($row) : intval($pagesize);

        if (!empty($param['eid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['eid'])) {
                echo '标签arclist报错：eid属性值语法错误，请正确填写模型扩展表ID编号。';
                return false;
            }
        }
        /*获取文档列表*/
        $where = [];
        if(!empty($param['eid'])){
            $where['eid']=['=',$param['eid']];
        }
        if(!empty($param['aid'])){
            $where['aid']=['=',$param['aid']];
        }else{
            $where['aid']=['=',0];
        }
        $logicArchives = new \app\index\logic\Arcext();
        $orderby =$logicArchives->getOrderBy($orderby,$orderway);
        $result = $logicArchives->getArcextList($where, true, $orderby,$pagesize);
        if(is_array($result['data'])){
            foreach($result['data'] as &$row){
                $row['litpic_array']=explode(',',$row['litpic']);
            }
        }
        //返回数据格式
        $data=[
            "list"=>$result['data'],
            "tag"=>'',
        ];
        return $data;
    }


}