<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Author: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\taglib;

use think\Db;
use think\Request;


/**
 * 面包绡导航
 */
class TagPosition extends Base
{
    public $tid = '';
    public $logicArchives='';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();

        //引用文档逻辑
        $this->logicArchives= new \app\index\logic\Archives();

        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');

        /*应用于文档列表*/
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = $this->logicArchives->getArchivesFieldValue(['id'=>$aid],'type_id');
        }
        /*--end*/

        /*tid为目录名称的情况下*/
        $this->tid = $this->getTrueTypeid($this->tid);
        /*--end*/
    }


    /**
     * 获取面包屑位置
     * @param string $typeid
     * @param string $symbol
     * @param string $style
     * @return string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getPosition($typeid = '', $symbol = '>', $style = 'crumb')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;

        $indexname = config('web_indexname');

        $basic_indexname = !empty($indexname) ? $indexname: '首页';

        $symbol = !empty($symbol) ? $symbol : config('web_symbol');
        $symbol = !empty($symbol) ? $symbol : '>';

        /*首页链接*/
        $home_url = url('index/index');
        /*--end*/
        $symbol = htmlspecialchars_decode($symbol);
        $str = "<a href='{$home_url}' class='{$style}'>{$basic_indexname}</a>";

        //解析当前栏目分类,父级菜单
        $logicArctype = new \app\index\logic\Arctype();
        $pids = $logicArctype->getArctypeAllPid($typeid);
        if (!empty($pids)) {
            $pids = array_reverse($pids);
            $i = 1;
            foreach ($pids as $tid) {
                $info = $logicArctype->getArctypeInfo(['id' => $tid], 'id,ispart,typedir,typename');
                if ($i < count($pids)) {
                    $str .= " {$symbol} <a href='{$info['typeurl']}' class='{$style}'>{$info['typename']}</a>";
                } else {
                    $str .= " {$symbol} <a href='{$info['typeurl']}'>{$info['typename']}</a>";
                }
                ++$i;
            }
        }

        $typeinfo=$logicArctype->getArctypeInfo(['id'=>$typeid]);
        $str .= " {$symbol} <a href='{$typeinfo['typeurl']}'>{$typeinfo['typename']}</a>";
        //关联当前分类--end

        return $str;
    }
}