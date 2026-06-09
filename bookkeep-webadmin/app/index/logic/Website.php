<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Channelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\logic;
use \think\Db;
/**
 * 网站配置列表=》逻辑层
 */
class Website extends IndexBase
{

    /**返回配置列表值
     * @param null $name
     * @return mixed
     * Author: lingqifei created by at 2020/3/16 0016
     */
    public function getWebsiteConfig($name=null)
    {
        $this->modelWebsite->alias('a');
        $list= $this->modelWebsite->getList('', true,true, false)->toArray();
        foreach ($list as $row){
            $config[$row['name']] =$row['value'];
        }
        if(isset($config[$name]) && $name){
            return $config[$name];
        }else{
            return $name.'标签不存在了~';
        }
    }

    /**
     * 获取配置列表
     * @param array $where
     * @param string $field
     * @param string $key
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/21 0021
     */
    public function getWebsiteConfigColumn($where = [], $field = 'value', $key='name')
    {
        $info = $this->modelWebsite->getColumn($where, $field,$key);
        return $info;
    }

}
