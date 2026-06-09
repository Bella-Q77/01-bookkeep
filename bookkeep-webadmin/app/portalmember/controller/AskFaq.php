<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * CstLinkmanor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\controller;

/**
 * 问题列表管理-控制器
 */
class AskFaq extends PortalmemberBaseAuth
{

    /**
     * 问题列表列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 问题列表列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = $this->logicAskFaq->getWhere($this->param);
        $orderby = $this->logicAskFaq->getOrderby($this->param);
        $list = $this->logicAskFaq->getAskFaqList($where, 'a.*', $orderby);
        return $list;
    }


    /**
     * 问题列表添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicAskFaq->askFaqAdd($this->param));
        return $this->fetch('add');
    }

    /**
     * 问题列表编辑
     * @return mixed|string
     */

    public function edit()
    {
        IS_POST && $this->jump($this->logicAskFaq->askFaqEdit($this->param));

        $info = $this->logicAskFaq->getAskFaqInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 问题列表删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicAskFaq->askFaqDel($where));
    }

    /**
     * 问题列表=>详细
     * @return mixed|string
     */
    public function detail()
    {
        $info = $this->logicAskFaq->getAskFaqInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        return $this->fetch('detail');
    }
}
