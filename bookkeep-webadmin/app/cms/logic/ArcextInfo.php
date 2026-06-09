<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Arcextor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\cms\logic;

use app\common\logic\TableField;
use think\Db;

/**
 *  文章扩展项目逻辑
 */
class ArcextInfo extends CmsBase
{

    private $tablefield = null;


    /**
     * 析构函数
     */
    function __construct()
    {
        $this->tablefield = new TableField();
    }

    /**
     * 扩展表信息列表数据
     * @param array $data
     * @return array
     * @throws \think\exception\DbException
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/7/1 0001
     */
    public function getArcextInfoList($data=[])
    {
        if(empty($data['arcext_id'])){
            $map=['channel_id'=>$data['channel_id']];
        }else{
            $map=['id'=>$data['arcext_id']];
        }
        $table=$this->modelArcext->getInfo($map,'addtable,maintable')->toArray();

        //扩展数据处理
        if(!empty($data['arcext_id'])){
            $where['arcext_id']=['=',$data['arcext_id']];
        }
        $where['archives_id']=['=',$data['archives_id']];
        $list=Db::table(SYS_DB_PREFIX.$table['addtable'])
            ->where($where)
            ->order('create_time desc')
            ->paginate(DB_LIST_ROWS)
            ->toArray();
        foreach ($list['data'] as &$row){

        }
        return $list;
    }

    /**
     *  文章扩展内容添加
     */
    public function arcextInfoAdd($data = [])
    {

        $table=$this->getArcextInfoTableInfo($data['arcext_id']);

        $data['create_time']=format_time();
        $result=Db::table(SYS_DB_PREFIX.$table['addtable'])->insert($data);

        $url = url('show');
        $result && action_log('编辑', '编辑内容：' . $data['title']);

        return $result ? [RESULT_SUCCESS, ' 操作成功', $url] : [RESULT_ERROR, $this->modelArcext->getError()];
    }

    /**
     *  文章扩展内容编辑
     */
    public function arcextInfoEdit($data = [])
    {

        $table=$this->getArcextInfoTableInfo($data['arcext_id']);

        $where['arcext_id']=['=',$data['arcext_id']];
        $where['id']=['=',$data['id']];
        $result=Db::table(SYS_DB_PREFIX.$table['addtable'])->where($where)->update($data);

        $url = url('ext_list');
        $result && action_log('编辑', '编辑内容：' . $data['content']);

        return $result ? [RESULT_SUCCESS, ' 操作成功', $url] : [RESULT_ERROR, $this->modelArcext->getError()];
    }

    /**
     *  文章扩展内容删除
     */
    public function arcextInfoDel($data = [])
    {

        $table=$this->getArcextInfoTableInfo($data['arcext_id']);

        $where['arcext_id']=['=',$data['arcext_id']];
        $where['id']=['=',$data['id']];
        $result=Db::table(SYS_DB_PREFIX.$table['addtable'])->where($where)->delete();

        $result && action_log('删除', '删除 留言表单，where：' . http_build_query($data));
        return $result ? [RESULT_SUCCESS, ' 删除成功'] : [RESULT_ERROR, $this->modelArcext->getError()];
    }

    /**
     *  文章扩展内容详细
     */
    public function getArcextInfoInfo($data=[])
    {
        if(empty($data['arcext_id'])){
            return [RESULT_ERROR,'选择表单'];
        }

        $table=$this->getArcextInfoTableInfo($data['arcext_id']);

        $where['arcext_id']=['=',$data['arcext_id']];
        $where['id']=['=',$data['id']];
        $info=Db::table(SYS_DB_PREFIX.$table['addtable'])
            ->where($where)
            ->find();
        $info['litpic_array']=explode(',',$info['litpic']);
        return $info;
    }

    /**
     * 获取表单数据表信息
     * @param int $gid
     * @return mixed
     * Author: lingqifei created by at 2020/3/2 0002
     */
    public function getArcextInfoTableInfo($gid=0)
    {
        $info=$this->modelArcext->getInfo(['id'=>$gid],'addtable,maintable');
        return $info;
    }

}
