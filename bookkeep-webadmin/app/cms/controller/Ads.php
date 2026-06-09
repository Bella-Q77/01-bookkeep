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
* 广告管理-控制器
*/

class Ads extends CmsBase
{

    /**
     * 广告列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 广告列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['title|intro'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        $list = $this->logicAds->getAdsList($where);
        return $list;
    }


    /**
     * 广告添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicAds->adsAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 广告编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicAds->adsEdit($this->param));

        $info = $this->logicAds->getAdsInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 广告删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicAds->adsDel($where));
    }

}
