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
 * 点赞管理=》逻辑层
 */
class Alikes extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getAdsList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $this->modelAds->alias('a');
        $list= $this->modelAds->getList($where, $field, $order, $paginate)->toArray();

        $paginate===false && $list['data']=$list;

        foreach ($list['data'] as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }

        return $list;
    }

    /**
     * 点赞添加
     */
    public function alikesAdd($data = [])
    {
        if(empty($data['openid'])) $data['openid']='lixiao';
        $map['aid']=['=',$data['aid']];
        $map['openid']=['=',$data['openid']];
        $cnt=$this->modelAlikes->stat($map,'count','aid');
        if($cnt>0){
            return  [RESULT_ERROR, '你已经投过了'];
        }else{
            $result = $this->modelAlikes->setInfo($data);
            return $result ? [RESULT_SUCCESS, '部门添加成功', $result] : [RESULT_ERROR, $this->modelSysDept->getError()];
        }


    }

    /**
     * 点赞添加
     */
    public function alikesNumber($data = [])
    {

        $info = $this->modelAlikes->stat(['aid'=>$data['id']],'count','aid');
        return $info;
    }

}
