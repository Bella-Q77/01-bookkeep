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
* 收支分类管理-控制器
*/

class BkCategory extends PortalmemberBookkeepAuth
{

    /**
     * 收支分类列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if(!empty($this->param['catetype'])){
            $this->assign('catetype',$this->param['catetype']);
        }else{
            $this->assign('catetype','income');
        }
        return $this->fetch('show');
    }

    /**
     * 收支分类列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicBkCategory->getWhere($this->param);
        $list = $this->logicBkCategory->getBkCategoryList($where);
        return $list;
    }

    /**
     * 收支分类添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicBkCategory->bkCategoryAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 收支分类编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicBkCategory->bkCategoryEdit($this->param));

        $info = $this->logicBkCategory->getBkCategoryInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 收支分类删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkCategory->bkCategoryDel($where));
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
