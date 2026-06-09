<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * AskCommentor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

use \think\Db;

/**
 * 点评管理=》逻辑层
 */
class AskComment extends MemberBase
{
    /**
     * 点评列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getAskCommentList($where = [], $field = '*', $order = 'a.update_time desc', $paginate = DB_LIST_ROWS)
    {
        $this->modelAskComment->alias('a');
        $list = $this->modelAskComment->getList($where, $field, $order, $paginate);
        foreach ($list as &$row) {
            $row['bus_info'] = $this->modelAskComment->linkBusTypeInfo(array('bus_id' => $row['bus_id'], 'bus_type' => $row['bus_type']));
            $row['comment_user_name'] = $this->modelAskComment->getCommentRealName($row['comment_user_id']);
            $row['receive_user_name'] = $this->modelAskComment->getCommentRealName($row['receive_user_id']);
            $row['reply_list'] = $this->logicAskCommentReply->getAskCommentReplyList(['comment_id' => $row['id']]);
        }
        return $list;
    }

    /**
     * 点评添加
     * @param array $data
     * @return array
     */
    public function askCommentAdd($data = [])
    {
        if (empty($data['content'])) {
            return [RESULT_ERROR, '内容不能为空！'];
        }
        $data['comment_user_id'] = SYS_USER_ID * (-1);
        $result = $this->modelAskComment->setInfo($data);

        $result && $this->modelAskFaq->updateInfo(['id' => $data['bus_id']], ['status' => 2]);//修改faq状态

        $url = url('show');
        $result && action_log('新增', '新增点评：' . $data['content']);
        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelAskComment->getError()];
    }

    /**
     * 点评编辑
     * @param array $data
     * @return array
     */
    public function askCommentEdit($data = [])
    {
        if (empty($data['content'])) {
            return [RESULT_ERROR, '内容不能为空！'];
        }
        $result = $this->modelAskComment->setInfo($data);
        $result && action_log('编辑', '编辑点评，name：' . $data['link_body']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelAskComment->getError()];
    }

    /**
     * 点评删除
     * @param array $where
     * @return array
     */
    public function askCommentDel($data = [])
    {

        if (empty($data['id'])) {
            return [RESULT_ERROR, '确少参数'];
        }

        $where = ['id' => $data['id']];
        $result = $this->modelAskComment->deleteInfo($where, true);

        //删除这个点评的回复
        $map = ['comment_id' => $data['id']];
        $result && $this->modelAskCommentReply->deleteInfo($map, true);

        $result && action_log('删除', '删除点评，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelAskComment->getError()];

    }

    /**
     * 点评信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getAskCommentInfo($where = [], $field = true)
    {
        return $this->modelAskComment->getInfo($where, $field);
    }


    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];
        //关键字查
        !empty($data['keywords']) && $where['content'] = ['like', '%' . $data['keywords'] . '%'];

        //下次联系时间
        if (!empty($data['create_time'])) {
            $range_date = str2arr($data['create_time'], "-");
            $where['a.create_time'] = ['between', [strtotime($range_date[0]), strtotime($range_date[1])]];
        }
        //点评数据类型
        if (!empty($data['listtype'])) {
            switch ($data['listtype']) {
                case 'comment'://我点评过的
                    $where['a.comment_user_id'] = ['=', SYS_USER_ID];
                    break;
                case 'receive'://我被点评过的
                    $where['a.receive_user_id'] = ['=', SYS_USER_ID];
                    break;
            }
        }
        //跟进类型按业务类型
        if (!empty($data['bus_id'])) {
            !empty($data['bus_id']) && $where['bus_id'] = ['=', '' . $data['bus_id'] . ''];
            !empty($data['bus_type']) && $where['bus_type'] = ['=', '' . $data['bus_type'] . ''];
        }
        return $where;
    }

    /**
     * 获取排序条件
     */
    public function getOrderBy($data = [])
    {
        if (!empty($data['orderField'])) {
            $orderField = $data['orderField'];
            $orderDirection = $data['orderDirection'];
        } else {
            $orderField = "";
            $orderDirection = "";
        }
        if ($orderField == 'by_link') {
            $order_by = "a.link_time $orderDirection";
        } else if ($orderField == 'by_next') {
            $order_by = "a.next_time $orderDirection";
        } else {
            $order_by = "a.create_time desc";
        }
        return $order_by;
    }
}
