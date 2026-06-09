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
* 广告列表-控制器
*/

class AdsList extends CmsBase
{

    /**
     * 列表
     */
    public function show()
    {
        if (!empty($this->param['id'])) {
            $this->assign('ads_id', $this->param['id']);
        }else{
            $this->assign('ads_id', '0');
        }

        $ads_list = $this->logicAds->getAdsList('', '', '',false);

        $this->assign('ads_list', $ads_list);

        return $this->fetch('show');
    }

    /**
     * 列表json数据
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['ads_id'])) {
            $where['ads_id'] = ['=', $this->param['ads_id']];
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

        $list = $this->logicAdsList->getAdsListList($where, true, $order_by);
        return $list;
    }


    /**
     * 添加
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicAdsList->adsListAdd($this->param));

        if (!empty($this->param['ads_id'])) {
            $this->assign('ads_id', $this->param['ads_id']);
        }else{
            $this->assign('ads_id', '0');
        }

        return $this->fetch('add');
    }

    /**
     * 编辑
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicAdsList->adsListEdit($this->param));

        $info = $this->logicAdsList->getAdsListInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 数据状态设置
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicAdsList->adsListDel($where));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('AdsList', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('AdsList', $this->param));
    }
}
