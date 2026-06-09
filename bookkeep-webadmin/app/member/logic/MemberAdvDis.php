<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberAdvDisor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员广告管理=》逻辑层
 */
class MemberAdvDis extends MemberBase
{
    /**
     * 会员广告列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberAdvDisList($where = [], $field = 'a.*,b.name as adv_name,m.username', $order = 'a.create_time desc', $paginate = DB_LIST_ROWS)
    {

        $this->modelMemberAdvDis->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'member_adv b', 'b.id = a.adv_id','LEFT'],
            [SYS_DB_PREFIX . 'member m', 'm.id = a.member_id','LEFT'],
        ];
        $this->modelMemberAdvDis->join = $join;
        //$total_money=$this->modelMemberAdvDis->stat($where, 'sum', 'a.money');
        $list= $this->modelMemberAdvDis->getList($where, $field, $order, $paginate);
        foreach ($list as &$row){
//            $row['ad_type_info']=$this->modelMemberAdvDis->ad_type($row['ad_type']);
            $row['status_info']=$this->modelMemberAdvDis->status($row['status']);
        }

        $list=$list->toArray();
        //$list['all_total_money']=$total_money;

        return $list;
    }

    /**
     * 会员广告添加
     * @param array $data
     * @return array
     */
    public function memberAdvDisAdd($data = [])
    {

        $validate_result = $this->validateMemberAdvDis->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberAdvDis->getError()];
        }
        $result = $this->modelMemberAdvDis->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员广告：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberAdvDis->getError()];
    }

    /**
     * 会员广告编辑
     * @param array $data
     * @return array
     */
    public function memberAdvDisEdit($data = [])
    {

        $validate_result = $this->validateMemberAdvDis->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberAdvDis->getError()];
        }

        $url = url('show');

        $result = $this->modelMemberAdvDis->setInfo($data);

        $result && action_log('编辑', '编辑会员广告，title：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberAdvDis->getError()];
    }

    /**
     * 会员广告删除
     * @param array $where
     * @return array
     */
    public function memberAdvDisDel($where = [])
    {

        $result = $this->modelMemberAdvDis->deleteInfo($where,true);

        $result && action_log('删除', '删除会员广告，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberAdvDis->getError()];
    }

    /**会员广告信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberAdvDisInfo($where = [], $field = true)
    {

        return $this->modelMemberAdvDis->getInfo($where, $field);
    }

    /**会员广告状态
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getstatus($key='')
    {

        return $this->modelMemberAdvDis->status($key='');
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
            $where['a.title|a.member_id|b.name'] = ['like', '%' . $data['keywords'] . '%'];
        }

        if (!empty($data['status']) || is_numeric($data['status'])) {
            $where['a.status'] = ['=', $data['status']];
        }

        return $where;
    }

}
