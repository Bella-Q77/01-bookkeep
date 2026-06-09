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
 * 收支记录管理-控制器
 */
class BkTransaction extends BookkeepBase
{

    /**
     * 收支记录列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if (!empty($this->param['catetype'])) {
            $this->assign('catetype', $this->param['catetype']);
        } else {
            $this->assign('catetype', 'income');
        }
        return $this->fetch('show');
    }

    /**
     * 收支记录列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicBkTransaction->getWhere($this->param);
        $list = $this->logicBkTransaction->getBkTransactionList($where);
        return $list;
    }

    /**
     * 收支记录添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicBkTransaction->bkTransactionAdd($this->param));
        //历史记录
        $history = session('history_transaction');
        $this->assign('history', $history);
        return $this->fetch('add');
    }

    public function addTab()
    {
        IS_POST && $this->jump($this->logicBkTransaction->bkTransactionAdd($this->param));

        //历史记录
        $history = session('history_transaction');
        $this->assign('history', $history);
        return $this->fetch('add_tab');
    }

    public function getAddLists()
    {
        $list = $this->logicBkTransaction->getBkTransactionAddList($this->param);
        return $list;
    }

    /**
     * 收支记录编辑
     * @return mixed|string
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicBkTransaction->bkTransactionEdit($this->param));

        $info = $this->logicBkTransaction->getBkTransactionInfo(['a.id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 收支记录删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkTransaction->bkTransactionDel($where));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBkTransaction->setField('BkTransaction', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBkTransaction->setSort('BkTransaction', $this->param));
    }

    public function set_default()
    {
        $this->jump($this->logicBkTransaction->setBkTransactionDefault($this->param));
    }
}
