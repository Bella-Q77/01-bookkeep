<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * 账本管理or: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;


/**
 * 账本管理管理=》逻辑层
 */
class BkBook extends BookkeepBase
{
    /**
     * 账本管理列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkBookList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkBook->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 账本管理添加
     * @param array $data
     * @return array
     */
    public function bkBookAdd($data = [])
    {

        $validate_result = $this->validateBkBook->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkBook->getError()];
        }
        $result = $this->modelBkBook->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增账本管理：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('add success') . '', $url] : [RESULT_ERROR, $this->modelBkBook->getError()];
    }

    /**
     * 账本管理编辑
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
        $result && action_log('编辑', '编辑账本管理，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkBook->getError()];
    }

    /**
     * 账本管理删除
     * @param array $where
     * @return array
     */
    public function bkBookDel($where = [])
    {
        $result = $this->modelBkBook->deleteInfo($where, true);
        $result && action_log('删除', '删除账本管理，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkBook->getError()];
    }

    /**
     * 账本管理信息
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
        $where = [];
        //关键字查
        !empty($data['keywords']) && $where['name|description'] = ['like', '%' . $data['keywords'] . '%'];

        if (isset($data['is_template'])) {
            if (!empty($data['is_template']) || is_numeric($data['is_template'])) {
                $where['is_template'] = $data['is_template'];
            }
        }

        if (isset($data['member_id'])) {
            if (!empty($data['member_id']) || is_numeric($data['member_id'])) {
                $where['member_id'] = ['=', $data['member_id']];
            }
        }

        if (isset($data['book_id'])) {
            if (!empty($data['book_id']) || is_numeric($data['book_id'])) {
                $where['book_id'] = ['=', $data['book_id']];
            }
        }

        return $where;
    }

    /**
     * 获取排序条件
     */
    public function getOrderBy($data = [])
    {
        $order_by = "sort asc";
        //排序操作
        if (!empty($data['orderField'])) {
            $orderField = $data['orderField'];
            $orderDirection = $data['orderDirection'];
            if ($orderField == 'by_sort') {
                $order_by = "sort $orderDirection";
            }
        }
        return $order_by;
    }
}