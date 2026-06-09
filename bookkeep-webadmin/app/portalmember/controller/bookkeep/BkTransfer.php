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
 * 转账记录管理-控制器
 */
class BkTransfer extends PortalmemberBookkeepAuth
{

    /**
     * 转账记录列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if (!empty($this->param['catetype'])) {
            $this->assign('catetype', $this->param['catetype']);
        } else {
            $this->assign('catetype', 'income');
        }
        return $this->fetch('show');
    }

    /**
     * 转账记录列表-》json数据
     * @return
     */
    public function show_json()
    {

        if (!empty($this->param['get_list_total'])) {
            $where = $this->logicBkTransfer->getWhere($this->param);
            $total = $this->logicBkTransfer->getBkTransferListTotal($where);
            $rtnArray = ['code' => 1, 'msg' => '数据返回成功！', 'data' => $total];
            return $rtnArray;
        }

        $where = $this->logicBkTransfer->getWhere($this->param);
        $list = $this->logicBkTransfer->getBkTransferList($where);
        return $list;
    }

    /**
     * 转账记录添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicBkTransfer->bkTransferAdd($this->param));
        //历史记录
        $history = session('history_transaction');
        $this->assign('history', $history);
        return $this->fetch('add');
    }


    /**
     * 转账记录编辑
     * @return mixed|string
     */
    public function edit()
    {

        IS_POST && $this->jump($this->logicBkTransfer->bkTransferEdit($this->param));

        $info = $this->logicBkTransfer->getBkTransferInfo(['a.id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 转账记录删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicBkTransfer->bkTransferDel($this->param));
    }

    /**
     * 排序
     */
    public function set_visible()
    {
        $this->jump($this->logicBkTransfer->setField('BkTransfer', $this->param));
    }

    /**
     * 排序
     */
    public function set_sort()
    {
        $this->jump($this->logicBkTransfer->setSort('BkTransfer', $this->param));
    }

    public function set_default()
    {
        $this->jump($this->logicBkTransfer->setBkTransferDefault($this->param));
    }
}
