<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberRealnameor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\member\controller\admin;

/**
* 会员等级升级管理-控制器
*/

class MemberRealname extends MemberAdminBase
{

    /**
     * 会员等级升级列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        $this->comm_data();
        return $this->fetch('show');
    }

    /**
     * 会员等级升级列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where=$this->logicMemberRealname->getWhere($this->param);
        $list = $this->logicMemberRealname->getMemberRealnameList($where);
        return $list;
    }


    /**
     * 会员等级升级添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicMemberRealname->memberRealnameAdd($this->param));
        $this->comm_data();
        return $this->fetch('add');
    }

    /**
     * 会员等级升级编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicMemberRealname->memberRealnameEdit($this->param));

        $info = $this->logicMemberRealname->getMemberRealnameInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $this->comm_data();
        return $this->fetch('edit');
    }

    /**
     * 会员等级升级删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicMemberRealname->memberRealnameDel($where));
    }

    /**
     * 审核
     */
    public function pass()
    {
        $this->jump($this->logicMemberRealname->memberRealnameAudit($this->param));
    }
    /**
     * 审核
     */
    public function reject()
    {
        IS_POST && $this->jump($this->logicMemberRealname->memberRealnameAudit($this->param));
        $info = $this->logicMemberRealname->getMemberRealnameInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $this->comm_data();
        return $this->fetch('reject');
    }

    /**
     *加载公共参数
     */
    public function comm_data(){
        $status_list= $this->logicMemberRealname->getStatus();
        $this->assign('status_list', $status_list);

        $real_type_list= $this->logicMemberRealname->getRealType();
        $this->assign('real_type_list', $real_type_list);

    }

}
