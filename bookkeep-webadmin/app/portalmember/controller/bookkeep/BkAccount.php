<?php
/*
*
* 零起飞进销系统（07FLY-DMS）
*
* ----------------------------------------------
* 零起飞网络 - 专注于企业管理系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD All rights reserved.
* @license    For licensing, see LICENSE.html
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/
namespace app\portalmember\controller\bookkeep;

use app\portalmember\controller\PortalmemberBaseAuth;

/**
* 账本管理-控制器
*/

class BkAccount extends PortalmemberBookkeepAuth
{
    public function __construct()
    {
        parent::__construct();
        $account_type_list=$this->logicBkAccount->getAccountType();
        $this->assign('account_type_list',$account_type_list);
    }

    /**
     * 账本列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if(!empty($this->param['catetype'])){
            $this->assign('catetype',$this->param['catetype']);
        }else{
            $this->assign('catetype','income');
        }
        return $this->fetch('show');
    }

    /**
     * 账本列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicBkAccount->getWhere($this->param);
        $list = $this->logicBkAccount->getBkAccountList($where);
        return $list;
    }

    /**
     * 账本添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicBkAccount->bkAccountAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 账本编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicBkAccount->bkAccountEdit($this->param));

        $info = $this->logicBkAccount->getBkAccountInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 账本删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkAccount->bkAccountDel($where));
    }
    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBkAccount->setField('BkAccount', $this->param));
    }
    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBkAccount->setSort('BkAccount', $this->param));
    }
    public function set_default()
    {
        $this->jump($this->logicBkAccount->setBkAccountDefault($this->param));
    }

}
