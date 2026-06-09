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
class Login extends PortalmemberBase
{
    /**
     * 登录
     */
    public function reg()
    {
        IS_POST && $this->jump($this->logicRegister->loginRegister($this->param));
        return $this->fetch('reg');
    }

	/**
	 * 登录
	 */
	public function checklogin()
	{
		if(member_is_login()){
			$str= "<a href='".url('portalmember/index/index')."'>会员中心</a>";
		}else{
			$str= "<a href='".url('portalmember/Login/login')."'>登录</a>/<a href='".url('portalmember/Login/reg')."'>注册</a>";
		}
		return $str;
	}

    /**
     * 登录
     */
    public function login()
    {
        member_is_login() && $this->jump(RESULT_REDIRECT, '已登录则跳过登录页', url('portalmember/Login/login'));
        IS_POST && $this->loginHandle($this->param['username'],$this->param['password']);
        return $this->fetch('login');
    }

    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {

        $this->jump($this->logicLogin->loginHandle($username, $password, $verify));
    }

    /**
     * 注销登录
     */
    public function logout()
    {

        $this->jump($this->logicLogin->logout());
    }

    /**
     * 清理缓存
     */
    public function clearCache()
    {

        $this->jump($this->logicLogin->clearCache());
    }
}
?>