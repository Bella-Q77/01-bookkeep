<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * AskFaqor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\member\logic;

/**
 * 问题列表管理=》逻辑层
 */
class AskFaq extends MemberBase
{
    /**
     * 问题列表列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getAskFaqList($where = [], $field = 'a.*', $order = 'a.update_time desc', $paginate = DB_LIST_ROWS)
    {
        $this->modelAskFaq->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'faq_type t', 't.id = t.type_id', 'LEFT'],
        ];
        //$this->modelAskFaq->join=$join;
        $list = $this->modelAskFaq->getList($where, $field, $order, $paginate)->toArray();
        foreach ($list['data'] as &$row) {
            $row['member_name'] = $this->modelMember->getValue(['id' => $row['member_id']], 'username');
            $row['status_text'] = $this->modelAskFaq->getStatusText($row['status']);
        }
        return $list;
    }

    /**
     * 问题列表添加
     * @param array $data
     * @return array
     */
    public function askFaqAdd($data = [])
    {
        if (empty($data['name']) || empty($data['content'])) {
            return [RESULT_ERROR, '请填写问题名称和内容'];
        }

        $data['member_id'] = empty($data['member_id']) ? MEMBER_ID : $data['member_id'];
        $result = $this->modelAskFaq->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增问题列表：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '添加成功', $url, $result] : [RESULT_ERROR, $this->modelAskFaq->getError()];
    }

    /**
     * 问题列表编辑
     * @param array $data
     * @return array
     */
    public function askFaqEdit($data = [])
    {

        $validate_result = $this->validateAskFaq->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateAskFaq->getError()];
        }

        $result = $this->modelAskFaq->setInfo($data);

        $result && action_log('编辑', '编辑问题列表，name：' . $data['link_body']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelAskFaq->getError()];
    }

    /**
     * 问题列表删除
     * @param array $where
     * @return array
     */
    public function askFaqDel($where = [])
    {

        $result = $this->modelAskFaq->deleteInfo($where, true);

        $result && action_log('删除', '删除问题列表，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelAskFaq->getError()];
    }

    /**
     * 问题列表信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getAskFaqInfo($where = [], $field = true)
    {
        $info = $this->modelAskFaq->getInfo($where, $field);
        if (!empty($info)) {
            $info['member_info'] = $this->modelMember->getInfo(['id' => $info['member_id']], '*');
        }
        return $info;
    }

    /**
     * 问题详细
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getAskFaqDetail($data = [])
    {
        $info = array();
        if (!empty($data['id'])) {
            $info = $this->modelAskFaq->getInfo(['id' => $data['id']], "*");
            if (!empty($info)) {

            }
        }
        return $info;
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
        $where = [];
        //关键字查
        !empty($data['keywords']) && $where['a.name|a.content'] = ['like', '%' . $data['keywords'] . '%'];

        !empty($data['member_id']) && $where['a.member_id'] = ['=', $data['member_id']];

        if (!empty($data['create_time'])) {
            $rangetime = rangedate2arr($data['create_time'], '-', 'int');
            $where['a.create_time'] = ['between', $rangetime];
        }

        return $where;
    }

    /**
     * 获取排序条件
     */
    public function getOrderBy($data = [])
    {
        $orderField = isset($data['orderField']) ? $data['orderField'] : '';
        $orderDirection = isset($data['orderDirection']) ? $data['orderDirection'] : '';

        // 定义允许的排序字段映射
        $allowedFields = [
            'link_time' => 'a.link_time',
            'next_time' => 'a.next_time',
            'create_time' => 'a.create_time',
            'update_time' => 'a.update_time'
        ];

        // 如果排序字段合法，则使用指定的排序，否则使用默认排序
        if (isset($allowedFields[$orderField])) {
            $order_by = $allowedFields[$orderField] . ' ' . $orderDirection;
        } else {
            $order_by = 'a.id desc';
        }

        return $order_by;
    }

    /**
     * 列表下载
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getAskFaqListDown($where = [], $field = '', $order = 'a.id desc', $paginate = false)
    {
        $this->modelAskFaq->alias('a');
        $join = [
            [SYS_DB_PREFIX . 'cst_customer c', 'c.id = a.customer_id', 'LEFT'],
        ];
        $this->modelAskFaq->join = $join;
        $list = $this->modelAskFaq->getList($where, $field, $order, $paginate)->toArray();

        foreach ($list as &$row) {
            $row['customer_name'] = $this->modelCstCustomer->getValue(['id' => $row['customer_id']], 'name');
            $row['linkman_name'] = $this->modelCstLinkman->getValue(['id' => $row['linkman_id']], 'name');
            $row['chance_name'] = $this->modelCstLinkman->getValue(['id' => $row['chance_id']], 'name');
        }

        $titles = "联系时间,联系方式,客户名称,沟通内容,下次联系时间,当前状态,创建时间,更新时间,创建人";
        $keys = "link_time,salemode,customer_name,link_body,next_time,salestage,create_time,update_time,create_user_name";

        action_log('下载', '沟通记录列表');
        export_excel($titles, $keys, $list, '沟通记录列表');
    }

}
