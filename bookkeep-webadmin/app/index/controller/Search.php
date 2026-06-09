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

class Search extends IndexBase{

    public $tid = '';
    public $type = '';

    /**
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index($keywords=0){
//        if(empty($this->param['keywords'])){
//            echo "keywords不能为空~";
//            exit;
//        }
        /*模板参数*/
        $rtnArray = array(
            'field' => $this->param,
        );
        $this->assign('fly', $rtnArray);

        /*模板文件*/
		if(empty($this->param['tpfile'])){
			$tpfile= 'search.html';
		}else{
			$tpfile= $this->param['tpfile'];
		}

        $viewfile = !empty($tpfile)?strtolower($tpfile):$tpfile;
        return $this->fetch($viewfile);
    }
}
