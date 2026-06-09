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
* 内容发布系统 留言表单控制器
*/

class Guestbook extends CmsBase
{

    /**
     * 列表
     */
    public function show()
    {

        if(IS_POST){
            $where=[];
            if (!empty($this->param['keywords'])) {
                $where['name'] = ['like', '%' . $this->param['keywords'] . '%'];
            }
            $order_by=$this->logicGuestbook->getOrderby($this->param);
            $list = $this->logicGuestbook->getGuestbookList($where, true, $order_by);
            return $list;
        }

        return $this->fetch('show');
    }

    /**
     * 添加
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicGuestbook->guestbookAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 编辑
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicGuestbook->guestbookEdit($this->param));

        $info = $this->logicGuestbook->getGuestbookInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 数据状态设置
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicGuestbook->guestbookDel($where));
    }



    /**
     * 扩展表单详细列表
     */
    public function ext_list()
    {
        if(IS_POST){
            $list = $this->logicGuestbook->getGuestbookExtList($this->param);
            return $list;
        }
        if (!empty($this->param['gid'])) {
            $this->assign('gid',$this->param['gid']);
        }else{
            $this->jump([RESULT_ERROR,'选择表单']);
        }
        return $this->fetch('ext_list');
    }

    /**
     * 扩展表单详细列表
     */
    public function ext_list_down()
    {
        $this->logicGuestbook->getGuestbookExtListDown($this->param);
    }

    /**
     * 编辑
     */
    public function ext_reply()
    {
        IS_POST && $this->jump($this->logicGuestbook->guestbookExtReply($this->param));

        $info = $this->logicGuestbook->getGuestbookExtInfo( $this->param);

        $this->assign('info', $info);

        return $this->fetch('ext_reply');
    }

    /**
     * 数据状态设置
     */
    public function ext_del()
    {
        $this->jump($this->logicGuestbook->guestbookExtDel($this->param));
    }
}
