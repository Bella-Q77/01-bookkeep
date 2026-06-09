<?php
/*
*
* portalmember.MemberOrder  前台会员管理中心-频道模型
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
 * 订单回调管理=》首页
 */
class MemberOrderCalback extends PortalmemberBase
{

    /**
     * 支付回调
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/7/12 0012
     */
    public function  order_pay_notify(){
        $this->jump($this->logicMemberOrderCalback->getMemberOrderPayCalback($this->param));
    }

}
?>