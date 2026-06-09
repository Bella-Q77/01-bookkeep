<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberPictureor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\member\controller\admin;

/**
* 会员图片管理-控制器
*/

class MemberPicture extends MemberAdminBase
{

    /**
     * 会员图片列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        $this->comm_data();
        return $this->fetch('show');
    }

    /**
     * 会员图片列表-》json数据
     * @return
     */
    public function show_json()
    {
        $orderby=$this->logicMemberPicture->getOrderBy($this->param);
        $where=$this->logicMemberPicture->getWhere($this->param);
        $list = $this->logicMemberPicture->getMemberPictureList($where,'a.*,m.username',$orderby);
        return $list;
    }


    /**
     * 会员图片添加
     * @return mixed|string
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicMemberPicture->memberPictureAdd($this->param));
        $this->comm_data();
        return $this->fetch('add');
    }

    /**
     * 会员图片编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicMemberPicture->memberPictureEdit($this->param));
        $info = $this->logicMemberPicture->getMemberPictureInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $this->comm_data();
        return $this->fetch('edit');
    }

    /**
     * 会员图片删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicMemberPicture->memberPictureDel($where));
    }

    /**
     * 审核
     */
    public function pass()
    {
        $this->jump($this->logicMemberPicture->memberPictureAudit($this->param));
    }
    /**
     * 审核
     */
    public function reject()
    {
        IS_POST && $this->jump($this->logicMemberPicture->memberPictureAudit($this->param));
        $info = $this->logicMemberPicture->getMemberPictureInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
        $this->comm_data();
        return $this->fetch('reject');
    }


    /**
     *加载公共参数
     */
    public function comm_data(){

        $status_list= $this->logicMemberPicture->getStatus();
        $this->assign('status_list', $status_list);
    }

}
