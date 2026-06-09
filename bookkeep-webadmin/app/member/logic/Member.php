<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Memberor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员列表管理=》逻辑层
 */
class Member extends MemberBase
{
    /**
     * 会员列表列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberList($where = [], $field = true, $order = 'id desc', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelMember->getList($where, $field, $order, $paginate);
        foreach ($list as &$row) {
            $row['last_login'] = format_time($row['last_login']);
            $row['level_name'] = $this->modelMemberLevel->getValue(['id' => $row['level_id']], 'level_name');
        }
        return $list;
    }

    /**
     * 会员列表添加
     * @param array $data
     * @return array
     */
    public function memberAdd($data = [])
    {

        $validate_result = $this->validateMember->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMember->getError()];
        }
        $data['password'] = data_md5_key($data['password']);
        $result = $this->modelMember->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员列表：' . $data['username']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMember->getError()];
    }

    /**
     * 会员列表编辑
     * @param array $data
     * @return array
     */
    public function memberEdit($data = [])
    {
        $validate_result = $this->validateMember->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMember->getError()];
        }

        $url = url('show');
        $result = $this->modelMember->setInfo($data);
        $result && action_log('编辑', '编辑会员列表，name：' . $data['username']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMember->getError()];
    }

    /**
     * 会员列表编辑=>密码
     * @param array $data
     * @return array
     */
    public function memberEditPwd($data = [])
    {
        $validate_result = $this->validateMember->scene('edit_pwd')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMember->getError()];
        }
        $data['password'] = data_md5_key($data['password']);
        $url = url('show');
        $result = $this->modelMember->setInfo($data);
        $result && action_log('编辑', '重置密码，name：' . $data['username']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMember->getError()];
    }

    /**
     * 会员列表删除
     * @param array $where
     * @return array
     */
    public function memberDel($where = [])
    {

        $result = $this->modelMember->deleteInfo($where, true);

        $result && action_log('删除', '删除会员列表，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMember->getError()];
    }

    /**会员列表信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberInfo($where = [], $field = true)
    {

        return $this->modelMember->getInfo($where, $field);
    }

    /**
     * 查询条件组合
     * @param array $data
     * @return array|mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/6 0006
     */
    public function getWhere($data = [])
    {
        $where = [];
        if (!empty($data['keywords'])) {
            $where['username|mobile|email|qicq'] = ['like', '%' . $data['keywords'] . '%'];
        }
        return $where;
    }
}
