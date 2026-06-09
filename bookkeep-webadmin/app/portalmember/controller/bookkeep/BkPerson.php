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

/**
* 成员管理-控制器
*/

class BkPerson extends PortalmemberBookkeepAuth
{

    /**
     * 成员列表=》模板
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
     * 成员列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicBkPerson->getWhere($this->param);
        $list = $this->logicBkPerson->getBkPersonList($where);
        return $list;
    }

    /**
     * 成员添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicBkPerson->bkPersonAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 成员编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicBkPerson->bkPersonEdit($this->param));

        $info = $this->logicBkPerson->getBkPersonInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 成员删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkPerson->bkPersonDel($where));
    }
    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBkPerson->setField('BkPerson', $this->param));
    }
    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBkPerson->setSort('BkPerson', $this->param));
    }
    public function set_default()
    {
        $this->jump($this->logicBkPerson->setBkPersonDefault($this->param));
    }
}
