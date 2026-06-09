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
 * 会员等级升级管理=》逻辑层
 */
class Login extends PortalmemberBase
{
    /**
     * 登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {

        $validate_result = $this->validateLogin->scene('portalmemberlogin')->check(compact('username', 'password'));
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateLogin->getError()];
        }

        $user = $this->modelMember->getInfo(['username' => $username]);

        if (!empty($user['password']) && data_md5_key($password) == $user['password']) {

            $this->modelMember->setFieldValue(['id' => $user['id']], 'last_login', TIME_NOW);

            //会员登录
            $this->loginHandleSession($user);

            //登录积分
            $this->logicMemberIntegral->memberIntegralAdd('member_login', $user['id']);

            action_log('登录', '登录操作，username：' . $username);
            return [RESULT_SUCCESS, '登录成功', url('portalmember/index/index')];

        } else {
            $error = empty($user['id']) ? '用户账号不存在' : '密码输入错误';
            return [RESULT_ERROR, $error];
        }
    }

    //登录处理=>api调用
    public function loginHandleToApi($data = [])
    {
        if (empty($data['username']) || empty($data['password'])) {
            return [RESULT_ERROR, '用户名称和密码不能空~'];
            exit;
        }
        $user = $this->modelMember->getInfo(['username' => $data['username']]);

        if (!empty($user['password']) && data_md5_key($data['password']) == $user['password']) {

            //会员登录
            $this->loginHandleSession($user);

            //登录用户token
            $user_token = $this->loginUserToken($user);

            return [RESULT_SUCCESS, '登录成功', url('index/index'), $user_token];
        } else {
            $error = empty($user['id']) ? '用户账号不存在' : '密码输入错误';
            return [RESULT_ERROR, $error];
        }
    }


    /**
     * openid=>登录方式
     * @param array $data
     * @return int 成功为id,失败为0
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2021/12/24 0024 17:28
     */
    public function loginOpenId($data = [])
    {
        $openid = $data['openid'];
        $user = $this->modelMember->getInfo(['open_id' => $openid]);
        if ($user) {
            if (empty($user['nickname'])) {
                $userData = [
                    'nickname' => empty($data['nickname']) ? '' : $data['nickname'],
                    'gender' => empty($data['sex']) ? '1' : $data['sex'],
                    'head_pic' => empty($data['headimgurl']) ? '' : $data['headimgurl'],
                    'last_login' => TIME_NOW,
                ];
            } else {
                $userData = [
                    'last_login' => TIME_NOW,
                ];
            }
            $this->modelMember->updateInfo(['id' => $user['id']], $userData);

            //登录session
            $this->loginHandleSession($user);

            //登录用户token
            $user_token = $this->loginUserToken($user);

            return $user_token;
        } // 注册
        else {
            $userData = [
                'open_id' => $openid,
                'username' => get_sys_seqnum('bookkeep'),
                'nickname' => empty($data['nickname']) ? '' : $data['nickname'],
                'gender' => empty($data['sex']) ? '1' : $data['sex'],
                'head_pic' => empty($data['headimgurl']) ? '' : $data['headimgurl'],
                'password' => data_md5_key('123456'),
                'last_login' => TIME_NOW,
            ];
            $result = $this->modelMember->setInfo($userData);

            //注册成功,之后操作
            if ($result) {
                $user = $this->logicMember->getMemberInfo(['open_id' => $openid]);

                //更新编号
                get_sys_seqnum('bookkeep', true);

                //初始化账本,分类
                $this->modelBkBook->regInitBookCategory($user['id']);

                //登录session
                $this->loginHandleSession($user);

                //注册积分
                $this->logicMemberIntegral->memberIntegralAdd('member_reg', $user['id']);
                action_log('注册', '微信自动注册，username：' . $openid);

                //登录用户token
                $user_token = $this->loginUserToken($user);

            }
            return $user_token;
        }
    }


    public function loginHandleSession($user)
    {
        $auth = ['member_id' => $user['id'], TIME_UT_NAME => TIME_NOW];
        session('member_info', $user);
        session('member_auth', $auth);
        session('member_auth_sign', data_auth_sign($auth));
    }


    //登录用户token
    public function loginUserToken($user = [])
    {
        //生成user token
        $user_token = encoded_user_token($user);
        $user_token['access_token'] = get_access_token();
        $user_token['userinfo'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'nickname' => $user['nickname'],
            'level_id' => $user['level_id'],
            'head_pic_url' => DOMAIN . get_picture_url($user['head_pic']),
            'mobile' => $user['mobile'],
            'qicq' => $user['qicq'],
            'email' => $user['email']
        ];

        return $user_token;
    }

    /**
     * 注销当前用户
     */
    public function logout()
    {
        clear_member_login_session();
        return [RESULT_SUCCESS, '注销成功', url('portalmember/Login/login')];
    }

    /**
     * 清理缓存
     */
    public function clearCache()
    {
        \think\Cache::clear();
        return [RESULT_SUCCESS, '清理成功', url('portalmember/Login/login')];
    }
}
