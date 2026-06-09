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
 * 分类信息栏目管理=》逻辑层
 */
class Info extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getInfoList($where = [], $field = '', $order = '', $paginate = DB_LIST_ROWS, $limit=DB_LIST_ROWS)
    {

        if(empty($field)){
            $field = 'a.id,a.title,a.description,a.content,a.pubdate_time,a.litpic,a.city_id';
        }

        $this->modelInfo->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'info_type t', 't.id = a.type_id2','LEFT'],
        ];
        $this->modelInfo->join = $join;
        if($paginate===false)  $this->modelInfo->limit =$limit;
        $list= $this->modelInfo->getList($where, $field, $order, $paginate);
        $city=$this->logicRegion->getRegionListName();
        foreach ($list as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['infourl'] =$this->getInfoUrl($row);
            $row['city_name'] =$city[$row['city_id']];
        }
        return $list;
    }

    /**转换一条文章的实际地址
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getInfoUrl($data = [])
    {
        $arcurl = url('index/info/view', array('iid' => $data['id']));
        return $arcurl;
    }



    /**获取详细
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getInfoInfo($where = [], $field = true)
    {
        $info = $this->modelInfo->getInfo($where, $field);
        $city=$this->logicRegion->getRegionListName();
        if ($info) {
            $info['province_name'] =$city[$info['province_id']];
            $info['city_name'] =$city[$info['city_id']];
            $info['county_name'] =$city[$info['county_id']];
            $info['company'] =$this->logicMemberCompany->getMemberCompanyInfo(['id'=>$info['company_id']]);
        }
        return  $info;
    }


    /**获取文档下一条
     * @param $aid
     * @return mixed|string
     * Author: lingqifei created by at 2020/3/18 0018
     */
    public function getInfoNext($aid, $channelid, $typeid)
    {
        $map['id'] = ['gt', $aid];
        $map['channel_id'] = ['=', $channelid];
        $map['type_id'] = ['=', $typeid];
        $this->modelInfo->limit = 1;
        $list = $this->modelInfo->getList($map, '', 'id asc', false)->toArray();

        if ($list) {
            $id = $list[0]['id'];
            return $this->getInfoInfo(['id' => $id]);
        } else {
            return '';
        }

    }

    /**获取文档上一条
     * @param $aid
     * @return mixed|string
     * Author: lingqifei created by at 2020/3/18 0018
     */
    public function getInfoPre($aid, $channelid, $typeid)
    {

        $map['id'] = ['lt', $aid];
        $map['channel_id'] = ['=', $channelid];
        $map['type_id'] = ['=', $typeid];
        $this->modelInfo->limit = 1;
        $list = $this->modelInfo->getList($map, '', 'id desc', false)->toArray();

        if ($list) {
            $id = $list[0]['id'];
            return $this->getInfoInfo(['id' => $id]);
        } else {
            return '';
        }

    }


    /**获取文章详细
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getInfoFieldValue($where = [], $field = true)
    {
        $info = $this->modelInfo->getValue($where, $field);
        return $info;
    }


    /**
     * 获取文章信息=》列数
     * @param array $where
     * @param string $field
     * @param string $key
     * @return array();
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/12/9 0009
     */
    public function getInfoColumn($where = [], $field = '', $key='')
    {
        $this->modelInfo->alias('a');
        $info = $this->modelInfo->getColumn($where, $field,$key);
        return $info;
    }
    
    /**设置文章点击
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function setInfoClick($where = [], $field = true)
    {
        $click = $this->modelInfo->getValue($where, 'click');
        if($click){
            $click=(int)$click+1;
            $this->modelInfo->setFieldValue($where, 'click',$click);
        }
    }

}