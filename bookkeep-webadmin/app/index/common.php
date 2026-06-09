<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Author: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

use app\admin\logic\Log as LogicLog;

/**
 * 记录行为日志
 */
function action_log($name = '', $describe = '')
{

    $logLogic = get_sington_object('logLogic', LogicLog::class);

    $logLogic->logAdd($name, $describe);
}


//得到把列表数据=》数形参数
function list2tree($list, $pId = 0, $level = 0, $pk = 'id', $pidk = 'pid', $name = 'name')
{
    $tree = [];
    foreach ($list as $k => $v) {
        if ($v[$pidk] == $pId) { //父亲找到儿子
            $v['nodes'] = list2tree($list, $v[$pk], $level + 1, $pk, $pidk, $name);
            $v['level'] = $level + 1;
            $v['treename'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '|--' . $v[$name];
            $v['tags'] = $v['id'];
            $v['text'] = $v[$name];
            $tree[] = $v;
        }
    }
    return $tree;
}

if (!function_exists('is_mobile')) {
    /**判断是手机还是电脑
     * @return bool|null
     * Author: lingqifei created by at 2020/4/27 0027
     */
    function is_mobile()
    {
        static $is_mobile = null;

        if (isset($is_mobile)) {
            return $is_mobile;
        }

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $is_mobile = false;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }
        return $is_mobile;
    }
}

if (!function_exists('get_ip')) {
    /**
     * //获得访客的IP
     * @return 0.0.0.0.
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/12/9 0009
     */
    function get_ip()
    {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match("/^(10│172.16│192.168)./", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
}

if (!function_exists('get_city')) {
    /**
     * 根据ip地址查询城市名称=》查询库中城市的IP地址
     * @return string
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/12/9 0009
     */
    function get_city()
    {
        $ip = get_ip();
        $api_url = "https://restapi.amap.com/v3/ip?ip=$ip&key=d775fd6b51c31589776004b109d43ff7";
        //根据IP地址定位所在城市
        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];
        $res = file_get_contents($api_url,false, stream_context_create($arrContextOptions));
        $res = json_decode($res, true);
        if (!empty($res['city'])) {
            return $res['city'];
        } else {
            return '';
        }
    }
}

if (!function_exists('array_rand_value')){

    /**
     * 随机选择数组中的值
     * array_rand（）随机选择数组中KEY值
     * @param $array
     * @param $num
     * @return array
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/12/9 0009
     */
    function array_rand_value($array, $num) {
        $radn_value = [];
        $rand_key = (array)array_rand($array, $num);
        foreach ($rand_key as $k) {
            $radn_value[] = $array[$k];
        }
        return $radn_value;
    }

}


function getTagData($str, $start, $end)
{
    if ($start == '' || $end == '') {
        return;
    }
    $str = explode($start, $str);
    $str = explode($end, $str[1]);
    return $str[0];
}

/*
 * 远程抓取数据函数
 *
 * $url           远程URL地址 必选
 * $way         1为file_get_contents抓取  2为CURL抓取  默认为1 可留空
 * $$coding  编码  1为UTF-8转GBK  1为GBK转UTF-8  留空为不转换
 *
 * 作者: 小曾  QQ839024615 欢迎加我一起交流!
 *
 */
function GetFile($url, $way = 1, $coding='1')
{
    if ($way == 1) {
        $str = file_get_contents($url);
    } else if ($way == 2) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $contents = curl_exec($ch);
        curl_close($ch);//关闭一打开的会话
    }
    if ($coding == "1") {
        $html = mb_convert_encoding($contents, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
    } elseif ($coding == "2") {
        $html = mb_convert_encoding($contents, 'GBK', 'UTF-8,GBK,GB2312,BIG5');
    }
    return $html;
}

function getAllURL($code){
    preg_match_all('/<a\s+href=["|\']?([^>"\' ]+)["|\']?\s*[^>]*>([^>]+)<\/a>/i',$code,$arr);
    return array('name'=>$arr[2],'url'=>$arr[1]);
}