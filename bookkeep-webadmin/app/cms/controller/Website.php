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
* 网站配置-控制器
*/

class Website extends CmsBase
{


    public function comm_data(){
        $group=$this->logicWebsite->getWebsiteGroup();
        $this->assign('group_list', $group);

        $type=$this->logicWebsite->getWebsiteType();
        $this->assign('type_list', $type);
    }

    /**
     * 网站配置列表=》模板
     * @return mixed|string
     */
    public function setting()
    {

        IS_POST && $this->jump($this->logicWebsite->setting($this->param));

        if (!empty($this->param['groupid'])) {
            $this->assign('groupid',$this->param['groupid']);
        }else{
            $this->assign('groupid',1);
        }
        $html=$this->logicWebsite->getWebsiteInfoHtml($this->param);

        $this->assign('html', $html);

        $this->comm_data();

        return $this->fetch('setting');
    }

    /**
     * 网站配置列表-》json数据
     * @return
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 网站配置列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['title|intro'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        $list = $this->logicWebsite->getWebsiteList($where);
        return $list;
    }


    /**
     * 网站配置添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicWebsite->websiteAdd($this->param));

        $this->comm_data();

        return $this->fetch('add');
    }

    /**
     * 网站配置编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicWebsite->websiteEdit($this->param));

        $info = $this->logicWebsite->getWebsiteInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);

        $this->comm_data();

        return $this->fetch('edit');
    }

    /**
     * 网站配置删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicWebsite->adsDel($where));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('Website', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('Website', $this->param));
    }

}
