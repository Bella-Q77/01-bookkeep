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
 * 友情链接=》逻辑层
 */
class FriendLink extends IndexBase
{

    /**列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getFriendlinkList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $this->modelFriendlink->alias('a');
        $list= $this->modelFriendlink->getList($where, $field, $order, $paginate)->toArray();

        $paginate===false && $list['data']=$list;

        foreach ($list['data'] as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }
        return $list;
    }


    /**列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getFriendlinkTaglibList($where = [], $limit = '')
    {
        $this->modelFriendlink->alias('a');
        $this->modelFriendlink->limit=$limit;
        $list= $this->modelFriendlink->getList($where, '', '', false)->toArray();
        foreach ($list as &$row){
            $row['links'] =$row['url'];
            $row['logo'] =get_picture_url($row['logo']);
            $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }
        return $list;
    }

}
