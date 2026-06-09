<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberAdvDisor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员购买广告管理=》逻辑层
 */
class MemberAdvDis extends PortalmemberBase
{
    /**
     * 会员购买广告列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberAdvDisList($where = [], $field = 'a.*', $order = 'a.create_time desc', $paginate = DB_LIST_ROWS)
    {
        $this->modelMemberAdvDis->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'member_adv b', 'b.id = a.adv_id'],
        ];

        $this->modelMemberAdvDis->join = $join;

        $list= $this->modelMemberAdvDis->getList($where, $field, $order, $paginate);

        foreach ($list as &$row){
            $row['status_info']=$this->modelMemberAdvDis->status($row['status']);
        }

        return $list;
    }

    /**会员自助广告信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberAdvDisInfo($where = [], $field = true)
    {
        $info=$this->modelMemberAdvDis->getInfo($where, $field);
        return $info;
    }

    /**
     * 会员购买广告删除
     * @param array $where
     * @return array
     */
    public function memberAdvDisDel($data = [])
    {
        $where['id'] = ['in', $data['id']];
        $result = $this->modelMemberAdvDis->deleteInfo($where, true);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '删除成功', $url] : [RESULT_ERROR, $this->modelMemberAdvDis->getError()];
    }

}
