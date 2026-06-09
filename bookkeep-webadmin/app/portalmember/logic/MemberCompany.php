<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * MemberCompanyor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

/**
 * 会员公司管理=》逻辑层
 */
class MemberCompany extends PortalmemberBase
{

    /**
     * 会员公司编辑
     * @param array $data
     * @return array
     */
    public function memberCompanyEdit($data = [])
    {

        $validate_result = $this->validateMemberCompany->scene('company_edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateMemberCompany->getError()];
        }
        $url = url('portalmember/index/index');
        $result = $this->modelMemberCompany->setInfo($data);
        $result && action_log('编辑', '编辑公司信息，name：' . $data['name']);
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelMemberCompany->getError()];
    }

    /**会员公司信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberCompanyInfo($where = [], $field = true)
    {
        return $this->modelMemberCompany->getInfo($where, $field);
    }

    /**会员公司信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberCompanyInfoAdd($member_id=MEMBER_ID)
    {
        $info= $this->modelMemberCompany->getInfo(['member_id'=>$member_id], true);
        if(empty($info)){
            $this->modelMemberCompany->setInfo(['member_id'=>$member_id]);
        }
        $info= $this->modelMemberCompany->getInfo(['member_id'=>$member_id], true);
        $info['province_name'] = $this->logicRegion->getRegionListName($info['province_id']);
        $info['city_name'] = $this->logicRegion->getRegionListName($info['city_id']);
        $info['county_name'] = $this->logicRegion->getRegionListName($info['county_id']);
        return $info;
    }

}
