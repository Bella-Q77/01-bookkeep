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
 * 栏目管理=》逻辑层
 */
class Arctype extends IndexBase
{
    /**
     * 栏目列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getArctypeList($where = [], $field = true, $order = '', $paginate = false)
    {
        $list= $this->modelArctype->getList($where, $field, $order, $paginate)->toArray();

        if($paginate===false) $list['data']=$list;

        foreach ($list['data'] as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
            $row['typeurl']=$this->getArctypeUrl($row);
        }

        return $list;

    }

    /**查询单条
     * @param array $where
     * @param bool $field
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getArctypeInfo($where = [], $field = true)
    {
        $info = $this->modelArctype->getInfo($where, $field);
        if($info){
            is_object($info)&& $info=$info->ToArray();
            $info['typeurl']=$this->getArctypeUrl($info);
        }
        return $info;
    }

    //得到数形参数
    public function getArctypeListTree($where)
    {
        $list = $this->getArctypeList($where,'','',false);
        $tree= list2tree($list['data'],0,0,'id','parent_id','typename');
        return $tree;
    }

    /**获得所有指定id所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getArctypeAllPid($typeid=0, $data=[])
    {
        $where['id']=['=',$typeid];
        $info = $this->modelArctype->getInfo($where,'parent_id');
        if(!empty($info) && $info['parent_id']){
            $data[]=$info['parent_id'];
            return $this->getArctypeAllPid($info['parent_id'],$data);
        }
        return $data;
    }

    /**获得所有指定id所有子级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getArctypeAllSon($typeid=0, $data=[])
    {
        $where['parent_id']=['=',$typeid];
        $sons = $this->modelArctype->getList($where,'id','sort asc',false);
        if (count($sons) > 0) {
            foreach ($sons as $v) {
                $data[] = $v['id'];
                $data = $this->getArctypeAllSon($v['id'], $data); //注意写$data 返回给上级
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
    public function getArctypeAllSelf($typeid=0, $data=[])
    {

        $pid = $this->modelArctype->getValue(['id'=>$typeid],'parent_id');
        $where['parent_id']=['=',$typeid];
        $data = $this->modelArctype->getColumn(['parent_id'=>$pid],'id');
        return $data;
    }

    /**获得所有指定id 所有顶级
     * @return array
     */
    public function getArctypeAllTop()
    {
        $data = $this->modelArctype->getColumn(['parent_id'=>'0'],'id');
        return $data;
    }


    /**获得所有指定id ,父级ID
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getArctypeSelf($typeid=0)
    {
        $pid=0;
        $where['id']=['=',$typeid];
        $typepid = $this->modelArctype->getValue($where,'parent_id');
        if($typepid>0){
            $pid=$typepid;
        }
        return $pid;
    }


    /**
     * 获得所有指定id所有父级
     * @param int $typeid
     * @param array $data
     * @return array
     */
    public function getArctypeUrl($data=[])
    {
        if ($data['ispart'] == 2) {
            $data['typeurl'] = $data['typedir'];
            if (!is_http_url($data['typeurl'])) {
                $typeurl = '//'.request()->host();
                $typeurl .= '/'.trim($data['jump_url'], '/');
            }
        } else {
        	if($data['typedir']){
				$typeurl = url('index/lists/index', array('tid'=>$data['typedir']));
			}else{
				$typeurl = url('index/lists/index', array('tid'=>$data['id']));
			}

        }
        return $typeurl;
    }


	/**
	 * 在typeid传值为目录名称的情况下，获取栏目ID
	 */
	public function getTrueTypedir($typeid = '')
	{
		$typedir = $this->modelArctype->getValue(['id'=>$typeid], 'typedir');
		if(empty($typedir))  return $typeid;
		return $typedir;
	}


}
