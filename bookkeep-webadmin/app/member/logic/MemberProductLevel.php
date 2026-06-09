<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberProductLevelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员等级升级管理=》逻辑层
 */
class MemberProductLevel extends MemberBase
{
    /**
     * 会员等级升级列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberProductLevelList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list= $this->modelMemberProductLevel->getList($where, $field, $order, $paginate)->toArray();
        if($paginate===false) $list['data']=$list;
        foreach ($list['data'] as &$row){
            $row['level_name']=$this->modelMemberLevel->getValue(['id'=>$row['level_id']],'level_name');
        }
        return $list;
    }

    /**
     * 会员等级升级添加
     * @param array $data
     * @return array
     */
    public function memberProductLevelAdd($data = [])
    {

        $validate_result = $this->validateMemberProductLevel->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberProductLevel->getError()];
        }
        $result = $this->modelMemberProductLevel->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员等级升级：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberProductLevel->getError()];
    }

    /**
     * 会员等级升级编辑
     * @param array $data
     * @return array
     */
    public function memberProductLevelEdit($data = [])
    {

        $validate_result = $this->validateMemberProductLevel->scene('edit')->check($data);

        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberProductLevel->getError()];
        }

        $result = $this->modelMemberProductLevel->setInfo($data);
        $result && action_log('编辑', '编辑会员等级升级，name：' . $data['name']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberProductLevel->getError()];
    }

    /**
     * 会员等级升级删除
     * @param array $where
     * @return array
     */
    public function memberProductLevelDel($where = [])
    {

        $result = $this->modelMemberProductLevel->deleteInfo($where,true);

        $result && action_log('删除', '删除会员等级升级，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberProductLevel->getError()];
    }

    /**会员等级升级信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberProductLevelInfo($where = [], $field = true)
    {

        return $this->modelMemberProductLevel->getInfo($where, $field);
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
            $where['name|remark'] = ['like', '%' . $data['keywords'] . '%'];
        }
        return $where;
    }

}
