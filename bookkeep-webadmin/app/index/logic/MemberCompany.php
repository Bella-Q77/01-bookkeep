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
 * 会员企业管理=》逻辑层
 */
class MemberCompany extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getMemberCompanyList($where = [], $field = 'a.*', $order = '', $paginate = DB_LIST_ROWS, $limit = DB_LIST_ROWS)
    {

        if(empty($field)){
            $field = 'a.name,a.litpic,a.intro,a.id,a.city_id';
        }
        $this->modelMemberCompany->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'info_type t', 't.id = a.category_id','LEFT'],
        ];
        $this->modelMemberCompany->join = $join;

        if($paginate===false)  $this->modelMemberCompany->limit =$limit;

        $list= $this->modelMemberCompany->getList($where, $field, $order, $paginate);
        $city=$this->logicRegion->getRegionListName();
        foreach ($list as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['comurl'] =$this->getMemberCompanyUrl($row);
            $row['city_name'] =empty($city[$row['city_id']])?'':$city[$row['city_id']];
            //$row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }
        return $list;
    }


    /**转换一条文章的实际地址
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getMemberCompanyUrl($data = [])
    {
        $arcurl = url('index/Company/view', array('cid' => $data['id']));
        return $arcurl;
    }


    /**获取文章详细
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getMemberCompanyInfo($where = [], $field = true)
    {
        $info = $this->modelMemberCompany->getInfo($where, $field);
        return  $info;
    }


    /**设置文章点击
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function setMemberCompanyClick($where = [], $field = true)
    {
        $click = $this->modelMemberCompany->getValue($where, 'click');
        if($click){
            $click=(int)$click+1;
            $this->modelMemberCompany->setFieldValue($where, 'click',$click);
        }
    }


}