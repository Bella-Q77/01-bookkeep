<?php

namespace lqf;

use think\Db;

class WxApplet
{

    private $appid='';
    private $secret='';

    public function __construct($appid='wxb20c1e3db6344340', $secret='cb99802ad167c90cf6c59a2571f4f264')
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    /**
     * 通过code获取openid和session_key
     */
    public function getOpenIdByCode($code)
    {
        $api_url = 'https://api.weixin.qq.com/sns/jscode2session';
        $params = [
            'appid' => $this->appid,
            'secret' => $this->secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $url = $api_url . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('请求微信API失败');
        }
        $data = json_decode($response, true);
        if (isset($data['errcode'])) {
            return $data;
        }
        return [
            'openid' => $data['openid'],
            'session_key' => $data['session_key']
        ];
    }
}