<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkAccountor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

use app\common\service\ServiceBase;
use \think\Db;

/**
 * 账本管理=》逻辑层
 */
class BkAccount extends PortalmemberBookkeepBase
{

    public function getAccountType()
    {
        return $this->modelBkAccount->account_type();
    }

    public function getBkAccountData($data = [])
    {
        if (!empty($data['book_id'])) {
            $bookId = $data['book_id'];
        } else {
            $bookId = $this->bookId;
        }

        //同步资产账户中的余额
        $where['member_id'] = MEMBER_ID;
        $accountList = $this->modelBkAccount->getList($where, true, 'sort asc', false);
        foreach ($accountList as &$row) {
            $row['account_type_name'] = $this->modelBkAccount->account_type_name($row['account_type']);
            $this->modelBkAccount->syncAccountBalance($row['id']);//同步余额
        }

        $where['is_debt'] = 0;
        $totalAssets = $this->modelBkAccount->stat($where, 'sum', 'balance');
        $where['is_debt'] = 1;
        $negativeAssets = $this->modelBkAccount->stat($where, 'sum', 'balance');
        $negativeAssets = abs($negativeAssets);
        $netAssets = number_format(($totalAssets - $negativeAssets), 2);

        $typeList = $this->modelBkAccount->account_type();

        //总体预算使用情况
        $rtnData = [
            'total_assets' => number_format($totalAssets, 2),
            'negative_assets' => number_format($negativeAssets, 2),
            'net_assets' => $netAssets,
            'account_list' => $accountList,
            'account_type_list' => $typeList,
        ];
        return [RESULT_SUCCESS, '获取资产数据成功', '', $rtnData];
    }

