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
 * 内容发布系统-栏目-控制器
 */
class Arctype extends CmsBase
{

    /**
     * 列表
     */
    public function show()
    {
        $listtree = $this->logicArctype->getArctypeListTree($where = '');
//        $listtree = $this->logicArctype->getArctypeListHtml($listtree);
//        $this->assign('listtree', $listtree);
        return $this->fetch('show');
    }

    /**
     * 列表
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['name'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        if (!empty($this->param['pid'])) {
            //$ids=$this->logicSysDept->getDeptAllSon($this->param['pid']);
            $where['parent_id'] = ['in', $this->param['pid']];
        } else {
            $where['parent_id'] = ['in', '0'];
        }
        $list = $this->logicArctype->getArctypeList($where);
        return $list;
    }

    /**
     * 详细
     */
    public function info()
    {
        $info = $this->logicArctype->getArctypeInfo(['id' => $this->param['id']]);
        return $info;
    }

    public function get_list_tree()
    {
        $tree = $this->logicArctype->getArctypeListTree();
        return $tree;
    }

    /**
     * 添加
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicArctype->arctypeAdd($this->param));
        $this->comm();

        //判断是否有上级菜单
        if (!empty($this->param['id'])) {
            $this->assign('pid', $this->param['id']);
        } else {
            $this->assign('pid', 0);
        }
        return $this->fetch('add');
    }

    /**
     * 编辑
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicArctype->arctypeEdit($this->param));

        $info = $this->logicArctype->getArctypeInfo(['id' => $this->param['id']]);
        if (empty($info)) {
            $this->jump([RESULT_ERROR, 'id参数出错', url('arctype/show')]);
        }
        $this->assign('info', $info);
        $this->comm();
        return $this->fetch('edit');
    }

    /**
     * 编辑内容
     */
    public function edit_content()
    {

        IS_POST && $this->jump($this->logicArctype->arctypeEdit($this->param));

        $info = $this->logicArctype->getArctypeInfo(['id' => $this->param['id']]);
        if (empty($info)) {
            $this->jump([RESULT_ERROR, 'id参数出错', url('arctype/show')]);
        }
        $this->assign('info', $info);
        $this->comm();
        return $this->fetch('edit_content');
    }

    /*
     * 修改模板
     */
    public function edit_moban()
    {
        IS_POST && $this->jump($this->logicArctype->arctypeEditMoban($this->param));
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => ['in', $this->param['id']]];
        $list = $this->logicArctype->getArctypeList($where);
        $this->assign('list', $list);
        $this->comm();
        return $this->fetch('edit_moban');
    }

    /**
     * 删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicArctype->arctypeDel($where));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('Arctype', $this->param));
    }

    /**
     * 启用
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('Arctype', $this->param));
    }

    public function comm()
    {
        $channellist = $this->logicChannel->getChannelList('', '', '', false);
        $listtree = $this->logicArctype->getArctypeListTree($where = '');
        $arctypelist = $this->logicArctype->getArctypeListSelect($listtree);
        $this->assign('channellist', $channellist);
        $this->assign('arctypelist', $arctypelist);
    }

}
