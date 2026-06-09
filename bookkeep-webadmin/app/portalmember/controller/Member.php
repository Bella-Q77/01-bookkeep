<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2021 07FLY Network Technology Co,LTD.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/
namespace app\portalmember\controller;


/**
 * 会员中心=》首页
 */
class Member extends PortalmemberBaseAuth
{
    public function index()
    {
        return $this->fetch('index');
    }
    /**
     * 基本信息修改
     */
    public function editInfo()
    {
        IS_POST && $this->jump($this->logicMember->memberEdit($this->param));
        return $this->fetch('edit_info');
    }

    /**
     * 公司资料修改
     */
    public function company_edit()
    {
        IS_POST && $this->jump($this->logicMemberCompany->memberCompanyEdit($this->param));
//        $map['id']=['=',$this->member['id']];
        $map['member_id']=['=',$this->member['id']];
        $info=$this->logicMemberCompany->getMemberCompanyInfo($map);
        $typelist=$this->logicInfoType->getInfoTypeList(['level'=>'1'],'','',false);
        $this->assign('info', $info);
        $this->assign('typelist', $typelist);
        return $this->fetch('company_edit');
    }

    /**
     * 洽谈代码
     */
    public function member_talk_code()
    {
        IS_POST && $this->jump($this->logicMember->memberTalkcodeEdit($this->param));
        return $this->fetch('member_talk_code');
    }

    /**
     * 会员实名提交
     */
    public function member_realname()
    {
        IS_POST && $this->jump($this->logicMemberRealname->memberRealnameEdit($this->param));
        $map['member_id']=['=',$this->member['id']];
        $info=$this->logicMemberRealname->getMemberRealnameInfo($map);
        $this->assign('info', $info);
        return $this->fetch('member_realname');
    }
}
?>