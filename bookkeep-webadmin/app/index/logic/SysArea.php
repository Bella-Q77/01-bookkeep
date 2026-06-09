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
use \think\Session;

/**
 * 频道栏目管理=》逻辑层
 */
class SysArea extends IndexBase
{


    /**获得市场列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/3/14 0014 11:20
     */
    public function getSysAreaList($where = [], $field = true, $order = '', $paginate = 15)
    {

        $this->modelSysArea->alias('a');
        $list = $this->modelSysArea->getList($where, $field, $order, $paginate)->toArray();
        return $list;
    }

    /**获得城市信息
     * @param array $where
     * @param bool $field
     * @return mixed
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/3/14 0014 11:20
     */
    public function getSysAreaInfo($where = [], $field = true)
    {
        return $this->modelSysArea->getInfo($where, $field);
    }

    /**获得字段值
     * @param array $where
     * @param bool $field
     * @return mixed
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/3/14 0014 11:20
     */
    public function getSysAreaFieldValue($where = [], $field = true)
    {
        return $this->modelSysArea->getValue($where, $field);
    }

    /**
     * 根据IP地区，判断使用地区，默认为成都，1
     *
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/8/18 0018
     */
    public function getSysAreaDefaultInfo()
    {
        $city = get_city();
        $where['name'] = ['like', '%' . $city . '%'];
        $info = $this->modelSysArea->getInfo($where, true);
        if ($info) {
            Session::set('sys_city_name', $info['name']);
            Session::set('sys_city_id', $info['id']);
            Session::set('sys_city', $info);
        } else {
            Session::set('sys_city_name', '成都市');
            Session::set('sys_city_id', '1');
        }
    }

    /**
     * 根据域名，判断使用地区，默认为成都，1
     *
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/8/18 0018
     */
    public function getDomainSysAreaInfo()
    {
        $where['domain'] = ['like', '%' . DOMAIN . '%'];
        $info = $this->modelSysArea->getInfo($where, true);
        if ($info) {
            is_object($info) && $info = $info->toArray();
            Session::set('sys_city_name', $info['name']);
            Session::set('sys_city_id', $info['id']);
            Session::set('sys_city', $info);
        } else {
            $map['name'] = ['like', '%成都%'];
            $info = $this->modelSysArea->getInfo($map, true);
            is_object($info) && $info = $info->toArray();
            Session::set('sys_city_name', '成都');
            Session::set('sys_city_id', '1');
            Session::set('sys_city', $info);
        }
    }

    /**
     * 根据IP地区，判断使用地区，默认为成都，1
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/8/18 0018
     */
    public function setSysAreaInfo($data = [])
    {
        $where['id'] = ['=', $data['id']];
        $info = $this->modelSysArea->getInfo($where, true);
        if ($info) {
            Session::set('sys_city_name', $info['name']);
            Session::set('sys_city_id', $info['id']);
            Session::set('sys_city', $info);
            if (!empty($info['domain'])) {
                $url = $info['domain'];
                Header("HTTP/1.1 303 See Other");
                Header("Location: $url");
                exit;
            }
        } else {
            Session::set('sys_city_name', '成都市');
            Session::set('sys_city_id', '1');
        }
    }
}