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

/**
* 信息管理-控制器
*/

class Info extends CmsBase
{

    /**
     * 构造方法
     */
    public function __construct()
    {

        // 执行父类构造方法
        parent::__construct();

        $status_list=$this->logicInfo->getStatus();
        $this->assign('status_list', $status_list);

        $istop_list=$this->logicInfo->getIstop();
        $this->assign('istop_list', $istop_list);

    }
    /**
     * 信息列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 信息列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['title|intro'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        $list = $this->logicInfo->getInfoList($where);
        return $list;
    }


    /**
     * 信息添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicInfo->infoAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 信息编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicInfo->infoEdit($this->param));

        $info = $this->logicInfo->getInfoInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 信息删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicInfo->infoDel($where));
    }


    /**
     * 详细
     * @return mixed|string
     */

    public function detail()
    {

        $info = $this->logicInfo->getInfoInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('detail');
    }

    /**
     * 审核
     */
    public function pass()
    {
        $this->jump($this->logicInfo->infoAudit($this->param));
    }

    /**
     * 审核
     */
    public function reject()
    {
        IS_POST && $this->jump($this->logicInfo->infoAudit($this->param));
        $info = $this->logicInfo->getInfoInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        return $this->fetch('reject');
    }

}
