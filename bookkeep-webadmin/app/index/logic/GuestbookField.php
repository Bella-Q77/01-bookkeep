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

/**
 * 模型字段逻辑
 */
class GuestbookField extends IndexBase
{
    /**
     * 模型字段处列表
     */
    public function getExtTableFieldList($main_table=null,$ext_table=null)
    {
        if($main_table && $ext_table){
            $where['main_table']=['=',$main_table];
            $where['ext_table']=['=',$ext_table];
            return $this->modelGuestbookField->getList($where, true, 'sort asc', false)->toArray();
        }
    }
}