    /**
     * 账本列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getBkAccountList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        $list = $this->modelBkAccount->getList($where, $field, $order, $paginate);
        foreach ($list as &$row) {
            $row['account_type_name'] = $this->modelBkAccount->account_type_name($row['account_type']);
        }
        return $list;
    }

    /**
     * 账本添加
     * @param array $data
     * @return array
     */
    public function bkAccountAdd($data = [])
    {
        $validate_result = $this->validateBkAccount->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkAccount->getError()];
        }
        $data['member_id'] = MEMBER_ID;
        $data['is_debt'] = $this->modelBkAccount->account_type_debt($data['account_type']);
        $result = $this->modelBkAccount->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增账本：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('add success') . '', $url] : [RESULT_ERROR, $this->modelBkAccount->getError()];
    }

    /**
     * 账本编辑
     * @param array $data
     * @return array
     */
    public function bkAccountEdit($data = [])
    {
        $validate_result = $this->validateBkAccount->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateBkAccount->getError()];
        }
        $data['is_debt'] = $this->modelBkAccount->account_type_debt($data['account_type']);
        $url = url('show');
        $result = $this->modelBkAccount->setInfo($data);
        $result && action_log('编辑', '编辑账本，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '' . lang('edit success') . '', $url] : [RESULT_ERROR, $this->modelBkAccount->getError()];
    }

    /**
     * 账本删除
     * @param array $where
     * @return array
     */
    public function bkAccountDel($where = [])
    {
        $result = $this->modelBkAccount->deleteInfo($where, true);
        $result && action_log('删除', '删除账本，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '' . lang('del success') . ''] : [RESULT_ERROR, $this->modelBkAccount->getError()];
    }

    /**
     * 账本信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBkAccountInfo($where = [], $field = true)
    {
        return $this->modelBkAccount->getInfo($where, $field);
    }


    //账本详情
    //@param array $data id
    //@return array 账户的详细和总流入，总流出
    public function getBkAccountDetail($data)
    {
        $where['id'] = $data['id'];
        $accountInfo = $this->modelBkAccount->getInfo($where, true);
        $accountId = $data['id'];
        // 获取账户流水统计
        $accInfoFlowStat = $this->modelBkAccount->getAccountFlowStat($accountId);
        $rtnArray = [
            'account_info' => $accountInfo,
            'account_stat' => $accInfoFlowStat,
        ];
        return $rtnArray;
    }


    // 获取账户流水
    // @param array $data[id,book_id,period_type,period_date,]
    public function getBkAccountFlows($data)
    {
        $accountId = $data['id'];

        //时间段
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);


        // 获取分页参数
        $page = isset($data['pageNum']) ? max(1, intval($data['pageNum'])) : 1;
        $limit = isset($data['pageSize']) ? intval($data['pageSize']) : DB_LIST_ROWS;
        $offset = ($page - 1) * $limit;

        // 1. 收支流水（增加分页）
        $txSql = "
    (
        SELECT 
        t.id,t.transaction_date,t.amount,t.currency,c.name category_name,c.category_icon,t.remark,'transaction' flow_type
        FROM " . SYS_DB_PREFIX . "bk_transaction t
        LEFT JOIN " . SYS_DB_PREFIX . "bk_category c ON c.id=t.category_id
        WHERE t.account_id='" . $accountId . "' AND t.transaction_date BETWEEN '" . $start_time . "' AND '" . $end_time . "'
    )
    UNION ALL
    (
        SELECT tf.id,tf.transfer_date,tf.amount,tf.currency,'转账转出' category_name,'' category_icon,tf.remark,'transfer_out' flow_type
        FROM " . SYS_DB_PREFIX . "bk_transfer tf
        WHERE tf.from_account_id='" . $accountId . "' AND tf.transfer_date BETWEEN '" . $start_time . "' AND '" . $end_time . "'
    )
    UNION ALL
    (
         SELECT tf.id,tf.transfer_date,tf.amount,tf.currency,'转账转入' category_name,'' category_icon,tf.remark,'transfer_in' flow_type
         FROM " . SYS_DB_PREFIX . "bk_transfer tf
         WHERE tf.to_account_id='" . $accountId . "' AND tf.transfer_date BETWEEN '" . $start_time . "' AND '" . $end_time . "'
    )
    ORDER BY transaction_date DESC, id DESC
    LIMIT " . $offset . "," . $limit;

        // 获取总数的SQL
        $countSql = "
    SELECT COUNT(*) as total FROM (
        (
            SELECT t.id
            FROM " . SYS_DB_PREFIX . "bk_transaction t
            WHERE t.account_id='" . $accountId . "' AND t.transaction_date BETWEEN '" . $start_time . "' AND '" . $end_time . "'
        )
        UNION ALL
        (
            SELECT tf.id
            FROM " . SYS_DB_PREFIX . "bk_transfer tf
            WHERE tf.from_account_id='" . $accountId . "' AND tf.transfer_date BETWEEN '" . $start_time . "' AND '" . $end_time . "'
        )
        UNION ALL
        (
             SELECT tf.id
             FROM " . SYS_DB_PREFIX . "bk_transfer tf
             WHERE tf.to_account_id='" . $accountId . "' AND tf.transfer_date BETWEEN '" . $start_time . "' AND '" . $end_time . "'
        )
    ) as total_table";

        $list = Db::query($txSql);
        $countResult = Db::query($countSql);
        $total = $countResult[0]['total'];

        $running = 0;
        $flowList = [];
        foreach ($list as $row) {
            $amount = (float)$row['amount'];
            // 根据交易类型处理金额显示和余额计算
            if ($row['flow_type'] === 'transfer_out') {
                // 转出交易，金额显示为负数，余额减少
                $displayAmount = -abs($amount);
                $running -= abs($amount);
            } elseif ($row['flow_type'] === 'transfer_in') {
                // 转入交易，金额显示为正数，余额增加
                $displayAmount = abs($amount);
                $running += abs($amount);
            } else {
                // 普通交易，根据原始金额正负处理
                $displayAmount = $amount;
                $running += ($amount < 0) ? -abs($amount) : abs($amount);
            }

            $flowList[] = [
                'id' => $row['id'],
                'transaction_date' => $row['transaction_date'],
                'amount' => $displayAmount,  // 使用处理后的金额显示
                'balance_after' => $running,
                'category_name' => $row['category_name'],
                'category_icon' => $row['category_icon'],
                'remark' => $row['remark'],
                'flow_type' => $row['flow_type'],
            ];
        }

        // 获取账户明细，流水统计
        $flowListStat = $this->modelBkAccount->getAccountFlowStat($accountId, $start_time, $end_time);
        $rtnArray = [
            'flow_list' => $flowList,
            'flow_stat' => $flowListStat,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $limit,
            'last_page' => ceil($total / $limit)
        ];
        return $rtnArray;
    }


    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where['member_id'] = ['=', MEMBER_ID];
        //关键字查
        !empty($data['keywords']) && $where['name'] = ['like', '%' . $data['keywords'] . '%'];

        //账本负责类型
        if (isset($data['is_debt'])) {
            if (!empty($data['is_debt']) || is_numeric($data['is_debt'])) {
                $where['is_debt'] = ['=', $data['is_debt']];
            }
        }

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
            switch ($orderField) {
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
}
