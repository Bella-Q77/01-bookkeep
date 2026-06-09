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
 * 项目管理-控制器
 */
class BkProject extends BookkeepBase
{
    public function __construct()
    {
        parent::__construct();
        if (!empty($this->param['book_id'])) {
            $this->assign('book_id', $this->param['book_id']);
        } else {
            $this->assign('book_id', '');
        }
    }

    /**
     * 项目列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 项目列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicBkProject->getWhere($this->param);
        $list = $this->logicBkProject->getBkProjectList($where);
        return $list;
    }


    /**
     * 项目添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicBkProject->bkProjectAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 项目编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicBkProject->bkProjectEdit($this->param));

        $info = $this->logicBkProject->getBkProjectInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 项目删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkProject->bkProjectDel($where));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBookkeepBase->setField('BkProject', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBookkeepBase->setSort('BkProject', $this->param));
    }

}