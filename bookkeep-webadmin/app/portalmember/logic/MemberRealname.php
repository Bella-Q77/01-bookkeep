<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberRealnameor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员实名管理=》逻辑层
 */
class MemberRealname extends PortalmemberBase
{
    /**
     * 会员实名编辑
     * @param array $data
     * @return array
     */
    public function memberRealnameEdit($data = [])
    {

        $validate_result = $this->validateMemberRealname->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberRealname->getError()];
        }
        $status= $this->modelMemberRealname->getValue(['id'=>$data['id']], 'status');

        if($status==1){
            return [RESULT_ERROR, '您的帐号已经实名认证成功~不需要重复申请'];
            exit;
        }

        $url = url('portalmember/index/index');
        $result = $this->modelMemberRealname->setInfo($data);
        $result && action_log('编辑', '编辑实名信息，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberRealname->getError()];
    }

    /**会员实名信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberRealnameInfo($where = [], $field = true)
    {
        return $this->modelMemberRealname->getInfo($where, $field);
    }

}
