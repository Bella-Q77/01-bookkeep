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

class Tags extends IndexBase{

    public $tid = '';
    public $type = '';

    /**
     * 标签主页
     */
    public function index()
    {

        /*获取当前页面URL*/
        $result['pageurl'] = $this->request->url(true);

        /*--end*/
        $rtnArray = array(
            'field' => $result,
        );
        /*模板文件*/
        $viewfile = 'tags_index.html';
        /*--end*/
        $this->assign('fly', $rtnArray);
        return $this->fetch($viewfile);
    }

    /**
     * 标签列表
     */
    public function lists()
    {
        $param = $this->param;
        $tagindexInfo=array();
        $tagid = isset($param['tagid']) ? $param['tagid'] : '';
        $tag = isset($param['tag']) ? trim($param['tag']) : '';
        if (!empty($tag)) {
            $map['tag']=['=',$tag];
            $tagindexInfo = $this->logicTag->getTagindexInfo($map);
        } elseif (intval($tagid) > 0) {
            $map['id']=['=',$tagid];
            $tagindexInfo = $this->logicTag->getTagindexInfo($map);
        }

        if (!empty($tagindexInfo)) {
            $tagid = $tagindexInfo['id'];
            $tag = $tagindexInfo['tag'];
            //更新统计、点击数据
            $this->logicTag->getTagindexUpdate($tagindexInfo);
        }

        is_object($tagindexInfo) && $tagindexInfo=$tagindexInfo->toArray();

        $field_data = array(
            'tag'   => $tag,
            'title'   => $tag,
            'tagid'   => $tagid,
        );

        $rtnArray = array(
            'field'  => array_merge($field_data,$tagindexInfo),
        );

        $this->assign('fly', $rtnArray);

        /*模板文件*/
        $viewfile = 'tags_list.html';
        return $this->fetch($viewfile);
    }

}
