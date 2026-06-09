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
 * 会员注册管理=》逻辑层
 */
class Register extends PortalmemberBase
{
    /**
     * 帐号注册处理
     */
    public function loginRegister($data = [])
    {
        $validate_result = $this->validateRegister->scene('register')->check($data);;
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateRegister->getError()];
            exit;
        }
        $data['email'] = empty($data['email']) ? $data['username'] : $data['email'];
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => data_md5_key($data['password']),
        ];
        $result = $this->modelMember->setInfo($userData);
        if ($result) {
            $user = $this->logicMember->getMemberInfo(['username' => $data['username']]);
            if (!empty($user['password']) && data_md5_key($data['password']) == $user['password']) {
                $this->modelMember->setFieldValue(['id' => $user['id']], 'last_login', TIME_NOW);
                $auth = ['member_id' => $user['id'], TIME_UT_NAME => TIME_NOW];
                session('member_info', $user);
                session('member_auth', $auth);
                session('member_auth_sign', data_auth_sign($auth));

                //注册积分
                $this->logicMemberIntegral->memberIntegralAdd('member_reg', $user['id']);

                //初始化账本,分类
                $this->modelBkBook->regInitBookCategory($user['id']);

                action_log('注册', '注册操作，username：' . $data['username']);
                return [RESULT_SUCCESS, '注册成功', url('portalmember/index/index')];
            } else {
                $error = empty($user['id']) ? '用户账号不存在' : '密码输入错误';
                return [RESULT_ERROR, $error];
            }
        }
    }
}
