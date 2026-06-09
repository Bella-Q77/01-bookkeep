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

use app\common\controller\ControllerBase;
use app\admin\logic\SysMenu;

/**
 * 模块基类
 */
class PortalmemberBase extends ControllerBase
{
    /**
     * 构造方法
     */
    public function __construct()
    {

        // 执行父类构造方法
        parent::__construct();

        // 关闭布局
        // $this->view->engine->layout(false);

        //  $this->template_member_dir = 'theme/portalmember/';

        $logicSysMenu = new SysMenu();

        // 获取过滤后的菜单树
        $this->authMenuLis = $this->logicMemberMenu->leftMenus();
        $this->authMenuTree = list_to_tree(array_values($this->authMenuLis), 'id', 'pid', 'child');
        // 菜单转换为视图
        $this->menuView = $logicSysMenu->menuToView($this->authMenuTree);
        // 菜单视图
        $this->assign('menu_view', $this->menuView);
        // 获取当前栏目默认标题
        $this->title = $this->logicMemberMenu->getDefaultTitle();
        $this->assign('title', $this->title);
        // 获取面包屑
        $this->crumbsView = $this->logicMemberMenu->getCrumbsView();
        $this->assign('crumbs_view', $this->crumbsView);

        $this->initBaseInfo();

    }

    /**
     * 初始化基础数据
     */
    final private function initBaseInfo()
    {
        //网站配置文件
        $webconfig = $this->logicWebsite->getWebsiteConfig();
        $this->assign('webconfig', $webconfig);

    }

    /**
     * 重写fetch方法,用于权限控制
     */
    final protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $content = parent::fetch($template, $vars, $replace, $config);
        return $content;
    }
}

?>