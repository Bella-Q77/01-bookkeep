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

/**
 * 点评管理=》模型
 */
class AskCommentReply extends MemberBase
{
    /**
     * 微信通知数据=>点评
     * @param array $data
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/5/19 0019 17:10
     */
    public function send_weixin($data = [])
    {
        $sendData = [];
        $user = $this->logicSysUser->getSysUserInfo(['id' => $data['receive_user_id']], 'open_id,realname');//推荐微信的id
        if (!empty($user['open_id'])) {
            $openid = $user['open_id'];
            $realname = $user['realname'];
            //设置业务的url
            switch ($data['bus_type']) {
                case 'cst_chance':
                    $url = DOMAIN . url('CstChance/detail', array('id' => $data['bus_id']));
                    $bus_name = '商机-' . $data['bus_id'];
                case 'cst_trace':
                    $url = DOMAIN . url('CstTrace/detail', array('id' => $data['bus_id']));
                    $bus_name = '跟进-' . $data['bus_id'];
            }

            $weixin = new WeixinPortal();
            $sendData = array('touser' => $openid,   //发给谁
                'template_id' => 'wj21zDZG6CUgI3NDyun6S9u--xycY4QxgtIHrpfmxqA',   //模板id
                'url' => $url,     //这个是你发送了模板消息之后，当用户点击时跳转的连接
                'topcolor' => "#FF0000",   //颜色
                'miniprogram' => '',
                'data' => array(
                    'first' => array(
                        'value' => $realname . '你有新的点评待查看！',
                        'color' => '#173177'
                    ),
                    'keyword1' => array(
                        'value' => format_time(),
                        'color' => '#173177'
                    ),
                    'keyword2' => array(
                        'value' => $bus_name,
                        'color' => '#173177'
                    ),
                    'keyword3' => array(
                        'value' => '点评跟进',
                        'color' => '#173177'
                    ),
                    'keyword4' => array(
                        'value' => '待查看',
                        'color' => '#173177'
                    ),
                    'remark' => array(
                        'value' => '点评内容：' . $data['content'],
                        'color' => '#173177'
                    )
                )
            );
            dlog($sendData);
            $weixin->template($sendData);
        }
        return $sendData;
    }

    /**
     * 微信通知数据=>回复
     * @param array $data
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/5/19 0019 17:10
     */
    public function send_weixin_reply($data = [])
    {
        $sendData = [];
        $user = $this->logicSysUser->getSysUserInfo(['id' => $data['receive_user_id']], 'open_id,realname');//推荐微信的id
        if (!empty($user['open_id'])) {
            $openid = $user['open_id'];
            $realname = $user['realname'];
            //设置业务的url
            switch ($data['bus_type']) {
                case 'cst_chance':
                    $url = DOMAIN . url('CstChance/detail', array('id' => $data['bus_id']));
                    $bus_name = '回复点评-' . $data['bus_id'];
                case 'cst_trace':
                    $url = DOMAIN . url('CstTrace/detail', array('id' => $data['bus_id']));
                    $bus_name = '跟进-' . $data['bus_id'];
            }
            $weixin = new WeixinPortal();
            $sendData = array('touser' => $openid,   //发给谁
                'template_id' => 'wj21zDZG6CUgI3NDyun6S9u--xycY4QxgtIHrpfmxqA',   //模板id
                'url' => $url,     //这个是你发送了模板消息之后，当用户点击时跳转的连接
                'topcolor' => "#FF0000",   //颜色
                'miniprogram' => '',
                'data' => array(
                    'first' => array(
                        'value' => $realname . '你有新的回复待查看！',
                        'color' => '#173177'
                    ),
                    'keyword1' => array(
                        'value' => format_time(),
                        'color' => '#173177'
                    ),
                    'keyword2' => array(
                        'value' => $bus_name,
                        'color' => '#173177'
                    ),
                    'keyword3' => array(
                        'value' => '回复点评',
                        'color' => '#173177'
                    ),
                    'keyword4' => array(
                        'value' => '待查看',
                        'color' => '#173177'
                    ),
                    'remark' => array(
                        'value' => '回复内容：' . $data['content'],
                        'color' => '#173177'
                    )
                )
            );
            //d($sendData);exit;
            $weixin->template($sendData);
        }
        return $sendData;
    }
}
