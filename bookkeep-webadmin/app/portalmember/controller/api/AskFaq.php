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
 * 反馈问题控制器
 */
class AskFaq extends PortalmemberBookkeepApiBase
{
    public function getInfo()
    {
        $info = $this->logicAskFaq->getAskFaqInfo(['id' => $this->param['id']]);
        return $this->apiReturn($info);
    }

    //
    public function getList()
    {
        $where = $this->logicAskFaq->getWhere($this->param);
        $orderby = $this->logicAskFaq->getOrderby($this->param);
        $list = $this->logicAskFaq->getAskFaqList($where, 'a.*', $orderby);
        return $this->apiReturn($list);
    }

    public function add()
    {
        $result = $this->logicAskFaq->askFaqAdd($this->param);
        return $this->apiReturn($result);
    }

    public function edit()
    {
        $result = $this->logicAskFaq->askFaqEdit($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $result = $this->logicAskFaq->askFaqDel($where);
        return $this->apiReturn($result);
    }
}
