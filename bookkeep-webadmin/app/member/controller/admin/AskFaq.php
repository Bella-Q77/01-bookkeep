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

namespace app\member\controller\admin;

/**
 * 问题列表管理-控制器
 */
class AskFaq extends MemberAdminBase
{

    /**
     * 问题列表列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        if (!empty($this->param['listtype'])) {
            $this->assign('listtype', $this->param['listtype']);
        } else {
            $this->assign('listtype', 'selfson');
        }
        $this->common_data();
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
        $this->common_data();
        return $this->fetch('add');
    }

    /**
     * 问题列表编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicAskFaq->askFaqEdit($this->param));

        $this->common_data();

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
        $this->common_data();
        $info = $this->logicAskFaq->getAskFaqInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        return $this->fetch('detail');
    }

    /**
     * 公共数据
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/6/20 0020
     */
    public function common_data()
    {
        //问题类型
        if (!empty($this->param['bus_type'])) {
            $this->assign('bus_type', $this->param['bus_type']);
        } else {
            $this->assign('bus_type', '');
        }
        //问题类型
        if (!empty($this->param['bus_id'])) {
            $this->assign('bus_id', $this->param['bus_id']);
        } else {
            $this->assign('bus_id', '0');
        }

    }

    /**
     * 下载导出
     */
    public function download()
    {
        $where = $this->logicAskFaq->getWhere($this->param);
        if (!empty($this->param['openstatus'])) {
            $where['openstatus'] = ['=', $this->param['openstatus']];
        }
        $this->logicAskFaq->getAskFaqListDown($where);
    }

}
