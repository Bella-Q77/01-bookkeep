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
* 内容发布系统-频道模型控制器
*/

class Channel extends CmsBase
{

    /**
     * 列表
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 列表json数据
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['name|remark'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        //排序操作
        //排序操作
        if (!empty($this->param['orderField'])) {
            $orderField = $this->param['orderField'];
            $orderDirection = $this->param['orderDirection'];
        } else {
            $orderField = "";
            $orderDirection = "";
        }
        if ($orderField == 'by_name') {
            $order_by = "name $orderDirection";
        } else if ($orderField == 'by_mobile') {
            $order_by = "mobile $orderDirection";
        } else if ($orderField == 'by_linkman') {
            $order_by = "linkman $orderDirection";
        } else if ($orderField == 'by_tel') {
            $order_by = "tel $orderDirection";
        } else if ($orderField == 'by_sort') {
            $order_by = "sort $orderDirection";
        } else {
            $order_by = "sort asc";
        }
        $list = $this->logicChannel->getChannelList($where, true, $order_by);
        return $list;
    }


    /**
     * 添加
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicChannel->channelAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 编辑
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicChannel->channelEdit($this->param));

        $info = $this->logicChannel->getChannelInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 数据状态设置
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicChannel->channelDel($where));
    }

    /**
     * 排序
     */
    public function setSort()
    {
        $this->jump($this->logicLtasBase->setSort('Channel', $this->param));
    }
}
