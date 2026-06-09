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

namespace app\index\service;

/**
 * 频道栏目管理=》逻辑层
 */
class Channel extends IndexBase
{
    /**
     * 频道栏目列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getChannelList($data=[])
    {
        print_r($data);exit;
        return $this->modelChannel->getList($where, $field, $order, $paginate)->toArray();
    }


}
