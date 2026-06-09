<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkPersonor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 成员管理=》逻辑层
 */
class BkPerson extends PortalmemberBookkeepBase
{
    /**
     * 成员列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkPersonList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkPerson->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 成员添加
     * @param array $data
     * @return array
     */
    public function bkPersonAdd($data = [])
    {

        $validate_result = $this->validateBkPerson->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkPerson->getError()];
        }
        $data['member_id'] = MEMBER_ID;
        $result = $this->modelBkPerson->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增成员：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('add success') . '', $url] : [RESULT_ERROR, $this->modelBkPerson->getError()];
    }

    /**
     * 成员编辑
     * @param array $data
     * @return array
     */
    public function bkPersonEdit($data = [])
    {

        $validate_result = $this->validateBkPerson->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkPerson->getError()];
        }

        $url = url('show');
        $result = $this->modelBkPerson->setInfo($data);
        $result && action_log('编辑', '编辑成员，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkPerson->getError()];
    }

    /**
     * 成员删除
     * @param array $where
     * @return array
     */
    public function bkPersonDel($where = [])
    {
        $result = $this->modelBkPerson->deleteInfo($where, true);
        $result && action_log('删除', '删除成员，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkPerson->getError()];
    }

    /**
     * 成员信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkPersonInfo($where = [], $field = true)
    {
        return $this->modelBkPerson->getInfo($where, $field);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where['member_id'] = ['=', MEMBER_ID];
        //关键字查
        !empty($data['keywords']) && $where['name'] = ['like', '%' . $data['keywords'] . '%'];

        return $where;
    }

    /**
     * 获取排序条件
     */
    public function getOrderBy($data = [])
    {
        $order_by = "id asc";
        //排序操作
        if (!empty($data['orderField'])) {
            $orderField = $data['orderField'];
            $orderDirection = $data['orderDirection'];
            switch ($orderField){
                case 'sort':
                    $order_by = "sort $orderDirection";
                    break;
                case 'name':
                    $order_by = "name $orderDirection";
                    break;
            }
        }
        return $order_by;
    }
    public function setBkPersonDefault($data = [])
    {
        if (empty($data['id'])) {
            throw_response_error('选择执行的参数');
        }
        $where['member_id'] = ['=', MEMBER_ID];
        $result = $this->modelBkPerson->setFieldValue($where, 'is_default', 0);

        //设置默认
        $where['id'] = ['=', $data['id']];
        $result && $this->modelBkPerson->setFieldValue(['id' => $data['id']], 'is_default', 1);
        $result && action_log('设置默认成员', '设置默认成员，id：' . $data['id']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelLptLanguage->getError()];
    }
}
