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
class BkBook extends PortalmemberBookkeepApiBase
{

    // 获取当前账本信息
    public function getCurrentBook()
    {

    }

    public function getInfo()
    {
        $info = $this->logicBkBook->getBkBookInfo(['id' => $this->param['id']]);
        return $this->apiReturn($info);
    }

    public function getList()
    {
        $where = $this->logicBkBook->getWhere($this->param);
        $list = $this->logicBkBook->getBkBookList($where, '*', 'sort', false);
        return $this->apiReturn($list);
    }

    public function add()
    {
        $result = $this->logicBkBook->bkBookAdd($this->param);
        return $this->apiReturn($result);
    }

    public function edit()
    {
        $result = $this->logicBkBook->bkBookEdit($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 账本删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $result = $this->logicBkBook->bkBookDel($where);
        return $this->apiReturn($result);

    }

    // 账本切换,
    public function change()
    {
        $result = $this->logicBkBook->setBkBookDefault($this->param);
        return $this->apiReturn($result);
    }
}
