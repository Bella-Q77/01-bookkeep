<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * AskLinkmanor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\controller\admin;

/**
 * 点评管理-控制器
 */
class AskCommentReply extends MemberAdminBase
{

    /**
     * 点评添加
     * @return mixed|string
     */
    public function add()
    {

        IS_POST && $this->jump($this->logicAskCommentReply->askCommentReplyAdd($this->param));

        //点评详细
        $info = $this->logicAskCommentReply->getAskCommentReplyInfo($this->param);

        $this->assign('info', $info);

        return $this->fetch('add');

    }

    /**
     * 点评删除
     */
    public function del()
    {
        $where = empty($this->param['id']) ? ['id' => 0] : ['id' => $this->param['id']];
        $this->jump($this->logicAskCommentReply->cstCommentReplyDel($where));
    }
}
