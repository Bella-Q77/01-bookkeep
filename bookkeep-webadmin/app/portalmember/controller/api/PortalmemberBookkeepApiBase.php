<?php
/*
*
* 零起飞客户管理系统（07FLY-CRM）
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

namespace app\portalmember\controller\api;

use app\common\controller\ApiBase as CommonApiBase;
use think\Hook;

/**
 * 后台基类控制器
 */
class PortalmemberBookkeepApiBase extends CommonApiBase
{
    // 授权过的菜单列表
    protected $authMenuList = [];

    // 授权过的菜单url列表
    protected $authMenuUrlList = [];

    protected $AdminBase = '';

    /**
     * 构造方法
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();
        // 后台控制器钩子
        Hook::listen('hook_controller_api_access_base', $this->request);

        // 初始化用户信息
        $this->initUserAuth();
        //检查是否授权过的菜单和功能
        //$this->checkUserPermission();
    }


    /**
     * 初始化用户认证信息
     */
    private function initUserAuth()
    {
        $param = [
            'user_token'   => '',
            'access_token' => ''
        ];

        // 方法1：优先从 formData 中获取（POST 表单字段）
        $request = request();

        $user_token   = $request->param('user_token', '', 'trim');
        $access_token = $request->param('access_token', '', 'trim');

        if (!empty($user_token)) {
            $param['user_token']   = $user_token;
            $param['access_token'] = $access_token;
        }

        // 方法2：如果 formData 没有，则尝试从 Authorization Header 获取
        if (empty($param['user_token'])) {
            $authHeader = $request->header('authorization', '');
            if (!empty($authHeader) && preg_match('/^Bearer\s+(\S+)$/i', $authHeader, $matches)) {
                $param['user_token'] = $matches[1]; // 假设 Bearer token 就是 user_token
                // 注意：如果 access_token 不同，需单独处理
            }
        }

        // 方法3：兼容旧的 get_bearer_token() 函数（如果有）
        // if (empty($param['user_token']) && function_exists('get_bearer_token')) {
        //     $token = get_bearer_token();
        //     if ($token) {
        //         $param['user_token'] = $token;
        //     }
        // }

        // 调用逻辑层验证 token
        $usertoken = $this->logicApiBase->checkUserTokenParam($param);

        if (!empty($usertoken['data'])) {
            $userinfo = is_object($usertoken['data']) ? obj2arr($usertoken['data']) : $usertoken['data'];
            if (!defined('MEMBER_ID')) {
                define('MEMBER_ID', $userinfo['id']);
            }
            // 可选：定义更多常量
            // define('MEMBER_NICKNAME', $userinfo['nickname']);
        } else {
            // Token 无效，可选择：不定义 MEMBER_ID，后续接口自行判断
            // 或直接抛出异常
            // throw new \Exception('用户未登录或 token 无效', 401);
        }
    }

    /**检查api接口是否授权通过
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/7/24 0024 10:35
     */
    public function checkUserPermission()
    {
        $this->adminBase = new \app\admin\logic\AdminBase();
        $SysAuthAccess = new \app\admin\logic\SysAuthAccess();

        // 获取授权菜单列表
        $this->authMenuList = $SysAuthAccess->getAuthMenuList(SYS_USER_ID, MODULE_NAME);

        // 获得权限菜单URL列表
        $this->authMenuUrlList = $SysAuthAccess->getAuthMenuUrlList($this->authMenuList);

        // 权限验证不通过则跳转提示
        $url = str_replace('api.', '', URL);

        // 验证权限
        list($jump_type, $message) = $this->adminBase->authCheck($url, $this->authMenuUrlList);
        //RESULT_SUCCESS == $jump_type ?: $this->apiReturn($jump_type, $message);
        if (RESULT_ERROR == $jump_type) {
            $rtn = array('code' => 0, 'msg' => $message);
            echo json_encode($rtn);
            exit;
        }
    }
}
