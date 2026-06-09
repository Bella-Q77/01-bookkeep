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
class BkAccount extends PortalmemberBookkeepApiBase
{

    public function getAccountType()
    {
        $info = $this->logicBkAccount->getAccountType();
        return $this->apiReturn($info);
    }

    public function getAccountData()
    {
        $info = $this->logicBkAccount->getBkAccountData($this->param);
        return $this->apiReturn($info);
    }

    public function getInfo()
    {
        $info = $this->logicBkAccount->getBkAccountInfo(['id' => $this->param['id']]);
        return $this->apiReturn($info);
    }

    public function getDetail()
    {
        $info = $this->logicBkAccount->getBkAccountDetail($this->param);
        return $this->apiReturn($info);
    }

    public function getFlows()
    {
        $info = $this->logicBkAccount->getBkAccountFlows($this->param);
        return $this->apiReturn($info);
    }

    public function getList()
    {
        $where = $this->logicBkAccount->getWhere($this->param);
        $list = $this->logicBkAccount->getBkAccountList($where);
        return $this->apiReturn($list);
    }

    public function add()
    {
        $result = $this->logicBkAccount->bkAccountAdd($this->param);
        return $this->apiReturn($result);
    }

    public function edit()
    {
        $result = $this->logicBkAccount->bkAccountEdit($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 账本删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $result = $this->logicBkAccount->bkAccountDel($where);
        return $this->apiReturn($result);

    }
}
