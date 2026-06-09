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
class BkCategory extends PortalmemberBookkeepApiBase
{
    public function getInfo()
    {
        $info = $this->logicBkCategory->getBkCategoryInfo(['id' => $this->param['id']]);
        return $this->apiReturn($info);
    }

    public function getList()
    {
        $where = $this->logicBkCategory->getWhere($this->param);
        $list = $this->logicBkCategory->getBkCategoryList($where, '*', 'sort', false);
        return $this->apiReturn($list);
    }

    public function add()
    {
        $result = $this->logicBkCategory->bkCategoryAdd($this->param);
        return $this->apiReturn($result);
    }
    public function edit()
    {
        $result = $this->logicBkCategory->bkCategoryEdit($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 账本删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $result = $this->logicBkCategory->bkCategoryDel($where);
        return $this->apiReturn($result);

    }

    // 账本切换,
    public function change()
    {
        $result = $this->logicBkCategory->setBkCategoryDefault($this->param);
        return $this->apiReturn($result);
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBkCategory->setField('BkCategory', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBkCategory->setSort('BkCategory', $this->param));
    }
}
