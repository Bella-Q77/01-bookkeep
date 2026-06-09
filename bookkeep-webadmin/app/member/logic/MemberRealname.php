<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberRealnameor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员等级升级管理=》逻辑层
 */
class MemberRealname extends MemberBase
{
    /**
     * 会员等级升级列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberRealnameList($where = [], $field = 'a.*,m.username', $order = 'a.status asc,a.update_time desc,a.create_time desc', $paginate = DB_LIST_ROWS)
    {
        $this->modelMemberRealname->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'member m', 'm.id = a.member_id','LEFT'],
        ];
        $this->modelMemberRealname->join = $join;
        $list= $this->modelMemberRealname->getList($where, $field, $order, $paginate)->toArray();
        if($paginate===false) $list['data']=$list;
        foreach ($list['data'] as &$row){
            $row['status_arr']=$this->modelMemberRealname->status($row['status']);
            $row['real_type_arr']=$this->modelMemberRealname->real_type_text($row['real_type']);
            $row['cert_pic']=get_picture_url($row['cert_pic'],'portalmember');
        }
        return $list;
    }

    /**
     * 会员等级升级添加
     * @param array $data
     * @return array
     */
    public function memberRealnameAdd($data = [])
    {

        $validate_result = $this->validateMemberRealname->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberRealname->getError()];
        }
        $result = $this->modelMemberRealname->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员等级升级：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberRealname->getError()];
    }

    /**
     * 会员等级升级编辑
     * @param array $data
     * @return array
     */
    public function memberRealnameEdit($data = [])
    {

        $validate_result = $this->validateMemberRealname->scene('edit')->check($data);

        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberRealname->getError()];
        }

        $result = $this->modelMemberRealname->setInfo($data);
        $result && action_log('编辑', '编辑会员等级升级，name：' . $data['name']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberRealname->getError()];
    }

    /**
     * 会员等级升级删除
     * @param array $where
     * @return array
     */
    public function memberRealnameDel($where = [])
    {

        $result = $this->modelMemberRealname->deleteInfo($where,true);

        $result && action_log('删除', '删除会员等级升级，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberRealname->getError()];
    }

    /**
     * 会员图片审核
     * @param array $data
     * @return array
     */
    public function memberRealnameAudit($data = [])
    {
        $result = $this->modelMemberRealname->setInfo($data);
        $result && action_log('实名审核', '审核实名图片：' . $data['id'].'='.$data['status'] );
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功', ''] : [RESULT_ERROR, $this->modelMemberRealname->getError()];
    }

    /**会员等级升级信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberRealnameInfo($where = [], $field = true)
    {

        return $this->modelMemberRealname->getInfo($where, $field);
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
            $where['realname|reply_remark'] = ['like', '%' . $data['keywords'] . '%'];
        }

        if (!empty($data['status']) || is_numeric($data['status'])) {
            $where['a.status'] = ['=', $data['status']];
        }
        if (!empty($data['real_type']) || is_numeric($data['real_type'])) {
            $where['a.real_type'] = ['=', $data['real_type']];
        }
        return $where;
    }

    public function getStatus($key=''){
        return $this->modelMemberRealname->status($key);
    }

    public function getRealType($key=''){
        return $this->modelMemberRealname->real_type_text($key);
    }

}
