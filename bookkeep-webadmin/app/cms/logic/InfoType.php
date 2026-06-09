<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * InfoTypeor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2020-02-12
 */

namespace app\cms\logic;

use app\common\logic\TableField;

/**
 * 栏目逻辑
 */
class InfoType extends CmsBase
{

    private $tablefield = null;

    // 菜单Select结构
    public static $dataSelect   = [];

    /**
     * 析构函数
     */
    function  __construct() {
        $this->tablefield = new TableField();
    }

    /**
     * 模型管理处列表
     */
    public function getInfoTypeList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS, $limit = DB_LIST_ROWS)
    {

        if ($paginate === false && $limit!=false) {
            $this->modelInfoType->limit = $limit;
        }
        $list = $this->modelInfoType->getList($where, $field, $order, $paginate);
        return $list;
    }
    
    /**
     * 模型添加
     */
    public function infoTypeAdd($data = [])
    {
        
        $validate_result = $this->validateInfoType->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateInfoType->getError()];
        }

        $url = url('show');

        $result = $this->modelInfoType->setInfo($data);
        $result && action_log('新增', '新增cms栏目，name：' . $data['typename']);
        return $result ? [RESULT_SUCCESS, '栏目添加成功', $url] : [RESULT_ERROR, $this->modelInfoType->getError()];
    }
    
    /**
     * 模型编辑
     */
    public function infoTypeEdit($data = [])
    {
        
        $validate_result = $this->validateInfoType->scene('edit')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateInfoType->getError()];
        }
        $result = $this->modelInfoType->setInfo($data);
        $result && action_log('编辑', '编辑栏目，name：' . $data['typename']);
        $url = url('show');
        return $result ? [RESULT_SUCCESS, '栏目编辑成功', $url] : [RESULT_ERROR, $this->modelInfoType->getError()];
    }
    
    /**
     * 模型删除
     */
    public function infoTypeDel($where = [])
    {

        $result = $this->modelInfoType->deleteInfo($where,true);
        
        $result && action_log('删除', '删除模型，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '模型删除成功'] : [RESULT_ERROR, $this->modelInfoType->getError()];
    }
    
    /**
     * 模型管理处信息
     */
    public function getInfoTypeInfo($where = [], $field = true)
    {

        $info= $this->modelInfoType->getInfo($where, $field);

        return $info;

    }

    /**
     * 获得栏目信息--》详细内容
     * @param int $typeid
     * @return
     */
    public function getInfoTypeInfoDetail($typeid=0)
    {
        $where['a.id']=['=',$typeid];

        $this->modelInfoType->alias('a');
//        $join = [
//            [SYS_DB_PREFIX . 'channel c', 'c.id = a.channel_id'],
//        ];
//
//        $this->modelInfoType->join = $join;
        $info=$this->modelInfoType->getInfo($where, 'a.*,c.nid,c.maintable,c.addtable');
        is_object($info) && $info->toArray();
        return $info;
    }


    //得到数形参数
    public function getInfoTypeListTree($where='')
    {
        $list = $this->getInfoTypeList($where,'','sort asc',false,false);
        $tree= list2tree($list,0,0,'id','parent_id','typename');
        return $tree;
    }

    //得到数形参数
    public function getInfoTypeListSelect($where='')
    {
        $list = $this->getInfoTypeList($where,'','sort asc',false,false)->toArray();
        $tree= list2select($list,0,0,'id','parent_id','typename');
        return $tree;
    }

    /**获得所有指定id所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeAllPid($typeid=0, $data=[])
    {
        $where['id']=['=',$typeid];
        $info = $this->modelInfoType->getInfo($where,true);
        if(!empty($info) && $info['parent_id']){
            $data[]=$info['parent_id'];
            return $this->getInfoTypeAllPid($info['parent_id'],$data);
        }
        return $data;
    }

    /**获得所有指定id所有子级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeAllSon($typeid=0, $data=[])
    {
        $where['parent_id']=['=',$typeid];
        $sons = $this->modelInfoType->getList($where,true,'sort asc',false);
        if (count($sons) > 0) {
            foreach ($sons as $v) {
                $data[] = $v['id'];
                $data = $this->getInfoTypeAllSon($v['id'], $data); //注意写$data 返回给上级
            }
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return false;
        }
        return $data;
    }

    /**获得所有指定id 所有同级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeAllSelf($typeid=0, $data=[])
    {

        $pid = $this->modelInfoType->getValue(['id'=>$typeid],'parent_id');
        $where['parent_id']=['=',$typeid];
        $data = $this->modelInfoType->getColumn(['parent_id'=>$pid],'id');
        return $data;
    }

}
