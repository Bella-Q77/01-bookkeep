<?php
/*
*
* limsc.logic  逻辑层基础类
*
* ----------------------------------------------
* 零起飞网络 - 专注于企业管理系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright Copyright (C) 2017-2025 07FLY Network Technology Co,LTD.
* @license For licensing, see LICENSE.html
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.1.0
* @link ：http://www.07fly.xyz
* @Date:2023-10-11 09:53:53
*/

namespace app\portalmember\logic;

use app\common\logic\LogicBase;

/**
 * 模块基类
 */
class MemberMenu extends LogicBase
{
    /**
     * 左侧栏目
     * @return array[]
     * @author: 开发人生 goodkfrs@qq.com
     * @Time: 2024/1/2 9:14
     */
    public function leftMenus()
    {
        $menus = [
            ['id' => 300, 'pid' => 0, 'module' => 'portalmember', 'visible' => '1', 'name' => '记一记', 'url' => 'bookkeep.BkTransaction/addTab', 'icon' => 'fa-plus'],
            ['id' => 400, 'pid' => 0, 'module' => 'portalmember', 'visible' => '1', 'name' => '仪表盘', 'url' => 'bookkeep.BkStat/index', 'icon' => 'fa-tachometer'],

            ['id' => 500, 'pid' => 0, 'module' => 'portalmember', 'visible' => '1', 'name' => '交易管理', 'url' => 'bookkeep.BkTransaction/show', 'icon' => 'fa-list'],
            ['id' => 510, 'pid' => 500, 'module' => 'portalmember', 'visible' => '1', 'name' => '收支记录', 'url' => 'bookkeep.BkTransaction/show', 'icon' => 'fa-hourglass-o'],
            ['id' => 520, 'pid' => 500, 'module' => 'portalmember', 'visible' => '1', 'name' => '转账记录', 'url' => 'bookkeep.BkTransfer/show', 'icon' => 'fa-hourglass-o'],
//            ['id' => 530, 'pid' => 500, 'module' => 'portalmember', 'visible' => '1', 'name' => '报销管理', 'url' => 'DmsRebatBe/show', 'icon' => 'fa-hourglass-o'],
//            ['id' => 540, 'pid' => 500, 'module' => 'portalmember', 'visible' => '1', 'name' => '借贷记录', 'url' => 'DmsRebate/show', 'icon' => 'fa-hourglass-o'],

            ['id' => 600, 'pid' => 0, 'module' => 'portalmember', 'visible' => '1', 'name' => '统计分析', 'url' => 'Statistics', 'icon' => 'fa-line-chart'],
            ['id' => 610, 'pid' => 600, 'module' => 'portalmember', 'visible' => '1', 'name' => '日常收支表', 'url' => 'bookkeep.BkStat/transaction', 'icon' => 'fa-hourglass-o'],
            ['id' => 620, 'pid' => 600, 'module' => 'portalmember', 'visible' => '1', 'name' => '收支趋势表', 'url' => 'bookkeep.BkStat/trend', 'icon' => 'fa-hourglass-o'],
            ['id' => 630, 'pid' => 600, 'module' => 'portalmember', 'visible' => '1', 'name' => '成员收支表', 'url' => 'bookkeep.BkStat/person', 'icon' => 'fa-hourglass-o'],
            ['id' => 640, 'pid' => 600, 'module' => 'portalmember', 'visible' => '0', 'name' => '资产负债表', 'url' => 'bookkeep.BkStat/assets', 'icon' => 'fa-hourglass-o'],
            ['id' => 650, 'pid' => 600, 'module' => 'portalmember', 'visible' => '0', 'name' => '支出预算对比表', 'url' => 'bookkeep.BkStat/budget', 'icon' => 'fa-hourglass-o'],
            ['id' => 660, 'pid' => 600, 'module' => 'portalmember', 'visible' => '1', 'name' => '项目汇总表', 'url' => 'bookkeep.BkStat/project', 'icon' => 'fa-hourglass-o'],
            ['id' => 670, 'pid' => 600, 'module' => 'portalmember', 'visible' => '1', 'name' => '商家支出表', 'url' => 'bookkeep.BkStat/shop', 'icon' => 'fa-hourglass-o'],


            ['id' => 800, 'pid' => 0, 'module' => 'portalmember', 'visible' => '1', 'name' => '分类设置', 'url' => 'catgory manager', 'icon' => 'fa-cog'],
            ['id' => 801, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '资产账户', 'url' => 'bookkeep.BkAccount/show', 'icon' => 'fa-usd'],
            ['id' => 810, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '分类设置', 'url' => 'bookkeep.BkCategory/show', 'icon' => 'fa-ship'],
            ['id' => 820, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '账本管理', 'url' => 'bookkeep.BkBook/show', 'icon' => 'fa-ship'],
            ['id' => 830, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '预算设置', 'url' => 'bookkeep.BkBudget/show', 'icon' => 'fa-ship'],
            ['id' => 840, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '成员管理', 'url' => 'bookkeep.BkPerson/show', 'icon' => 'fa-ship'],
            ['id' => 850, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '商家管理', 'url' => 'bookkeep.BkShop/show', 'icon' => 'fa-ship'],
            ['id' => 860, 'pid' => 800, 'module' => 'portalmember', 'visible' => '1', 'name' => '项目管理', 'url' => 'bookkeep.BkProject/show', 'icon' => 'fa-ship'],


            ['id' => 1000, 'pid' => 0, 'module' => 'portalmember', 'visible' => '1', 'name' => '会员中心', 'url' => 'Login/logout', 'icon' => 'fa-user-secret'],
            ['id' => 1100, 'pid' => 1000, 'module' => 'portalmember', 'visible' => '1', 'name' => '会员中心', 'url' => 'Member/index', 'icon' => 'fa-user'],
            ['id' => 1200, 'pid' => 1000, 'module' => 'portalmember', 'visible' => '1', 'name' => '积分充值', 'url' => 'MemberProductIntegral/show', 'icon' => 'fa-h-square'],
            ['id' => 1300, 'pid' => 1000, 'module' => 'portalmember', 'visible' => '1', 'name' => 'VIP 会员', 'url' => 'MemberProductLevel/show', 'icon' => 'fa-tint'],
            ['id' => 1400, 'pid' => 1000, 'module' => 'portalmember', 'visible' => '1', 'name' => '订单管理', 'url' => 'MemberOrder/show', 'icon' => 'fa-exclamation-triangle'],
            ['id' => 1500, 'pid' => 1000, 'module' => 'portalmember', 'visible' => '1', 'name' => '问题反馈', 'url' => 'AskFaq/show', 'icon' => 'fa-question-circle'],
        ];
        foreach ($menus as &$row) {
            $row['name'] = lang($row['name']);
        }
        return $menus;
    }

    /**
     * 获取默认标题
     * @return mixed|string
     * @author: 开发人生 goodkfrs@qq.com
     * @Time: 2024/1/2 9:14
     */
    public function getDefaultTitle()
    {
        $menus = $this->leftMenus();
        $title = '';
        foreach ($menus as $menu) {
            if (strtolower($menu['url']) == URL) {
                $title = $menu['name'];
            }
        }
        return $title;
    }

    public function getCrumbsView()
    {
        $title = $this->getDefaultTitle();
        $crumbs_view = '<div class="row white-bg" style="padding: 10px 5px 10px 20px;font-size: 13px;"><div class="col-sm-12">';
        $crumbs_view .= "<ol class='breadcrumb'>";
        $crumbs_view .= "<li><a><i class='fa fa-circle-o'></i> " . $title . "</a></li>";
        $crumbs_view .= "</ol>";
        $crumbs_view .= "</div></div>";
        return $crumbs_view;
    }
}

?>