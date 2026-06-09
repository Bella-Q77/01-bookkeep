<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkBookor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 账本管理=》逻辑层
 */
class BkBook extends PortalmemberBookkeepBase
{
    /**
     * 账本列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkBookList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkBook->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 账本添加
     * @param array $data
     * @return array
     */
    public function bkBookAdd($data = [])
    {

        $validate_result = $this->validateBkBook->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkBook->getError()];
        }
        $data['member_id'] = MEMBER_ID;
        $result = $this->modelBkBook->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增账本：' . $data['name']);

        $info=$this->modelBkBook->getInfo(['id'=>$result]);

        return $result ? [RESULT_SUCCESS, '' . lang('添加成功') . '', $url,$info] : [RESULT_ERROR, $this->modelBkBook->getError()];
    }

    /**
     * 账本编辑
     * @param array $data
     * @return array
     */
    public function bkBookEdit($data = [])
    {

        $validate_result = $this->validateBkBook->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkBook->getError()];
        }
        $url = url('show');
        $result = $this->modelBkBook->setInfo($data);
        $result && action_log('编辑', '编辑账本，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkBook->getError()];
    }

    /**
     * 账本删除
     * @param array $where
     * @return array
     */
    public function bkBookDel($where = [])
    {
        $result = $this->modelBkBook->deleteInfo($where, true);
        $result && action_log('删除', '删除账本，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkBook->getError()];
    }

    /**
     * 账本信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkBookInfo($where = [], $field = true)
    {
        return $this->modelBkBook->getInfo($where, $field);
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
    public function setBkBookDefault($data = [])
    {
        if (empty($data['id'])) {
            throw_response_error('选择执行的参数');
        }
        $where['member_id'] = ['=', MEMBER_ID];
        $result = $this->modelBkBook->setFieldValue($where, 'is_default', 0);

        //设置默认
        $where['id'] = ['=', $data['id']];
        $result && $this->modelBkBook->setFieldValue(['id' => $data['id']], 'is_default', 1);
        $result && action_log('设置默认账本', '设置默认账本，id：' . $data['id']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelLptLanguage->getError()];
    }
}
