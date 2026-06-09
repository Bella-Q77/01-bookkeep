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
 * 信息栏目管理=》逻辑层
 */
class InfoType extends IndexBase
{
    /**
     * 栏目列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getInfoTypeList($where = [], $field = true, $order = '', $paginate = false)
    {
        $list= $this->modelInfoType->getList($where, $field, $order, $paginate);
        foreach ($list as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['url']=$this->getInfoTypeUrl($row);
        }
        return $list;

    }

    /**
     * 分类信息添加
     * @param array $data
     * @return array
     */
    public function infoTypeAdd($data = [])
    {

        $result = $this->modelInfoType->setInfo($data);
        return  $result;
    }

    /**查询单条
     * @param array $where
     * @param bool $field
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getInfoTypeInfo($where = [], $field = true)
    {
        $info = $this->modelInfoType->getInfo($where, $field);
        if($info){
            $info['url']=$this->getInfoTypeUrl($info);
        }
        return $info;
    }


    /**
     * 地区列表管理=>id=key name=value
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return array
     */
    public function getInfoTypeColumn($where = [],$field='shortname',$key='')
    {
        $cache_key = 'cache_info_type_' . md5(serialize($where)). md5(serialize($field));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $list = $this->modelInfoType->getColumn($where,$field,$key);
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }


    /**获得所有指定id=>所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeAllPid($typeid=0, $data=[])
    {
        $where['id']=['=',$typeid];
        $parent_id = $this->modelInfoType->getValue($where,'parent_id');
        if(!empty($parent_id)){
            $data[]=$parent_id;
            return $this->getInfoTypeAllPid($parent_id,$data);
        }
        return $data;
    }

    /**获得所有指定id=>所有子级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeAllSon($typeid=0, $data=[])
    {
        $where['parent_id']=['=',$typeid];
        $sons = $this->getInfoTypeColumn($where,'id');
        if (count($sons) > 0) {
            foreach ($sons as $vid) {
                $data[] = $vid;
                $data = $this->getInfoTypeAllSon($vid, $data); //注意写$data 返回给上级
            }
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return false;
        }
        return $data;
    }

    /**获得所有指定id=>下级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeSon($typeid=0, $data=[])
    {
        $where['parent_id']=['=',$typeid];
        $sons = $this->getInfoTypeColumn($where,'id');
        return $sons;
    }

    /**
     * 获得所有指定id   所有同级
     *
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeAllSelf($typeid=0, $data=[])
    {
        $pid = $this->modelInfoType->getValue(['id'=>$typeid],'parent_id');
        $where['parent_id']=['=',$typeid];
        $data = $this->getInfoTypeColumn(['parent_id'=>$pid],'id');
        return $data;
    }


    /**
     * 得到本身路径=》上级=》本身=》下级
     *
     * @param int $typeid
     * @param array $data
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/14 0014
     */
    public function getInfoTypePidSelfSon($typeid=0, $data=[]){

        $cache_key = 'cache_info_type_pid_self_son_' . md5(serialize($typeid));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $pid=$this->getInfoTypeAllPid($typeid);
            $pid[]=$typeid;
            $son=$this->getInfoTypeSon($typeid);
            if(empty($son)){
                $son=$this->getInfoTypeAllSelf($typeid);
            }
            $ids=array_merge($pid,$son);
            $map['id']=['in',$ids];
            $list = $this->getInfoTypeColumn($map,'typename,typedir,parent_id','id');
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

    /**
     * 获得所有分类信息=》网站左侧显示 一级和二级
     *
     * @param int $typeid
     * @param array $data
     * @return array  树形数组输出
     */
    public function getInfoTypeChannel($data=[])
    {
        $cache_key = 'cache_info_type_channel_' . md5(serialize($data));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $data['tid']=0;
            $son_ids=$this->getInfoTypeAllSon($data['tid']);
            $map['id']=['in',$son_ids];
            $list = $this->getInfoTypeColumn($map,'typename,typedir,parent_id','id');
            foreach ($list as $key=>&$row){
                $data['tid']=$row['id'];
                $row['typeurl']=$this->getInfoTypeUrl($data);//整理地址输出
            }
            $list= list2tree($list,0,0,'id','parent_id','typename');

            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

    /**
     * 网站右侧分类栏目
     *
     * 显示下级栏目，下级不存时显示当前一级所有栏目
     *
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeSelfSonChannel($data=[])
    {
        $cache_key = 'cache_getInfoTypeSelfSonChannel_' . md5(serialize($data));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            empty($data['tid']) && $data['tid']=0;
            $typeid=$data['tid'];
            $son=$this->getInfoTypeSon($typeid);
            if(empty($son)){
                $son=$this->getInfoTypeAllSelf($typeid);
            }
            $map['id']=['in',$son];
            $list = $this->getInfoTypeColumn($map,'typename,typedir,parent_id','id');
            foreach ($list as $key=>&$row){
                $data['tid']=$row['id'];
                $row['typeurl']=$this->getInfoTypeUrl($data);
            }
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }


    /**
     * 网站右侧分类栏目
     *
     * 显示下级栏目，下级不存时显示当前一级所有栏目
     *
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeSitemapChannel($data=[])
    {
        $cache_key = 'cache_getInfoTypeSitemapChannel_' . md5(serialize($data));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $data['tid']=0;
            $typeid=$data['tid'];
            $son=$this->getInfoTypeAllSon($typeid);
            $map['id']=['in',$son];
            $list = $this->getInfoTypeColumn($map,'typename,typedir,parent_id','id');
            $citylist=$hotlist=$this->logicRegion->getRegionHotList();
            foreach ($list as $key=>&$row){
                $row['typeurl']=$this->getInfoTypeUrl(['tid'=>$row['id']]);
                $citydata=[];
                foreach ($citylist as $city){
                    $tmp=$row;
                    $tmp['typename']=$city['shortname'].$row['typename'];
                    $urldata=['province'=>$city['province_pinyin'],'city'=>$city['citycode'],'tid'=>$row['id']];
                    $tmp['typeurl']=$this->getInfoTypeUrl($urldata);
                    $citydata[]=$tmp;
                }
                $row['citylist']=$citydata;
            }
           // !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }


    /**
     * 获得分类栏目整理过后的链接地址
     *
     * 如：province/city/county/typeid/
     *
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getInfoTypeUrl($data=[])
    {
        $param=array();
        if(!empty($data['province'])){
            $param= array('province'=>$data['province']);
        }
        if(!empty($data['city'])){
            $param= array_merge($param,array('city'=>$data['city']));
        }
        if(!empty($data['county'])){
            $param= array_merge($param,array('county'=>$data['county']));
        }
        if(!empty($data['tid'])){
            $param= array_merge($param,array('tid'=>$data['tid']));
        }

        /*列表分页URL问号的查询部分*/
//        $get_arr = input('get.');
//        foreach ($get_arr as $key => $val) {
//            if (empty($val) || stristr($key, '/')) {
//                unset($get_arr[$key]);
//            }
//        }
//        $param= array_merge($param,$get_arr);

        $typeurl=url('index/Info/lists',$param);
        return $typeurl;
    }

}
