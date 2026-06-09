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
class TagGuestbookform extends Base
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
    public function getGuestbookform($tid = '', $type = '', $addfield = '')
    {
        if (empty($tid)) {
            echo '缺少提交指定表单';
            return  false;
        }
        if (empty($addfield)) {
            echo '缺少提交指定字段';
            return  false;
        }
        $searchurl = url('index/Guestbook/add');

        $hidden = '';
        !empty($tid) && $hidden .= '<input type="hidden" name="tid" id="tid" value="'.$tid.'" />';
        !empty($addfield) && $hidden .= '<input type="hidden" name="addfield" id="addfield" value="'.$addfield.'" />';

        $result[0] = array(
            'searchurl' => $searchurl,
            'action' => $searchurl,
            'hidden'    => $hidden,
        );

        return $result;
    }
}