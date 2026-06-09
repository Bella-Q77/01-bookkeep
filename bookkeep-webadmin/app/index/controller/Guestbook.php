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

class Guestbook extends IndexBase{

    public $aid = '';
    public $type = '';


    /**
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index(){
        $tagGlobal  = new \app\index\taglib\TagGlobal;
        $name=$tagGlobal->getGlobal('seo_title');
        $this->assign('title',$name);
        return $this->fetch('guestbook.html');
    }

    /**
     * 留言添加
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function add(){
        if(empty($this->param['tid']) || empty($this->param['addfield'])){
            $this->tid=$this->param['tid'];
        }else{
            $this->jump($this->logicGuestbook->guestbookAdd($this->param));
        }
    }

    /**
     * 短信发送测试
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/7/12 0012
     */
    public function  send_sms(){
        $this->jump($this->logicGuestbook->send_sms($this->param));
    }

}
