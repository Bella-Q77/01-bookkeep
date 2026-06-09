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

namespace app\portalmember\controller\bookkeep;

/**
 * 预算管理-控制器
 */
class BkBudget extends PortalmemberBookkeepAuth
{
    /**
     * 预算列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if (IS_POST) {
            $data = $this->logicBkBudget->getBkBudgetData($this->param);
            $data = $this->jump($data);
            return $data;
        }
        return $this->fetch('show');
    }


    /**
     * 预算添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicBkBudget->bkBudgetAdd($this->param));
        $this->assign('period_type', $this->param['period_type']);
        $info=$this->logicBkBudget->getBkBudgetInfo($this->param);
        $this->assign('info', $info);
        return $this->fetch('add');
    }

    public function addCategory()
    {
        IS_POST && $this->jump($this->logicBkBudget->bkBudgetAddCategory($this->param));
        $this->assign('period_type', $this->param['period_type']);
        $info=$this->logicBkBudget->getBkBudgetInfo($this->param);
        $this->assign('info', $info);
        return $this->fetch('add_category');
    }

    /**
     * 预算编辑
     * @return mixed|string
     */
    public function edit()
    {
        IS_POST && $this->jump($this->logicBkBudget->bkBudgetEdit($this->param));
        $info = $this->logicBkBudget->getBkBudgetInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        return $this->fetch('edit');
    }

    /**
     * 预算删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkBudget->bkBudgetDel($where));
    }
}
