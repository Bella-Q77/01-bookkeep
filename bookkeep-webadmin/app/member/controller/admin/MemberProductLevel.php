<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberProductLevelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\member\controller\admin;

/**
* 会员等级升级管理-控制器
*/

class MemberProductLevel extends MemberAdminBase
{

    /**
     * 会员等级升级列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 会员等级升级列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where=$this->logicMember->getWhere($this->param);
        $list = $this->logicMemberProductLevel->getMemberProductLevelList($where);
        return $list;
    }


    /**
     * 会员等级升级添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicMemberProductLevel->memberProductLevelAdd($this->param));
        $this->comm_data();
        return $this->fetch('add');
    }

    /**
     * 会员等级升级编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicMemberProductLevel->memberProductLevelEdit($this->param));

        $info = $this->logicMemberProductLevel->getMemberProductLevelInfo(['id' => $this->param['id']]);
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
        $this->jump($this->logicMemberProductLevel->memberProductLevelDel($where));
    }

    /**
     * 排序
     */
    public function set_field()
    {
        $this->jump($this->logicMember->setModelField('MemberProductLevel', $this->param));
    }

    /**
     *加载公共参数
     */
    public function comm_data(){
        $level_list= $this->logicMemberLevel->getMemberLevelList('','','',false);
        $this->assign('level_list', $level_list['data']);
    }

}
