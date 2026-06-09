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
 * 频道栏目管理=》逻辑层
 */
class MemberAdv extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getMemberAdvList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $this->modelMemberAdv->alias('a');
        $list= $this->modelMemberAdv->getList($where, $field, $order, $paginate)->toArray();

        $paginate===false && $list['data']=$list;

        foreach ($list['data'] as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }

        return $list;
    }

    /**信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getMemberAdvInfo($where = [], $field = true)
    {
        return $this->modelMemberAdv->getInfo($where, $field);
    }


    /**
     * 广告位下=》可显示会员广告
     * @param array $data
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/3/8 0008
     */
    public function getMemberAdvDis($data = [])
    {


        $cache_key = 'getMemberAdvDis_' . md5(serialize($data));
        $cache_list = cache($cache_key);

        $cache_list='';

        if($cache_list){
            $list=$cache_list;
        }else{

            $now_date=format_time(time(),'Y-m-d');
            $list=[];

            //显示展示中
            $one_3 = $this->modelMemberAdvDis->getInfo(['status'=>3], true);
            if($one_3){
                if($one_3['stop_date']<$now_date){
                    $this->modelMemberAdvDis->setFieldValue(['id'=>$one_3['id']], 'status', '4');
                }
                $list=$one_3;
            }

            if(empty($list)){
                $one_2 = $this->modelMemberAdvDis->getInfo(['status'=>2], true);
                $up_data=[
                    'status'=>'3',
                    'start_date'=>$now_date,
                    'stop_date'=>date_calc($now_date,"+".$one_2['period'],'day'),
                ];
                $this->modelMemberAdvDis->updateInfo(['id'=>$one_2['id']],$up_data);
                $list=$one_2;
            }

            //!empty($list) && cache($cache_key, $list,3600*24*$list['days']);
            !empty($list) && cache($cache_key, $list,['expire'=>30]);

            //d($list->toArray());exit;
        }

        return $list;
    }

}
