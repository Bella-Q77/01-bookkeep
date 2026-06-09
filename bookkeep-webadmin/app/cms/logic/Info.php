<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Infoor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\cms\logic;

use app\common\logic\TableField;

/**
 * 信息管理=》逻辑层
 */
class Info extends CmsBase
{
    /**
     * 信息列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getInfoList($where = [], $field = '', $order = 'a.pubdate_time desc', $paginate = DB_LIST_ROWS,$limit=DB_LIST_ROWS)
    {

        empty($field) && $field="a.id,a.title,a.type_id2,a.city_id,a.province_id,a.member_id,a.status,a.istop,a.istop_money,a.start_date,a.stop_date,a.click,
                                                     t.typename,c.name as company_name";

        $this->modelInfo->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'info_type t', 't.id = a.type_id2','LEFT'],
            [SYS_DB_PREFIX . 'member_company c', 'c.id = a.company_id','LEFT'],
        ];
        $this->modelInfo->join = $join;

        if($paginate===false) $this->modelInfo->limit = $limit;

        $list= $this->modelInfo->getList($where, $field, $order, $paginate);

        $citys=$this->logicRegion->getRegionColumn();
        foreach ($list as $key=>&$row){
            $row['province_name']=$citys[$row['province_id']];
            $row['city_name']=$citys[$row['city_id']];
            $row['istop_info']=$this->getIstop($row['istop']);
            $row['status_arr']=$this->getStatus($row['status']);
        }
        return $list;
    }

    /**
     * 信息添加
     * @param array $data
     * @return array
     */
    public function infoAdd($data = [])
    {

        $validate_result = $this->validateInfo->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateInfo->getError()];
        }
        $result = $this->modelInfo->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增信息：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelInfo->getError()];
    }

    /**
     * 信息编辑
     * @param array $data
     * @return array
     */
    public function infoEdit($data = [])
    {

        $validate_result = $this->validateInfo->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateInfo->getError()];
        }

        $url = url('show');

        $result = $this->modelInfo->setInfo($data);

        $result && action_log('编辑', '编辑信息，name：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelInfo->getError()];
    }

    /**
     * 信息删除
     * @param array $where
     * @return array
     */
    public function infoDel($where = [])
    {

        $result = $this->modelInfo->deleteInfo($where,true);

        $result && action_log('删除', '删除信息，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelInfo->getError()];
    }

    /**
     * 信息审核
     * @param array $data
     * @return array
     */
    public function infoAudit($data = [])
    {
        $result = $this->modelInfo->setInfo($data);
        $result && action_log('分类信息审核', '审核分类信息：' . $data['id'].'='.$data['status'] );
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '操作成功', ''] : [RESULT_ERROR, $this->modelMemberRealname->getError()];
    }

    /**信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getInfoInfo($where = [], $field = true)
    {

        return $this->modelInfo->getInfo($where, $field);
    }

    /**
     * 查询条件组合
     * @param array $data
     * @return array|mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/6 0006
     */
    public function getWhere($data=[]){
        $where = [];
        if (!empty($data['keywords'])) {
            $where['title|description'] = ['like', '%' . $data['keywords'] . '%'];
        }

        if (!empty($data['istop']) || is_numeric($data['istop'])) {
            $where['a.istop'] = ['=', $data['istop']];
        }
        if (!empty($data['status']) || is_numeric($data['status'])) {
            $where['a.status'] = ['=', $data['status']];
        }
        return $where;
    }

    /**信息信息=>审核状态
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getStatus($key='')
    {
        return $this->modelInfo->status($key);
    }

    /**
     * 信息信息=>置顶状态
     *
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getIstop($key='')
    {
        return $this->modelInfo->istop($key);
    }

}
