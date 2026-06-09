<?php
// +----------------------------------------------------------------------
// | 07FLYERP [基于ThinkPHP5.0开发]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2021 http://www.07fly.xyz
// +----------------------------------------------------------------------
// | Professional because of focus  Persevering because of happiness
// +----------------------------------------------------------------------
// | Author: 开发人生 <goodkfrs@qq.com>
// +----------------------------------------------------------------------

namespace app\portalmember\controller\api;

use think\Hook;

/**
 * 后台基类控制器
 */
class Member extends PortalmemberBookkeepApiBase
{
    public function getInfo()
    {
        $where = ['id' => MEMBER_ID];
        $result = $this->logicMember->getMemberInfo($where);
        return $this->apiReturn($result);
    }

    public function editInfo()
    {
        $result = $this->logicMember->memberEdit($this->param);
        return $this->apiReturn($result);
    }

    public function changePassword()
    {
        $result = $this->logicMember->memberEditPwd($this->param);
        return $this->apiReturn($result);
    }

    public function avatar()
    {
        // ✅ 处理上传文件
        $result = $this->logicMember->memberEditHeaderPic($this->param);
        return $this->apiReturn($result);
    }
}
