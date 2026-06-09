<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberIntegralor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员积分管理=》逻辑层
 */
class MemberIntegral extends PortalmemberBase
{

    /**
     * 会员积分=》配置调用
     * @param array $data
     * @return array
     */
    public function memberIntegralAdd($type, $member_id = 0)
    {

        if (empty($type) || empty($member_id)) {
            return [RESULT_ERROR, '输入必要参数 type integral '];
            exit;
        }

        $conf = $this->logicMemberConfig->getMemberConfig($type);

        $member = $this->logicMember->getMemberInfo(['id' => $member_id]);

        $member_integral_new=$conf['value'] + $member['member_integral'];

        if($member_integral_new<0){
            return [RESULT_ERROR, '会员积分不足，您充值后再操作 '];
            exit;
        }

        $result = false;
        switch ($type) {
            case "member_login"://24小时内算一次
                if ((TIME_NOW - $member['last_login']) > 400) {
                    $intodata = [
                        'member_id' => $member_id,
                        'integral' => $conf['value'],
                        'member_integral' => $member_integral_new,
                        'cause' => $conf['desc'],
                        'cause_type' => $type,
                    ];
                    $result = $this->modelMemberIntegral->setInfo($intodata);
                    $result && $this->modelMember->setFieldValue(['id' => $member_id], 'member_integral', $member_integral_new);
                }
            default :
                $intodata = [
                    'member_id' => $member_id,
                    'integral' => $conf['value'],
                    'member_integral' => $member_integral_new,
                    'cause' => $conf['desc'],
                    'cause_type' => $type,
                ];
                $result = $this->modelMemberIntegral->setInfo($intodata);
                $result && $this->modelMember->setFieldValue(['id' => $member_id], 'member_integral', $member_integral_new);
                break;
        }

        //return $result ? [RESULT_SUCCESS, '编辑成功'] : [RESULT_ERROR, $this->modelMemberIntegral->getError()];
    }


    /**会员积分信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberIntegralInfo($where = [], $field = true)
    {
        return $this->modelMemberIntegral->getInfo($where, $field);
    }

    /**
     * 会员积分列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getMemberIntegrallList($where = [], $field = true, $order = 'create_time desc', $paginate = DB_LIST_ROWS)
    {
        $list= $this->modelMemberIntegral->getList($where, $field, $order, $paginate);
        return $list;
    }

}
