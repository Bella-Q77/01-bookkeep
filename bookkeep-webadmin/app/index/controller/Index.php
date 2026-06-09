<?php
/**
 * 零起飞07FLY-CMS
 * ============================================================================
 * 版权所有 2018-2028 成都零起飞科技有限公司，并保留所有权利。
 * 网站地址: http://www.07fly.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 开发人生 <goodkfrs@qq.com>
 * Date: 2021-01-01-3
 */

namespace app\index\controller;

use think\Controller;

class Index extends IndexBase
{
    /**
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index()
    {
        $member = session('member_auth');
        return $this->fetch('index.html');
    }
    /**
     * 后台调用方法，可以配合路由配置
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/2 0002
     */
    public function sitemap()
    {
        $sitemaplist = $this->logicInfoType->getInfoTypeSitemapChannel();
        $this->assign('sitemaplist', $sitemaplist);
        return $this->fetch('sitemap.html');
    }


    /**
     * 设置选择地区
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/9/7 0007
     */
    public function setArea()
    {
        $this->logicSysArea->setSysAreaInfo($this->param);
        $this->redirect('index/index');
    }

    /**
     * 提取选择地区
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/9/7 0007
     */
    public function getArea()
    {
        $list = $this->logicSysArea->getSysAreaList('', '', '', false);
        $html = '<div class="sys_city_list">';
        foreach ($list as $row) {
            $html .= '<a href="' . url("index/setArea", array('id' => $row['id'])) . '">' . $row['name'] . '</a>';
        }
        $html .= '</div>';
        return $html;
    }

}
