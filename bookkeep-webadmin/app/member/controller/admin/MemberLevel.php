<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberLevelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\member\controller\admin;

/**
* 会员等级管理-控制器
*/

class MemberLevel extends MemberAdminBase
{

    /**
     * 会员等级列表=》模板
     * @return mixed|string
     */
    public function show()
    {
        return $this->fetch('show');
    }

    /**
     * 会员等级列表-》json数据
     * @return
     */
    public function show_json()
    {
        $where = [];
        if (!empty($this->param['keywords'])) {
            $where['name|domain'] = ['like', '%' . $this->param['keywords'] . '%'];
        }
        $list = $this->logicMemberLevel->getMemberLevelList($where);
        return $list;
    }


    /**
     * 会员等级添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicMemberLevel->memberLevelAdd($this->param));

        return $this->fetch('add');
    }

    /**
     * 会员等级编辑
     * @return mixed|string
     */

    public function edit()
    {

        IS_POST && $this->jump($this->logicMemberLevel->memberLevelEdit($this->param));

        $info = $this->logicMemberLevel->getMemberLevelInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit');
    }

    /**
     * 会员等级删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicMemberLevel->memberLevelDel($where));
    }

}
