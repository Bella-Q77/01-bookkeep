<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * AskCommentReplyor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 点评管理=》逻辑层
 */
class AskCommentReply extends PortalmemberBase
{
    /**
     * 点评列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getAskCommentReplyList($where = [], $field = '*', $order = 'id asc', $paginate = false)
    {
        $list = $this->modelAskCommentReply->getList($where, $field, $order, $paginate);
        foreach ($list as &$row) {
            $row['reply_user_name'] = $this->modelAskComment->getCommentRealName($row['reply_user_id']);
            $row['receive_user_name'] = $this->modelAskComment->getCommentRealName($row['receive_user_id']);
        }
        return $list;
    }

    /**
     * 点评添加
     * @param array $data
     * @return array
     */
    public function askCommentReplyAdd($data = [])
    {
        if (empty($data['content'])) {
            return [RESULT_ERROR, '内容不能为空！'];
        }
        //回复人员
        $data['reply_user_id'] = empty($data['member_id']) ? MEMBER_ID : $data['member_id'];
        $result = $this->modelAskCommentReply->setInfo($data);

        $condition['id'] = ['=',$data['bus_id']];
        $condition['status'] = ['=',2];//已回复=》待处理
        $result && $this->modelAskFaq->updateInfo($condition, ['status' => 1]);//修改faq状态

        $url = url('show');
        $result && action_log('新增', '新增点评回复：' . $data['content']);
        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelAskCommentReply->getError()];
    }

    /**
     * 点评删除
     * @param array $where
     * @return array
     */
    public function askCommentReplyDel($where = [])
    {
        $result = $this->modelAskCommentReply->deleteInfo($where, true);
        $result && action_log('删除', '删除点评，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelAskCommentReply->getError()];
    }


    //获取点评回复信息
    public function getAskCommentReplyInfo($data=[])
    {
        //点评详细
        $info = $this->modelAskComment->getInfo(['id' => $data['comment_id']], '*');
        //设置回复的对象
        if(!empty($data['receive_user_id'])){
            $info['receive_user_id']=$data['receive_user_id'];
        }else{
            $info['receive_user_id']= $info['comment_user_id'];
        }
        $info['receive_user_name']= $this->modelAskComment->getCommentRealName($info['receive_user_id']);

        return $info;
    }
}
