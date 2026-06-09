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

use app\common\controller\ControllerBase;


use think\Hook;
use think\Session;

/**
 * 基类控制器
 */
class IndexBase extends ControllerBase
{

    public $sys_city_name = '';//当前地区
    public $web_theme_name = '';//主题
    public $web_config = '';//网站配置

    /**
     * 构造方法
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();

        //基本信息载入
        $this->initBaseInfo();

    }

    /**
     * 初始化基础数据
     */
    final private function initBaseInfo()
    {

        $this->web_config = $this->logicWebsite->getWebsiteConfigColumn();
        $this->web_theme_name = $this->web_config['web_theme'];

        //配置模板目录设置
        $root_url = get_file_root_path();
        $this->assign('root_url', $root_url);
        $index_entry_dir = config('index_entry_dir');

        //判断是否为移动端,是否启用移动站
        if (is_mobile() && !empty($this->web_config['web_wap'])) {
            $this->assign('template_dir', $root_url . $index_entry_dir . 'theme/' . $this->web_theme_name . '/wap/');
        } else {
            $this->assign('template_dir', $root_url . $index_entry_dir . 'theme/' . $this->web_theme_name . '/pc/');
        }

        //是否开启多城市站点
        if(!empty($this->web_config['web_multi_city'])){
            $this->initSysCityInfo();
        }
    }

    /**
     * 重写fetch方法
     */
    final protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        //判断终端是PC、Wap
        if (is_mobile() && !empty($this->web_config['web_wap'])) {
            $template = PATH_PUBLIC . 'theme' . DS . $this->web_theme_name . DS . 'wap' . DS . $template;
        } else {
            $template = PATH_PUBLIC . 'theme' . DS . $this->web_theme_name . DS . 'pc' . DS . $template;
        }


        if (!file_exists($template)) {
            echo "模板文件不存：" . $template;
            exit;
        }
        //系统默认关键替换
        $replace = [
            '{sys_city_name}' => $this->sys_city_name,
        ];
        return parent::fetch($template, $vars, $replace, $config);
    }

    /**
     * 初始化站点=>地区信息
     *
     * 1、根据IP定位地区
     * 2、根据域名定位地区
     *
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/12/23 0023
     */
    final private function initSysCityInfo()
    {
        //默认初始化地区信息,i不存在表示为第一次进入，调用默认信息

        if (!Session::has('sys_city_name') || !Session::has('sys_city_id')) {
            $this->logicSysArea->getSysAreaDefaultInfo();
        }
        //模板调用字段属性
        $this->assign('sys_city_id', Session::get('sys_city_id'));
        $this->assign('sys_city_name', Session::get('sys_city_name'));

        //模板标签自动替换
        $this->sys_city_name = Session::get('sys_city_name');

    }
}
