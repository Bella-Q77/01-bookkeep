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

namespace app\cms\controller\info;

use app\cms\controller\CmsBase;
use think\db;

/**
 * 内容发布系统-栏目-控制器
 */
class InfoType extends CmsBase
{

    /**
     * 列表
     */
    public function show()
    {
        if (!empty($this->param['pid'])) {
            $this->assign('pid', $this->param['pid']);
        } else {
            $this->assign('pid', 0);
        }
        return $this->fetch('show');
    }

    /**
     * 列表
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['typename'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        if (!empty($this->param['pid'])) {
            $where['parent_id'] = ['in', $this->param['pid']];
        } else {
            $where['parent_id'] = ['in', '0'];
        }
        $list = $this->logicInfoType->getInfoTypeList($where);
        return $list;
    }

    /**
     * 详细
     */
    public function info()
    {
        $info = $this->logicInfoType->getInfoTypeInfo(['id' => $this->param['id']]);
        return $info;
    }

    public function get_list_tree()
    {
        $tree = $this->logicInfoType->getInfoTypeListTree();
        return $tree;
    }

    /**
     * 添加
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicInfoType->infoTypeAdd($this->param));
        $this->comm();
        if (!empty($this->param['pid'])) {
            $this->assign('pid', $this->param['pid']);
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

        IS_POST && $this->jump($this->logicInfoType->infoTypeEdit($this->param));

        $info = $this->logicInfoType->getInfoTypeInfo(['id' => $this->param['id']]);
        if (empty($info)) {
            $this->jump([RESULT_ERROR, 'id参数出错', url('infoType/show')]);
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

        IS_POST && $this->jump($this->logicInfoType->infoTypeEdit($this->param));

        $info = $this->logicInfoType->getInfoTypeInfo(['id' => $this->param['id']]);
        if (empty($info)) {
            $this->jump([RESULT_ERROR, 'id参数出错', url('infoType/show')]);
        }
        $this->assign('info', $info);
        $this->comm();
        return $this->fetch('edit_content');
    }

    /**
     * 删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicInfoType->infoTypeDel($where));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('InfoType', $this->param));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('InfoType', $this->param));
    }

    public function comm()
    {
        $info_type_list = $this->logicInfoType->getInfoTypeListSelect();
        $this->assign('info_type_list', $info_type_list);
    }

}
