<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberLevelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员等级管理=》逻辑层
 */
class MemberLevel extends MemberBase
{
    /**
     * 会员等级列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberLevelList($where = [], $field = true, $order = 'level_value asc', $paginate = DB_LIST_ROWS)
    {
        $list= $this->modelMemberLevel->getList($where, $field, $order, $paginate)->toArray();
        if($paginate===false) $list['data']=$list;
        foreach ($list['data'] as &$row){
            //$row['last_login']=format_time($row['last_login']);
        }
        return $list;
    }

    /**
     * 会员等级添加
     * @param array $data
     * @return array
     */
    public function memberLevelAdd($data = [])
    {

        $validate_result = $this->validateMemberLevel->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberLevel->getError()];
        }
        $result = $this->modelMemberLevel->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员等级：' . $data['level_name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberLevel->getError()];
    }

    /**
     * 会员等级编辑
     * @param array $data
     * @return array
     */
    public function memberLevelEdit($data = [])
    {

        $validate_result = $this->validateMemberLevel->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberLevel->getError()];
        }

        $url = url('show');

        $result = $this->modelMemberLevel->setInfo($data);

        $result && action_log('编辑', '编辑会员等级，name：' . $data['level_name']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberLevel->getError()];
    }

    /**
     * 会员等级删除
     * @param array $where
     * @return array
     */
    public function memberLevelDel($where = [])
    {

        $result = $this->modelMemberLevel->deleteInfo($where,true);

        $result && action_log('删除', '删除会员等级，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberLevel->getError()];
    }

    /**会员等级信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberLevelInfo($where = [], $field = true)
    {

        return $this->modelMemberLevel->getInfo($where, $field);
    }

}
