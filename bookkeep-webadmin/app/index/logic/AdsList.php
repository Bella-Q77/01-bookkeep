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

namespace app\index\logic;
use \think\Db;
/**
 * 广告列表管理=》逻辑层
 */
class AdsList extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getAdsListList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS,$limit=DB_LIST_ROWS)
    {
        $this->modelAdsList->alias('a');
        $list= $this->modelAdsList->getList($where, $field, $order, $paginate)->toArray();
        $paginate===false && $list['data']=$list;
        foreach ($list['data'] as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
            //兼容手机图片
            if(!empty($row['litpic2'])){
                $row['litpic2'] =get_picture_url($row['litpic2']);
            }else{
               $row['litpic2'] ='';
            }
        }
        return $list;
    }

    /**信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getAdsListInfo($where = [], $field = true)
    {
        $info = $this->modelAdsList->getInfo($where, $field);
        $info['target'] = ($info['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        return $info;
    }

    /**设置文章点击
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function updateAdsListView($where = [])
    {
        $view = $this->modelAdsList->getValue($where, 'view');
        $view=(int)$view+1;
        $this->modelAdsList->setFieldValue($where, 'view',$view);

    }

    /**设置文章点击
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function updateAdsListClick($where = [], $field = true)
    {
        $click = $this->modelAdsList->getValue($where, 'click');
        if($click){
            $click=(int)$click+1;
            $this->modelInfo->setFieldValue($where, 'click',$click);
        }
    }

}
