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
 * 广告列表管理=》逻辑层
 */
class MemberAdvDis extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getMemberAdvDisList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS, $limit = DB_LIST_ROWS)
    {
        $this->modelMemberAdvDis->alias('a');
        $list = $this->modelMemberAdvDis->getList($where, $field, $order, $paginate)->toArray();
        $paginate === false && $this->modelMemberAdvDis->limit($limit);
        foreach ($list as &$row) {
            $row['litpic'] = get_picture_url($row['litpic']);
            $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }
        return $list;
    }

    /**信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberAdvDisInfo($where = [], $field = true)
    {
        $info = $this->modelMemberAdvDis->getInfo($where, $field);
        $info['target'] = ($info['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        return $info;
    }

    /**设置文章点击
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function updateMemberAdvDisView($where = [])
    {
        $view = $this->modelMemberAdvDis->getValue($where, 'view');
        $view = (int)$view + 1;
        $this->modelMemberAdvDis->setFieldValue($where, 'view', $view);

    }

    /**设置文章点击
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function updateMemberAdvDisClick($where = [], $field = true)
    {
        $click = $this->modelMemberAdvDis->getValue($where, 'click');
        if ($click) {
            $click = (int)$click + 1;
            $this->modelInfo->setFieldValue($where, 'click', $click);
        }
    }

}
