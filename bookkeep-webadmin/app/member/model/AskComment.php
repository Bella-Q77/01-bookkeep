<?php
// +----------------------------------------------------------------------
// | 07FLYCRM [基于ThinkPHP5.0开发]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2021 http://www.07fly.xyz
// +----------------------------------------------------------------------
// | Professional because of focus  Persevering because of happiness
// +----------------------------------------------------------------------
// | Author: 开发人生 <goodkfrs@qq.com>
// +----------------------------------------------------------------------
namespace app\member\model;

use think\Db;

/**
 * 点评管理=》模型
 */
class AskComment extends MemberBase
{
    /**
     * 问题的类型
     * @param $key
     * @return string|string[]
     * @author: 开发人生 goodkfrs@qq.com
     * @Time: 2022/11/8 15:34
     */
    public function linkBusType($key = '')
    {
        $dataArr = array(
            "ask_faq" => "问题列表",
            "cst_customer" => "客户",
        );
        if (!empty($key)) {
            if (!empty($dataArr[$key])) {
                return $dataArr[$key];
            } else {
                return '';
            }
        }
        return $dataArr;
    }

    /**
     * 问题的类型
     * @param $key
     * @return string|string[]
     * @author: 开发人生 goodkfrs@qq.com
     * @Time: 2022/11/8 15:34
     */
    public function linkBusTypeInfo($data = [])
    {
        $rtn = array(
            'type' => '其它业务',
            'name' => '---',
            'id' => '0',
            'url' => '',
        );

        //判断模块
        $module_name = 'member';
        if (!empty($data['bus_type'])) {
            switch ($data['bus_type']) {
                case "ask_faq":
                    if (tableExists("ask_faq")) {
                        $info = Db::name('ask_faq')->where(['id' => $data['bus_id']])->find();
                        $rtn = [
                            'type' => '问题信息',
                            'name' => $info['name'],
                            'id' => $info['id'],
                            'url' => url($module_name . '/AskFaq/detail', array('id' => $info['id'])),
                        ];
                        break;
                    }
                    break;
            }
        }
        return $rtn;
    }

    //获取真实姓名
    public function getCommentRealName($id)
    {
        if ($id > 0) {
            $name = '';
            $member = Db::name('member')->where('id', $id)->field('username,nickname')->find();
            if ($member) {
                $name = !empty($member['nickname']) ? $member['nickname'] : $member['username'];
            }
        } else {
            $name = '系统:';
            $id = abs($id);//负数转为正数
            $sysuser = Db::name('sys_user')->where('id', $id)->field('username,realname')->find();
            if ($sysuser) {
                $name .= !empty($sysuser['realname']) ? $sysuser['realname'] : $sysuser['username'];
            }
        }
        return $name;
    }
}