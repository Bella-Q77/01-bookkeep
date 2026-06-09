<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * CstLinkmanor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\controller\admin;

/**
 * 点评管理-控制器
 */
class AskComment extends MemberAdminBase
{
    /**
     * 点评列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if (!empty($this->param['listtype'])) {
            $this->assign('listtype', $this->param['listtype']);
        } else {
            $this->assign('listtype', 'selfson');
        }
        $this->common_data();
        return $this->fetch('show');
    }

    /**
     * 点评列表=》模板=>我点评的
     * @return mixed|string
     */
    public function show_comment()
    {
        if (!empty($this->param['listtype'])) {
            $this->assign('listtype', $this->param['listtype']);
        } else {
            $this->assign('listtype', 'comment');
        }
        $this->common_data();
        return $this->fetch('show_comment');
    }

    /**
     * 点评列表=》模板=>我被点评的
     * @return mixed|string
     */
    public function show_receive()
    {
        if (!empty($this->param['listtype'])) {
            $this->assign('listtype', $this->param['listtype']);
        } else {
            $this->assign('listtype', 'receive');
        }
        $this->common_data();
        return $this->fetch('show_receive');
    }

    /**
     * 点评列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicAskComment->getWhere($this->param);
        $orderby = $this->logicAskComment->getOrderby($this->param);
        $list = $this->logicAskComment->getAskCommentList($where, '*', $orderby);
        return $list;
    }

    /**
     * 点评添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicAskComment->askCommentAdd($this->param));
        $this->common_data();
        return $this->fetch('add');
    }

    /**
     * 点评编辑
     * @return mixed|string
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicAskComment->askCommentEdit($this->param));

        $this->common_data();

        $info = $this->logicAskComment->getAskCommentInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 点评删除
     */
    public function del()
    {
        $this->jump($this->logicAskComment->askCommentDel($this->param));
    }


    /**
     * 公共数据
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/6/20 0020
     */
    public function common_data()
    {
        if (!empty($this->param['receive_user_id'])) {
            $this->assign('receive_user_id', $this->param['receive_user_id']);
        } else {
            $this->assign('receive_user_id', '0');
        }
        $this->assign('param', $this->param);
    }
}
