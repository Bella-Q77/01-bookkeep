<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberAdvor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员广告管理=》逻辑层
 */
class MemberAdv extends MemberBase
{
    /**
     * 会员广告列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberAdvList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        $list= $this->modelMemberAdv->getList($where, $field, $order, $paginate);
        if($paginate===false) $list['data']=$list;
        foreach ($list as &$row){
            $row['ad_type_info']=$this->modelMemberAdv->ad_type($row['ad_type']);
            $row['adv_dis_cnt']=$this->modelMemberAdvDis->stat(['adv_id'=>$row['id']],'count','id');
        }
        return $list;
    }

    /**
     * 会员广告添加
     * @param array $data
     * @return array
     */
    public function memberAdvAdd($data = [])
    {

        $validate_result = $this->validateMemberAdv->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberAdv->getError()];
        }
        $result = $this->modelMemberAdv->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员广告：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberAdv->getError()];
    }

    /**
     * 会员广告编辑
     * @param array $data
     * @return array
     */
    public function memberAdvEdit($data = [])
    {

        $validate_result = $this->validateMemberAdv->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateMemberAdv->getError()];
        }

        $url = url('show');

        $result = $this->modelMemberAdv->setInfo($data);

        $result && action_log('编辑', '编辑会员广告，name：' . $data['level_name']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberAdv->getError()];
    }

    /**
     * 会员广告删除
     * @param array $where
     * @return array
     */
    public function memberAdvDel($where = [])
    {

        $result = $this->modelMemberAdv->deleteInfo($where,true);

        $result && action_log('删除', '删除会员广告，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberAdv->getError()];
    }

    /**会员广告信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberAdvInfo($where = [], $field = true)
    {

        return $this->modelMemberAdv->getInfo($where, $field);
    }

}
