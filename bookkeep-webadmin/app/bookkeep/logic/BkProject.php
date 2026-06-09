<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * 项目管理or: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;


/**
 * 项目管理=》逻辑层
 */
class BkProject extends BookkeepBase
{
    /**
     * 项目列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkProjectList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkProject->getList($where, $field, $order, $paginate);
        return $list;
    }

    /**
     * 项目添加
     * @param array $data
     * @return array
     */
    public function bkProjectAdd($data = [])
    {

        $validate_result = $this->validateBkProject->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkProject->getError()];
        }
        $result = $this->modelBkProject->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增项目：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('add success') . '', $url] : [RESULT_ERROR, $this->modelBkProject->getError()];
    }

    /**
     * 项目编辑
     * @param array $data
     * @return array
     */
    public function bkProjectEdit($data = [])
    {
        $validate_result = $this->validateBkProject->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkProject->getError()];
        }
        $url = url('show');
        $result = $this->modelBkProject->setInfo($data);
        $result && action_log('编辑', '编辑项目，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkProject->getError()];
    }

    /**
     * 项目删除
     * @param array $where
     * @return array
     */
    public function bkProjectDel($where = [])
    {
        $result = $this->modelBkProject->deleteInfo($where, true);
        $result && action_log('删除', '删除项目，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkProject->getError()];
    }

    /**
     * 项目信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkProjectInfo($where = [], $field = true)
    {
        return $this->modelBkProject->getInfo($where, $field);
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