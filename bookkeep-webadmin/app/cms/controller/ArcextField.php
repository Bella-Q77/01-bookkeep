<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/
namespace app\cms\controller;

use think\db;

/**
* 内容发布系统-频道-扩展字段-控制器
*/

class ArcextField extends CmsBase
{

    /**
     * 列表
     */
    public function show()
    {
        $info = $this->logicArcext->getArcextInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('show');
    }

    /**
     * 列表json数据
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['main_table'])) {
            $where['main_table'] = ['=', $this->param['main_table']];
        }
        if (!empty($this->param['ext_table'])) {
            $where['ext_table'] = ['=', $this->param['ext_table']];
        }
        if (!empty($this->param['arcext_id'])) {
            $where['arcext_id'] = ['=', $this->param['arcext_id']];
        }
        //排序操作
        if (!empty($this->param['orderField'])) {
            $orderField = $this->param['orderField'];
            $orderDirection = $this->param['orderDirection'];
        } else {
            $orderField = "";
            $orderDirection = "";
        }
        if ($orderField == 'by_sort') {
            $order_by = "sort $orderDirection";
        } else {
            $order_by = "sort asc";
        }
        $list = $this->logicArcextField->getArcextFieldList($where, true, $order_by);
        return $list;
    }


    /**
     * 添加
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicArcextField->arcextFieldAdd($this->param));
        $info = $this->logicArcext->getArcextInfo(['id' => $this->param['channel_id']]);
        $this->assign('info', $info);
        $this->assign('typelist', $this->logicArcextField->getArcextTypeList());
        return $this->fetch('add');
    }

    /**
     * 编辑
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicArcextField->arcextFieldEdit($this->param));

        $info = $this->logicArcextField->getArcextFieldInfo(['id' => $this->param['id']]);
        $this->assign('typelist', $this->logicArcextField->getArcextTypeList());
        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 数据状态设置
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicArcextField->arcextFieldDel($where));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('ArcextField', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('ArcextField', $this->param));
    }
}
