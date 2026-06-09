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
 * 会员配置管理=》逻辑层
 */
class MemberConfig extends PortalmemberBase
{


    /**
     * 会员配置参数
     *
     * @param string $key
     * @return array|mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/26 0026
     */
    public function getMemberConfig($key=''){
        $list=$this->getMemberConfigColumn('','value,desc','name');
        return empty($key)?'':$list[$key];
    }



    /**
     * 会员配置管理=>id=key name=value
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return array
     */
    public function getMemberConfigColumn($where = [],$field='value',$key='name')
    {
        $cache_key = 'cache_getMemberConfigColumn_' . md5(serialize($where));
        $cache_list = cache($cache_key);
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $list = $this->modelMemberConfig->getColumn($where,$field,$key);
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

}
