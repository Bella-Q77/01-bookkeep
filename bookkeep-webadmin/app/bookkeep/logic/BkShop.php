<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * 商家管理or: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;


/**
 * 商家管理=》逻辑层
 */
class BkShop extends BookkeepBase
{
    /**
     * 商家列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkShopList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkShop->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 商家添加
     * @param array $data
     * @return array
     */
    public function bkShopAdd($data = [])
    {

        $validate_result = $this->validateBkShop->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkShop->getError()];
        }
        $result = $this->modelBkShop->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增商家：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('add success') . '', $url] : [RESULT_ERROR, $this->modelBkShop->getError()];
    }

    /**
     * 商家编辑
     * @param array $data
     * @return array
     */
    public function bkShopEdit($data = [])
    {
        $validate_result = $this->validateBkShop->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkShop->getError()];
        }
        $url = url('show');
        $result = $this->modelBkShop->setInfo($data);
        $result && action_log('编辑', '编辑商家，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkShop->getError()];
    }

    /**
     * 商家删除
     * @param array $where
     * @return array
     */
    public function bkShopDel($where = [])
    {
        $result = $this->modelBkShop->deleteInfo($where, true);
        $result && action_log('删除', '删除商家，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkShop->getError()];
    }

    /**
     * 商家信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkShopInfo($where = [], $field = true)
    {
        return $this->modelBkShop->getInfo($where, $field);
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
        $where = [];
        //关键字查
        !empty($data['keywords']) && $where['name|description'] = ['like', '%' . $data['keywords'] . '%'];

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