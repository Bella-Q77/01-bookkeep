<?php
/*
*
* cms.Archives  内容发布系统 留言表单
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
* 内容发布系统 文章扩展项目控制器
*/

class ArcextInfo extends CmsBase
{

    /**
     * 公共数据
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/7/1 0001
     */
    public function  common_data(){
        if (!empty($this->param['channel_id'])) {
            $this->assign('channel_id', $this->param['channel_id']);
        }else{
            $this->assign('channel_id',0);
        }
        if (!empty($this->param['archives_id'])) {
            $this->assign('archives_id', $this->param['archives_id']);
        }else{
            $this->assign('archives_id',0);
        }
        if (!empty($this->param['arcext_id'])) {
            $this->assign('arcext_id', $this->param['arcext_id']);
        }else{
            $this->assign('arcext_id',0);
        }
    }

    //扩展表信息列表
    public function show(){
        $this->common_data();
        //查询文章模块扩展项目
        if (!empty($this->param['channel_id'])) {
            $where['channel_id'] = ['=',$this->param['channel_id']];
        }
        $item_list = $this->logicArcext->getArcextList($where, true, 'sort asc ',false);
        $this->assign('item_list',$item_list);
        return $this->fetch('show');
    }

    /**
     * 扩展表信息列表
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/7/1 0001
     */
    public function show_json(){
        return $this->logicArcextInfo->getArcextInfoList($this->param);
    }

    /**
     * 编辑
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicArcextInfo->arcextInfoAdd($this->param));
        $this->common_data();
        return $this->fetch('add');
    }

    /**
     * 编辑
     */
    public function edit()
    {
        IS_POST && $this->jump($this->logicArcextInfo->arcextInfoEdit($this->param));
        $info = $this->logicArcextInfo->getArcextInfoInfo( $this->param);
        $this->assign('info', $info);
        return $this->fetch('edit');
    }

    /**
     * 删除
     */
    public function del()
    {
        $this->jump($this->logicArcextInfo->arcextInfoDel($this->param));
    }

}
