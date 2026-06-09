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
 * 会员实名中心=》首页
 */
class MemberRealname extends PortalmemberBaseAuth
{

    /**
     * 会员实名提交
     */
    public function edit()
    {
        IS_POST && $this->jump($this->logicMemberRealname->memberRealnameEdit($this->param));
        $map['member_id']=['=',$this->member['id']];
        $info=$this->logicMemberRealname->getMemberRealnameInfo($map);
        $this->assign('info', $info);
        return $this->fetch('member_realname_edit');
    }

    /**
     * 实名说明文件
     */
    public function info()
    {
        $map['member_id']=['=',$this->member['id']];
        $info=$this->logicMemberRealname->getMemberRealnameInfo($map);
        $this->assign('info', $info);
        return $this->fetch('member_realname_info');
    }

}
?>