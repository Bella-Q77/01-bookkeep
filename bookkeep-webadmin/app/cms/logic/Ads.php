<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Adsor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\cms\logic;

use app\common\logic\TableField;

/**
 * 广告管理=》逻辑层
 */
class Ads extends CmsBase
{
    /**
     * 广告列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getAdsList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        return $this->modelAds->getList($where, $field, $order, $paginate)->toArray();
    }

    /**
     * 广告添加
     * @param array $data
     * @return array
     */
    public function adsAdd($data = [])
    {

        $validate_result = $this->validateAds->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateAds->getError()];
        }
        $result = $this->modelAds->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增广告：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelAds->getError()];
    }

    /**
     * 广告编辑
     * @param array $data
     * @return array
     */
    public function adsEdit($data = [])
    {

        $validate_result = $this->validateAds->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateAds->getError()];
        }

        $url = url('show');

        $result = $this->modelAds->setInfo($data);

        $result && action_log('编辑', '编辑广告，name：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelAds->getError()];
    }

    /**
     * 广告删除
     * @param array $where
     * @return array
     */
    public function adsDel($where = [])
    {

        $result = $this->modelAds->deleteInfo($where,true);

        $result && action_log('删除', '删除广告，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelAds->getError()];
    }

    /**广告信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getAdsInfo($where = [], $field = true)
    {

        return $this->modelAds->getInfo($where, $field);
    }

}
