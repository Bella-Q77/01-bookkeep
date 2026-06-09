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

/**
* 友情链接管理-控制器
*/

class Friendlink extends CmsBase
{

    /**
     * 友情链接列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 友情链接列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['title|intro'] = ['like', '%' . $this->param['keywords'] . '%'];
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
        $list = $this->logicFriendlink->getFriendlinkList($where,true,$order_by);
        return $list;
    }


    /**
     * 友情链接添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicFriendlink->friendlinkAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 友情链接编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicFriendlink->friendlinkEdit($this->param));

        $info = $this->logicFriendlink->getFriendlinkInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 友情链接删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicFriendlink->friendlinkDel($where));
    }
    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('Friendlink', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('Friendlink', $this->param));
    }
}
