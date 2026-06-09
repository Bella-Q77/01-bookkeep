<?php
/*
*
* 零起飞进销系统（07FLY-DMS）
*
* ----------------------------------------------
* 零起飞网络 - 专注于企业管理系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD All rights reserved.
* @license    For licensing, see LICENSE.html
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\bookkeep\controller;

/**
 * 账本管理管理-控制器
 */
class BkBook extends BookkeepBase
{

    /**
     * 账本管理列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 账本管理列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicBkBook->getWhere($this->param);
        $list = $this->logicBkBook->getBkBookList($where);
        return $list;
    }


    /**
     * 账本管理添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicBkBook->bkBookAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 账本管理编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicBkBook->bkBookEdit($this->param));

        $info = $this->logicBkBook->getBkBookInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 账本管理删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkBook->bkBookDel($where));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBookkeepBase->setField('BkBook', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBookkeepBase->setSort('BkBook', $this->param));
    }

}
