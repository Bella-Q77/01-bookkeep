<?php
// +----------------------------------------------------------------------
// | 07FLYERP [基于ThinkPHP5.0开发]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2021 http://www.07fly.xyz
// +----------------------------------------------------------------------
// | Professional because of focus  Persevering because of happiness
// +----------------------------------------------------------------------
// | Author: 开发人生 <goodkfrs@qq.com>
// +----------------------------------------------------------------------

namespace app\portalmember\controller\api;

use think\Hook;

/**
 * 后台基类控制器
 */
class BkTransaction extends PortalmemberBookkeepApiBase
{
    public function getList()
    {
        $where = $this->logicBkTransaction->getWhere($this->param);
        $result = $this->logicBkTransaction->getBkTransactionList($where);
        return $this->apiReturn($result);
    }

    public function getMonthSummary()
    {
        $where = $this->logicBkTransaction->getWhere($this->param);
        $total = $this->logicBkTransaction->getBkTransactionListTotal($where);
        $result = ['code' => 1, 'msg' => '数据返回成功！', 'data' => $total];
        return $this->apiReturn($result);
    }

    public function getInfo()
    {
        $result = $this->logicBkTransaction->getBkTransactionInfo(['a.id' => $this->param['id']]);
        return $this->apiReturn($result);
    }

    public function add()
    {
        $result = $this->logicBkTransaction->bkTransactionAdd($this->param);
        return $this->apiReturn($result);
    }

    public function edit()
    {
        $result = $this->logicBkTransaction->bkTransactionEdit($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 收支记录删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $result = $this->logicBkTransaction->bkTransactionDel($where);
        return $this->apiReturn($result);
    }

}
