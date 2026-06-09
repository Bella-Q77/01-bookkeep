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
 * 会员购买广告中心=》首页
 */
class MemberAdv extends PortalmemberBaseAuth
{

    /**
     * 会员购买广告提交
     */
    public function show()
    {
        $list=$this->logicMemberAdv->getMemberAdvList();
        $pages=$list->render('info,pre,next,pageno',DB_LIST_ROWS);
        $this->assign('pages', $pages);
        $this->assign('list', $list);
        return $this->fetch('member_adv_show');
    }

    /**
     * 积分购买
     */
    public function buy()
    {
        IS_POST && $this->jump($this->logicMemberAdv->memberAdvBuy($this->param));
        $map['id'] = $this->param['id'];
        $info = $this->logicMemberAdv->getMemberAdvInfo($map);
        $this->assign('info', $info);
        return $this->fetch('member_adv_buy');
    }


}
?>