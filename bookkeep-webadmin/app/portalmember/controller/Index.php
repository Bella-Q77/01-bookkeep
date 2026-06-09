<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2021 07FLY Network Technology Co,LTD.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\portalmember\controller;


/**
 * 会员中心=》首页
 */
class Index extends PortalmemberBaseAuth
{
    public function __construct()
    {
        parent::__construct();

        $where['member_id'] = $this->member['id'];
        $where['is_default'] = 1;
        $this->book = $this->logicBkBook->getBkBookInfo($where);
        session('default_book_id', $this->book['id']);

        $list = $this->logicBkBook->getBkBookList(['visible' => 1], 'name, id,sort', 'sort asc', false);
        $this->assign('book_list', $list);
    }

    /**
     * 登录
     */
    public function index()
    {
        $this->view->engine->layout(false);

        $this->assign('book', $this->book);

        return $this->fetch('index');
    }

    public function main()
    {
        return $this->fetch('main');
    }

    //切换账本
    public function chg_book()
    {
        session('default_book_id', $this->param['id']);
        $this->jump($this->logicBkBook->setBkBookDefault($this->param));
    }
}

?>