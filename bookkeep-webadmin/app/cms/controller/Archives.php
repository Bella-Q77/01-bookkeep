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
* 内容发布系统-内容管理-控制器
*/

class Archives extends CmsBase
{
    /**
     * 列表
     */
    public function show()
    {
        //请求页左边栏目数据
        if (!empty($this->param['data_type'])) {
            if($this->param['data_type']=='get_arctype_tree'){
                $where=[];
                $listtree=$this->logicArctype->getArctypeListTree($where);
                return $listtree;
            }else if($this->param['data_type']=='get_arctype_info'){
                $info = $this->logicArctype->getArctypeInfo(['id' => $this->param['id']]);
                return $info;
            }
        }
        $this->comm();
        return $this->fetch('show');
    }

    /**
     * 列表json数据
     */
    public function show_json()
    {
        $where      =$this->logicArchives->getWhere($this->param);
        $order_by  =$this->logicArchives->getOrderBy($this->param);
        $list = $this->logicArchives->getArchivesList($where, 'a.*,t.typename', $order_by);
        return $list;
    }

    /**
     * 添加
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicArchives->archivesAdd($this->param));
        $this->comm();
        if(empty($this->param['type_id'])){
            return $this->fetch('typelist');
            $this->jump([RESULT_SUCCESS, '选择左侧栏目',url('archives/show')]);
        }else{
            $arctype=$this->logicArctype->getArctypeInfoDetail($this->param['type_id']);
            $ext_field= $this->logicChannelField->channelExtFieldHtml($arctype['addtable']);
            $checkbox= $this->logicArcatt->getArcattCheckbox('flag');//属性
            $this->assign('arcatt_checkbox_html', $checkbox);
            $this->assign('ext_field', $ext_field);
            $this->assign('type_id', $this->param['type_id']);
        }
        return $this->fetch('add');
    }
    /**
     * 编辑
     */
    public function edit()
    {
        IS_POST && $this->jump($this->logicArchives->archivesEdit($this->param));
        $info = $this->logicArchives->getArchivesInfo(['id' => $this->param['id']]);
        if(empty($info)){
            $this->jump( [RESULT_ERROR, 'id参数出错',url('archives/show')]);
        }
        //栏目信息
        $channel=$this->logicChannel->getChannelInfo(['id'=>$info['channel_id']],'addtable');
        $ext_field= $this->logicChannelField->channelExtFieldHtml($channel['addtable'],$info);

        $checkbox= $this->logicArcatt->getArcattCheckbox('flag',$info['flag']);//属性
        $this->assign('arcatt_checkbox_html', $checkbox);
        $this->assign('info', $info);
        $this->assign('ext_field', $ext_field);
        $this->comm();
        return $this->fetch('edit');
    }

    /**
     * 移动栏目
     */
    public function move()
    {
        IS_POST && $this->jump($this->logicArchives->archivesMove($this->param));
        if (!empty($this->param['id'])) {
            $this->assign('id', $this->param['id']);
        }
        $this->comm();
        return $this->fetch('move');
    }
    public function visible()
    {
        IS_POST && $this->jump($this->logicArchives->archivesVisible($this->param));
        if (!empty($this->param['id'])) {
            $this->assign('id', $this->param['id']);
        }
        $this->comm();
        return $this->fetch('move');
    }

    /**
     * 删除
     */
    public function del()
    {
        $this->jump($this->logicArchives->archivesDel($this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicCmsBase->setSort('Archives', $this->param));
    }
    /**
     * 启用
     */
    public function set_visible()
    {
        $this->jump($this->logicCmsBase->setField('Archives', $this->param));
    }
    /**
     *加载公共参数
     */
    public function comm(){
        //l加载下拉栏目
        $listtree= $this->logicArctype->getArctypeListTree($where='');
        $arctypelist= $this->logicArctype->getArctypeListSelect($listtree);
        $this->assign('arctypelist', $arctypelist);

        $sys_area_list= $this->logicSysArea->getSysAreaTreeSelect();
        $this->assign('sys_area_list', $sys_area_list);
    }
}
