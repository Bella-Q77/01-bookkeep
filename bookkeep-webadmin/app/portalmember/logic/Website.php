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

namespace app\portalmember\logic;

use \think\Db;

/**
 * 网站配置列表=》逻辑层
 */
class Website extends PortalmemberBase
{

    /**
     * 列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getWebsiteList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $this->modelWebsite->alias('a');
        $list = $this->modelWebsite->getList($where, $field, $order, $paginate)->toArray();

        $paginate === false && $list['data'] = $list;

        foreach ($list['data'] as &$row) {
            // $row['litpic'] =get_picture_url($row['litpic']);
            // $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }
        return $list;
    }

    /**返回配置列表值
     * @param null $name
     * @return mixed
     * Author: lingqifei created by at 2020/3/16 0016
     */
    public function getWebsiteConfig($name = null)
    {
        $list = $this->modelWebsite->getList('', true, true, false);
        foreach ($list as $row) {
            $config[$row['name']] = $row['value'];
        }
        if (empty($name)) {
            return $config;
        }else if (isset($config[$name]) && $name) {
            return $config[$name];
        } else {
            return $name . '标签不存在了~';
        }
    }
}
