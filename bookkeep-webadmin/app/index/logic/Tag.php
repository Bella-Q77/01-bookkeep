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
 * 文章标签列表=》逻辑层
 */
class Tag extends IndexBase
{

    /**列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getTagList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $this->modelTags->alias('a');
        $list= $this->modelTags->getList($where, $field, $order, $paginate)->toArray();

        $paginate===false && $list['data']=$list;

        foreach ($list['data'] as &$row){
           // $row['litpic'] =get_picture_url($row['litpic']);
           // $row['target'] = ($row['target'] == 1) ? 'target="_blank"' : 'target="_self"';
        }
        return $list;
    }


    /**
     * 获取标签的信息
     * @param array $where
     * @param bool $field
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/3 0003
     */
    public function getTagindexInfo($where = [], $field = true)
    {
        return $this->modelTagindex->getInfo($where, $field);
    }


    /**
     * 传入标签信息,更新标签索引的点击，浏览，统计数据
     * @param array $data
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/3 0003
     */
    public function getTagindexUpdate($tagindexInfo=[])
    {
        $map['tid']=['=',$tagindexInfo['id']];
        $total=$this->modelTaglist->stat($map,'count','tid');

        $updata=[
            'total'=>$total,
            'count'=>$tagindexInfo['count']+1,
            'weekcc'=>$tagindexInfo['weekcc']+1,
            'monthcc'=>$tagindexInfo['monthcc']+1,
        ];

        $ntime=time();
        $oneday = 24 * 3600;
        //周统计
        if(ceil( ($ntime - $tagindexInfo['weekup'])/$oneday ) > 7)
        {
            $updata['weekcc']=0;
            $updata['weekup']=$ntime;
        }
        //月统计
        if(ceil( ($ntime - $tagindexInfo['monthup'])/$oneday ) > 30)
        {
            $updata['monthcc']=0;
            $updata['monthcc']=$ntime;
        }
        $this->modelTagindex->updateInfo(['id'=>$tagindexInfo['id']],$updata);
    }


    /**
     * 获取调用标签相关文档aid
     * @param null $tag
     * @param null $tagid
     * @return array;
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/3 0003
     */
    public  function  getTaglistAid($tag=null, $tagid=null){
        $aidArr='';
        if (!empty($tag)) {
            $map['tag']=['like', "%{$tag}%"];
            $tagidArr = $this->modelTagindex->getColumn($map, "id");
            $aidArr = $this->modelTaglist->getColumn(array('tid'=>array('in', $tagidArr)), "aid");
        } elseif ($tagid > 0) {
            $aidArr = $this->modelTaglist->getColumn(['tid'=>$tagid], "aid");
        }
        return $aidArr;
    }


}
