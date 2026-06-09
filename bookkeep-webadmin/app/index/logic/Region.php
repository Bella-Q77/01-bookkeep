<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Memberor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\logic;

use think\Db;

/**
 * 地区列表管理=》逻辑层
 */
class Region extends IndexBase
{

    /**
     * 地区列表管理
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getRegionList($where = [])
    {
        $cache_key = 'cache_region_' . md5(serialize($where));
        $cache_list = cache($cache_key);
        if (!empty($cache_list)) : return $cache_list; endif;
        $list = Db::name('region')->where($where)->field(true)->select();
        !empty($list) && cache($cache_key, $list);
        return $list;
    }


    /**
     * 地区列表管理=>热门城市
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getRegionHotList($where = [], $field = 'a.citycode,a.name,a.shortname,p.name AS province_name,p.pinyin AS province_pinyin', $order = 'a.id desc', $paginate = 30)
    {
        $where['a.level']=['=','2'];
        $cache_key = 'cache_getRegionHotList_' . md5(serialize($where)). md5(serialize($field)). md5(serialize($order)). md5(serialize($paginate));
        $cache_list = cache($cache_key);
        if (!empty($cache_list)){
            $list= $cache_list;
        } else{
            $this->modelRegion->alias('a');
            $join = [
                [SYS_DB_PREFIX . 'region p', 'p.id = a.upid','LEFT'],
            ];
            $this->modelRegion->join = $join;
            $list= $this->modelRegion->getList($where, $field, $order, $paginate);
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

    /**
     * 地区列表管理
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getRegionInfo($where = [],$field=true)
    {
        $cache_key = 'cache_region_' . md5(serialize($where)). md5(serialize($field));
        $cache_list = cache($cache_key);
        if (!empty($cache_list)) : return $cache_list; endif;
        $list=$this->modelRegion->getInfo($where,$field);
        !empty($list) && cache($cache_key, $list);
        return $list;
    }

    /**获取详细
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getRegionFieldValue($where = [], $field = true)
    {
        $info = $this->modelRegion->getValue($where, $field);
        return $info;
    }


    /**获取详细
     * @param array $data
     * @return mixed|string
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getRegionCityName($where = [], $field = true)
    {

        $info = $this->getRegionInfo($where, $field);
        if($info){
            $province=$this->getRegionInfo(['id'=>$info['upid']]);
        }

        $data=[
            'city_name'=>$info['shortname'],
            'city_code'=>$info['citycode'],
            'province_code'=>$province['shortname'],
            'province_code'=>$province['pinyin'],
        ];
        return $data;
    }


    /**
     * 地区列表管理=>所有省=》市=》区
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getRegionListName($where=[])
    {
        return $this->getRegionColumn($where,'shortname','id');
    }

    /**
     * 地区列表管理=>id=key name=value
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return array
     */
    public function getRegionColumn($where = [],$field='shortname',$key='')
    {
        $cache_key = 'cache_getRegionColumn_' . md5(serialize($where));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $list = $this->modelRegion->getColumn($where,$field,$key);
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }


    /**获得所有指定id所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionAllPid($typeid=0, $data=[])
    {
        $where['id']=['=',$typeid];
        $where['visible']='1';
        $upid= $this->modelRegion->getValue($where,'upid');
        if(!empty($upid)){
            $data[]=$upid;
            return $this->getRegionAllPid($upid,$data);
        }
        return $data;
    }
    /**获得所有指定id所有子级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionAllSon($typeid=0, $data=[])
    {
        $where['upid']=['=',$typeid];
        $where['visible']='1';
        $sons = $this->modelRegion->getColumn($where,'id');
        foreach ($sons as $v) {
            $data[] = $v;
            $data = $this->getRegionAllSon($v, $data); //注意写$data 返回给上级
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return false;
        }
        return $data;
    }

    /**获得所有指定id=>下级子级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionSon($typeid=0, $data=[])
    {
        $where['upid']=['=',$typeid];
        $where['visible']='1';
        $sons = $this->modelRegion->getColumn($where,'id');
        return $sons;
    }


    /**获得所有指定id =》所有同级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionAllSelf($typeid=0, $data=[])
    {
        $pid = $this->modelRegion->getValue(['id'=>$typeid],'upid');
        $data = $this->modelRegion->getColumn(['upid'=>$pid],'id');
        return $data;
    }

    /**获得所有指定id 所有顶级
     * @return array
     */
    public function getRegionAllTop()
    {
        $data = $this->modelRegion->getColumn(['upid'=>'0'],'id');
        return $data;
    }


