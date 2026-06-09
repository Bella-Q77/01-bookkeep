<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberPictureor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 会员图片管理=》逻辑层
 */
class MemberPicture extends MemberBase
{
    /**
     * 会员图片列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberPictureList($where = [], $field = true, $order = 'a.status asc,a.update_time desc', $paginate = DB_LIST_ROWS)
    {
        $this->modelMemberPicture->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'member m', 'm.id = a.member_id','LEFT'],
        ];
        $this->modelMemberPicture->join = $join;
        $list= $this->modelMemberPicture->getList($where, $field, $order, $paginate)->toArray();
        if($paginate===false) $list['data']=$list;
        foreach ($list['data'] as &$row){
            $row['status_arr']=$this->modelMemberPicture->status($row['status']);
            $row['path_img']=get_picture_url($row['path']);
        }
        return $list;
    }

    /**
     * 会员图片添加
     * @param array $data
     * @return array
     */
    public function memberPictureAdd($data = [])
    {

        $validate_result = $this->validateMemberPicture->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberPicture->getError()];
        }
        $result = $this->modelMemberPicture->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增会员图片：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelMemberPicture->getError()];
    }

    /**
     * 会员图片编辑
     * @param array $data
     * @return array
     */
    public function memberPictureEdit($data = [])
    {

        $validate_result = $this->validateMemberPicture->scene('edit')->check($data);

        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberPicture->getError()];
        }

        $result = $this->modelMemberPicture->setInfo($data);
        $result && action_log('编辑', '编辑会员图片，name：' . $data['name']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberPicture->getError()];
    }

    /**
     * 会员图片删除
     * @param array $where
     * @return array
     */
    public function memberPictureDel($where = [])
    {

        $result = $this->modelMemberPicture->deleteInfo($where,true);

        $result && action_log('删除', '删除会员图片，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelMemberPicture->getError()];
    }

    /**
     * 会员图片审核
     * @param array $data
     * @return array
     */
    public function memberPictureAudit($data = [])
    {

        $validate_result = $this->validateMemberPicture->scene('audit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberPicture->getError()];
        }

        $result = $this->modelMemberPicture->setInfo($data);
        $result && action_log('审核', '审核会员图片：' . $data['id'].'='.$data['status'] );
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功',''] : [RESULT_ERROR, $this->modelMemberPicture->getError()];
    }

    /**会员图片信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberPictureInfo($where = [], $field = true)
    {

        return $this->modelMemberPicture->getInfo($where, $field);
    }

    /**
     * 查询条件组合
     * @param array $data
     * @return array|mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/6 0006
     */
    public function getWhere($data=[]){

        $where = [];
        if (!empty($data['keywords'])) {
            $where['name|reply_remark'] = ['like', '%' . $data['keywords'] . '%'];
        }

        if (!empty($data['status']) || is_numeric($data['status'])) {
            $where['a.status'] = ['=', $data['status']];
        }
        if (!empty($data['member_id'])) {
            $where['a.member_id'] = ['=', $data['member_id']];
        }
        return $where;
    }
    public function getOrderBy($data = [])
    {
        // 默认排序
        $order_by = "a.id desc";

        // 排序映射关系
        $orderMap = [
            'update_time' => 'a.update_time',
            'create_time' => 'a.create_time'
        ];

        // 检查是否有排序字段
        if (!empty($data['orderField']) && isset($orderMap[$data['orderField']])) {
            $orderField = $orderMap[$data['orderField']];
            $orderDirection = !empty($data['orderDirection']) ? $data['orderDirection'] : 'asc';
            $order_by = "{$orderField} {$orderDirection}";
        }
        return $order_by;
    }
    public function getStatus($key=''){
        return $this->modelMemberPicture->status($key);
    }

}
