<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\cms\controller\api;

use app\common\controller\ControllerBase;
use think\Db;


/**
 * 地图管理-控制器
 */
class Sitemap extends ControllerBase
{

    /**
     * 构造方法
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();

    }

    /**
     * 地图创建
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function sitemap()
    {

        $siteStr = '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $arctypelist = Db::name('arctype')->field('id,litpic,ispart,typedir')->select();
        foreach ($arctypelist as &$row) {
            $typeurl = $this->getArctypeUrl($row);
            $siteStr .= '<url><loc>' . DOMAIN . $typeurl . '</loc></url>';
        }

        $arclist = Db::name('archives')->field('id,litpic,is_jump,type_id')->select();
        foreach ($arclist as $row) {
            $arcurl = $this->getArchivesUrl($row);
            $siteStr .= '<url><loc>' . DOMAIN . $arcurl . '</loc></url>';
        }
        $siteStr .= '</urlset>';
        file_put_contents(PATH_PUBLIC . 'sitemap.xml', $siteStr);
        //d($siteStr);
    }


    /**
     * 获得所有指定id所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getArctypeUrl($data = [])
    {
        if ($data['ispart'] == 2) {
            $data['typeurl'] = $data['typedir'];
            if (!is_http_url($data['typeurl'])) {
                $typeurl = '//' . request()->host();
                $typeurl .= '/' . trim($data['typeurl'], '/');
            }
        } else {
            if ($data['typedir']) {
                $typeurl = url('index/lists/index', array('tid' => $data['typedir']));
            } else {
                $typeurl = url('index/lists/index', array('tid' => $data['id']));
            }
        }
        return $typeurl;
    }

    /**
     * 转换一条文章的实际地址
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getArchivesUrl($data = [])
    {
        if ($data['is_jump'] == 1 && $data['jump_url']) {
            $arcurl = $data['jump_url'];
        } else {
            $typeid = $this->getTrueTypedir($data['type_id']);
            $arcurl = url('index/view/index', array('typeid' => $typeid, 'aid' => $data['id']));
        }
        return $arcurl;
    }

    /**
     * 在typeid传值为目录名称的情况下，获取栏目ID
     */
    public function getTrueTypedir($typeid = '')
    {
        $typedir = Db::name('arctype')->where(['id' => $typeid])->value('typedir');
        if (empty($typedir)) return $typeid;
        return $typedir;
    }

}
