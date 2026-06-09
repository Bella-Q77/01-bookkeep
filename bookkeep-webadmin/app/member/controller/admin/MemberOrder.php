<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberOrderor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\member\controller\admin;

/**
* 会员订单管理-控制器
*/

class MemberOrder extends MemberAdminBase
{

    /**
     * 会员订单列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        $this->comm_data();
        return $this->fetch('show');
    }

    /**
     * 会员订单列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where=$this->logicMemberOrder->getWhere($this->param);
        $list = $this->logicMemberOrder->getMemberOrderList($where);
        return $list;
    }


    /**
     * 会员订单添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicMemberOrder->memberOrderAdd($this->param));
        $this->comm_data();
        return $this->fetch('add');
    }

    /**
     * 会员订单编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicMemberOrder->memberOrderEdit($this->param));

        $info = $this->logicMemberOrder->getMemberOrderInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $this->comm_data();
        return $this->fetch('edit');
    }

    /**会员订单详细
     * @return mixed|string
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/1/19 0019 11:56
     */
    public function detail()
    {

        $info = $this->logicMemberOrder->getMemberOrderInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $this->comm_data();
        return $this->fetch('detail');
    }

    /**
     * 会员订单删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicMemberOrder->memberOrderDel($where));
    }
    

    /**
     *加载公共参数
     */
    public function comm_data(){
        $pay_status_list= $this->logicMemberOrder->getPayStatus();
        $this->assign('pay_status_list', $pay_status_list);

        $pay_method_list= $this->logicMemberOrder->getPayMethod();
        $this->assign('pay_method_list', $pay_method_list);

        $bus_type_list= $this->logicMemberOrder->getBusType();
        $this->assign('bus_type_list', $bus_type_list);

    }

}
