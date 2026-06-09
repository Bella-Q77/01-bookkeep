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
 * 模块基类
 */
class PortalmemberBaseAuth extends PortalmemberBase
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();

        // 会员ID
        defined('MEMBER_ID') or define('MEMBER_ID', member_is_login());

        // 验证登录
        !MEMBER_ID && $this->redirect('portalmember/Login/login');

        $this->member = $this->logicMember->getMemberInfo(['id' => MEMBER_ID]);

        $this->assign('member', $this->member);

    }
}

?>