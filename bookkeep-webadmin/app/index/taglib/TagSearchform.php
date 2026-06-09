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
 * 搜索表单列表
 */
class TagSearchform extends Base
{

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }


    /**获取搜索表单
     * @return string
     * Author: lingqifei created by at 2020/3/5 0005
     */
    public function getSearchform($typeid = '', $channelid = '', $notypeid = '', $flag = '', $noflag = '')
    {
        $searchurl = url('index/Search/index');

        $hidden = '';

        !empty($typeid) && $hidden .= '<input type="hidden" name="typeid" id="typeid" value="'.$typeid.'" />';
        !empty($channelid) && $hidden .= '<input type="hidden" name="channelid" id="channelid" value="'.$channelid.'" />';
        !empty($notypeid) && $hidden .= '<input type="hidden" name="notypeid" id="notypeid" value="'.$notypeid.'" />';
        !empty($flag) && $hidden .= '<input type="hidden" name="flag" id="flag" value="'.$flag.'" />';
        !empty($noflag) && $hidden .= '<input type="hidden" name="noflag" id="noflag" value="'.$noflag.'" />';

        $result[0] = array(
            'searchurl' => $searchurl,
            'action' => $searchurl,
            'hidden'    => $hidden,
        );

        return $result;
    }
}