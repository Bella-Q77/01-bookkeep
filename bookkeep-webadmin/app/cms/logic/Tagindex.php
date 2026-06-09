<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Tagindexor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\cms\logic;

use app\common\logic\TableField;
use think\Db;
/**
 * 网站管理=》逻辑层
 */
class Tagindex extends CmsBase
{
    /**
     * 网站列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getTagindexList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        return $this->modelTagindex->getList($where, $field, $order, $paginate)->toArray();
    }

    /**
     * 标签添加
     */
    public function tagindexAdd($data = [])
    {

        $reg_txt=preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$data['keywords']);
        $result = $this->modelTagindex->setInfo($data);
        $result && action_log('新增', '新增配置，name：' . $data['name']);
        $url = url('tagindexList', array('group' => $data['group'] ? $data['group'] : 0));
        return $result ? [RESULT_SUCCESS, '配置添加成功', $url] : [RESULT_ERROR, $this->modelTagindex->getError()];
    }

    /**
     * 配置编辑
     */
    public function tagindexEdit($data = [])
    {

        $validate_result = $this->validateTagindex->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateTagindex->getError()];
        }

        $result = $this->modelTagindex->setInfo($data);

        $result && action_log('编辑', '编辑配置，name：' . $data['name']);
        $url = url('tagindexList', array('group' => $data['group'] ? $data['group'] : 0));
        return $result ? [RESULT_SUCCESS, '配置编辑成功', $url] : [RESULT_ERROR, $this->modelTagindex->getError()];
    }

    /**
     * 配置删除
     */
    public function tagindexDel($where = [])
    {
        $result = $this->modelTagindex->deleteInfo($where);
        $result && action_log('删除', '删除配置，where：' . http_build_query($where));
        return $result ? [RESULT_SUCCESS, '菜单删除成功'] : [RESULT_ERROR, $this->modelTagindex->getError()];
    }

    public function getTagindexInfo($where = [],$field=true){
        return $this->modelTagindex->getInfo($where, $field);
    }


    /**
     * 文章标签添加接口，针对文档编辑关键字时调用
     * @param $keywords
     * @param $aid
     * @param $typeid
     * @return array
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/3 0003
     */
    public function tagindexAddArchives($keywords, $aid, $typeid)
    {
        $tagindex_list=$this->modelTagindex->getColumn('','tag','id');
        $keywords=preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$keywords);
        $key_array=str2arr($keywords);
        foreach ($key_array as $onetag){
            if(in_array($onetag,$tagindex_list)){
                $tagid=array_search($onetag,$tagindex_list);
            }else{
                $tagid=$this->modelTagindex->setInfo(['tag'=>$onetag,'typeid'=>$typeid]);
            }
            $taglistdata[]=[
                'aid'=>$aid,
                'tid'=>$tagid,
                'typeid'=>$typeid,
                'tag'=>$onetag,
            ];
        }
        $this->modelTaglist->deleteInfo(['aid'=>$aid],true);
        $result = $this->modelTaglist->setList($taglistdata);
        $result && action_log('新增', '文档新增tag标签' . $keywords);
        return $result ? [RESULT_SUCCESS, '配置添加成功'] : [RESULT_ERROR, $this->modelTagindex->getError()];
    }

}
