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

namespace app\cms\logic;
/**
 * 广告列表-》逻辑层
 */
class AdsList extends CmsBase
{
    /**
     * 广告列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getAdsListList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        $list=$this->modelAdsList->getList($where, $field, $order, $paginate);
        foreach ($list as &$row){
            $row['litpic']=get_picture_url($row['litpic']);
            if(!empty($row['litpic2'])){
                $row['litpic2'] =get_picture_url($row['litpic2']);
            }else{
                $row['litpic2'] ='';
            }
        }
        return $list;
    }

    /**
     * 广告列表信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getAdsListInfo($where = [], $field = true)
    {
        return $this->modelAdsList->getInfo($where, $field);
    }

    /**
     * 广告列表内容添加
     */
    public function adsListAdd($data = [])
    {
        $validate_result = $this->validateAdsList->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateAdsList->getError()];
        }
        $result = $this->modelAdsList->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增广告列表内容：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelAdsList->getError()];
    }
    /**
     * 广告列表内容编辑
     */
    public function adsListEdit($data = [])
    {
        $validate_result = $this->validateAdsList->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateAdsList->getError()];
        }
        $url = url('show');
        $result = $this->modelAdsList->setInfo($data);
        $result && action_log('编辑', '编辑广告列表内容：' .$data['title']);
        return $result ? [RESULT_SUCCESS, '编辑成功', $url] : [RESULT_ERROR, $this->modelAdsList->getError()];
    }
    /**
     * 广告列表内容删除
     */
    public function adsListDel($where = [])
    {
        $result = $this->modelAdsList->deleteInfo($where,true);
        $result && action_log('删除', '广告列表内容，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelAdsList->getError()];
    }
}
