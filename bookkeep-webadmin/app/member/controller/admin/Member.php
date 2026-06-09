<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Memberor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\member\controller\admin;

/**
* 会员列表管理-控制器
*/

class Member extends MemberAdminBase
{
    /**
     * 会员列表列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 会员列表列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where=$this->logicMember->getWhere($this->param);
        $list = $this->logicMember->getMemberList($where);
        return $list;
    }


    /**
     * 会员列表添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicMember->memberAdd($this->param));
        $this->comm_data();
        return $this->fetch('add');
    }

    /**
     * 会员列表编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicMember->memberEdit($this->param));

        $info = $this->logicMember->getMemberInfo(['id' => $this->param['id']]);
        $this->comm_data();
        $this->assign('info', $info);
        return $this->fetch('edit');
    }

    /**
     * 会员列表删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicMember->memberDel($where));
    }

    /**
     * 会员列表编辑
     * @return mixed|string
     */

    public function detail()
    {
        $info = $this->logicMember->getMemberInfo(['id' => $this->param['id']]);
        $this->comm_data();
        $this->assign('info', $info);
        return $this->fetch('detail');
    }


    /**
     * 会员列表编辑
     * @return mixed|string
     */

    public function resetpwd()
    {

        IS_POST && $this->jump($this->logicMember->memberEditPwd($this->param));

        $info = $this->logicMember->getMemberInfo(['id' => $this->param['id']]);
        $this->comm_data();
        $this->assign('info', $info);
        return $this->fetch('resetpwd');
    }

    /**
     *加载公共参数
     */
    public function comm_data(){
        $level_list= $this->logicMemberLevel->getMemberLevelList('','','',false);
        $this->assign('level_list', $level_list['data']);
    }

}
