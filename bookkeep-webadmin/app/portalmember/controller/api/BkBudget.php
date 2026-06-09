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
 * 账本控制器
 */
class BkBudget extends PortalmemberBookkeepApiBase
{

    // 获取预算数据
    public function getBudgetData()
    {
        $info = $this->logicBkBudget->getBkBudgetData($this->param);
        return $this->apiReturn($info);
    }

    // 默认预算添加
    public function add()
    {
        $result = $this->logicBkBudget->bkBudgetAdd($this->param);
        return $this->apiReturn($result);
    }

    // 预算类别添加
    public function addCategory()
    {
        $result = $this->logicBkBudget->bkBudgetAddCategory($this->param);
        return $this->apiReturn($result);
    }

    // 预算类别编辑
    public function editCategory()
    {
        $result = $this->logicBkBudget->bkBudgetEdit($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 预算删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $result = $this->logicBkBudget->bkBudgetDel($where);
        return $this->apiReturn($result);

    }
}
