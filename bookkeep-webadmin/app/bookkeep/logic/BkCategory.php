<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * 收支分类or: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;


/**
 * 收支分类管理=》逻辑层
 */
class BkCategory extends BookkeepBase
{
    /**
     * 收支分类列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkCategoryList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkCategory->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 收支分类添加
     * @param array $data
     * @return array
     */
    public function bkCategoryAdd($data = [])
    {

        $validate_result = $this->validateBkCategory->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkCategory->getError()];
        }
        $result = $this->modelBkCategory->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增收支分类：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('add success') . '', $url] : [RESULT_ERROR, $this->modelBkCategory->getError()];
    }

    /**
     * 收支分类编辑
     * @param array $data
     * @return array
     */
    public function bkCategoryEdit($data = [])
    {
        $validate_result = $this->validateBkCategory->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkCategory->getError()];
        }
        $url = url('show');
        $result = $this->modelBkCategory->setInfo($data);
        $result && action_log('编辑', '编辑收支分类，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkCategory->getError()];
    }

    /**
     * 收支分类删除
     * @param array $where
     * @return array
     */
    public function bkCategoryDel($where = [])
    {
        $result = $this->modelBkCategory->deleteInfo($where, true);
        $result && action_log('删除', '删除收支分类，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkCategory->getError()];
    }

    /**
     * 收支分类信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkCategoryInfo($where = [], $field = true)
    {
        return $this->modelBkCategory->getInfo($where, $field);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];
        //关键字查
        !empty($data['keywords']) && $where['name|remark'] = ['like', '%' . $data['keywords'] . '%'];

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

        if (isset($data['type'])) {
            if (!empty($data['type']) || is_numeric($data['type'])) {
                $where['type'] = ['=', $data['type']];
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