    /**获得所有指定id ,父级ID
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionSelfPid($typeid=0)
    {
        $pid=0;
        $where['id']=['=',$typeid];
        $where['visible']='1';
        $typepid = $this->modelRegion->getValue($where,'upid');
        if($typepid>0){
            $pid=$typepid;
        }
        return $pid;
    }


    /**
     * 得到本身路径=》上级=》本身=》下级
     *
     * @param int $typeid
     * @param array $data
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/14 0014
     */
    public function getRegionPidSelfSon($typeid=0, $data=[]){

        $cache_key = 'cache_region_pid_self_son_' . md5(serialize($typeid));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $pid=$this->getRegionAllPid($typeid);
            $pid[]=$typeid;
            $son=$this->getRegionSon($typeid);
            if(empty($son)){
                $son=$this->getRegionAllSelf($typeid);
            }
            $ids=array_merge($pid,$son);
            $map['id']=['in',$ids];
            $list = $this->modelRegion->getColumn($map,'shortname,pinyin,citycode,level','id');

            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

    /**
     * 所有=》省级=》热门城市调用
     *
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionProvinceChannel($data=[])
    {
        $where['upid']='100000';
        $where['visible']='1';
        $cache_key = 'cache_getRegionProvinceChannel_' . md5(serialize($where));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else {
            $list = $this->getRegionColumn($where, 'shortname,citycode,pinyin', 'id');
            foreach ($list as $key => &$row) {
                $row['url'] = url('index/City/index', array('province' => $row['pinyin']));
            }
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }


    /**
     * 分类栏目页=》省=》市=》区
     *
     * 获得所有指定id所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getRegionCityTypeChannel($data=[])
    {
        if(empty($data['province'])){
            $region_id='100000';
        }else{
            $region_id=$this->getRegionCityId($data['province'],'1');
        }

        if(!empty($data['city'])){
            $region_id=$this->getRegionCityId($data['city'],'2');
        }

        if(!empty($data['county'])){
            $region_id=$this->getRegionCityId($data['county'],'3',$data['city']);
        }

        $cache_key = 'cache_getRegionCityTypeChannel_' . md5(serialize($data));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else {
            $list = $this->getRegionPidSelfSon($region_id);
            $pids = $this->getRegionAllPid($region_id);//所有上级id
            //栏目
            if (empty($data['tid'])) {
                $data['tid'] = 0;
            }
            foreach ($list as $key => &$row) {
                $param = array();
                switch ($row['level']) {
                    case '1':
                        $param = array('province' => $row['pinyin']);
                        break;
                    case '2':
                        $param = array('province' => $data['province'], 'city' => $row['citycode']);
                        break;
                    case '3':
                        $param = array('province' => $data['province'], 'city' => $data['city'], 'county' => $row['pinyin']);
                        break;
                    default:
                        $rid = '0';
                        $param = array();
                        break;
                }
                if (in_array($row['id'], (array)$pids) || $region_id == $row['id']) {
                    $style = 'on';
                } else {
                    $style = '';
                }
                $row['style'] = $style;

                if(empty($data['tid'])){
                    $row['url'] = url('index/City/index', $param);
                }else{
                    $param = array_merge($param, array('tid' => $data['tid']));
                    $row['url'] = url('index/Info/lists', $param);
                }

            }
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

    /**
     * 头部=》热门城市调用=》支持省=》市  树形输出
     *
     * @param int $typeid
     * @param array $data
     * @return array
     *  如：array('
     * name=>'四川'
     * city=>array('成都'，南充)
     * ')
     */
    public function getInfoProvinceCityChannel($data=[])
    {
        $cache_key = 'cache_region_province_city_channel_' . md5(serialize($data));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $field='shortname,citycode,pinyin';
            $where['upid']='100000';
            $list=$this->getRegionColumn($where,$field,'id');
            //整理地址输出
            foreach ($list as $key=>&$row){
                $row['url']=url('index/City/index',array('province'=>$row['pinyin']));
            }
            foreach ($list as $key=>&$row1){
                $map['upid']=$row1['id'];
                $citylist=$this->getRegionColumn($map,$field,'id');
                foreach ($citylist as $key=>&$row2){
                    $row2['url']=url('index/City/index',array('province'=>$row['pinyin'],'city'=>$row2['citycode']));
                }
                $row1['city']=$citylist;
            }
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }


    /**
     * 获得地区的编号
     *
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/15 0015
     */
    public function getRegionCityId($rid,$level=1,$cityid=''){
        switch ($level){
            case 1:
                $map2 = array('pinyin' => $rid,'level'=>$level);
                break;
            case 2:
                $map2 = array('citycode' => $rid,'level'=>$level);
                break;
            case 3:
                $map2 = array('pinyin' => $rid,'level'=>$level,'citycode'=>$cityid);
                break;
        }
        $cache_key = 'cache_getRegionCityId_' . md5(serialize($map2));
        $cache_list = cache($cache_key);
        $cache_list ='';
        if (!empty($cache_list)){
            $list=$cache_list;
        }else{
            $list = $this->logicRegion->getValue($map2,'id');
            if (empty($list)) {
                echo "获得 region-id错误~";
                abort(404, '页面不存在');
                exit;
            }
            !empty($list) && cache($cache_key, $list);
        }
        return $list;
    }

}
