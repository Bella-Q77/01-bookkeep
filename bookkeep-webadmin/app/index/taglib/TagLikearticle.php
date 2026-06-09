<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Author: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\taglib;

use think\Db;
use think\Request;


/**
 * 相关文档列表
 */
class TagLikearticle extends Base
{
    public $tid = '';
    public $aid = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();

        // 应用于栏目列表
        $this->tid = input("param.tid/s", '');

        /*应用于文档列表*/
        $this->aid = input('param.aid/d', 0);

    }

    /**
     *  arclist解析函数
     *
     * @param array $param 查询数据条件集合
     * @param int $row 调用行数
     * @param string $orderby 排列顺序
     * @param string $addfields 附加表字段，以逗号隔开
     * @param string $orderway 排序方式
     * @param string $tagid 标签id
     * @param string $tag 标签属性集合
     * @param string $pagesize 分页显示条数
     * @param string $thumb 是否开启缩略图
     * @return    array
     * @author wengxianhu by 2018-4-20
     * @access    public
     */
    public function getLikearticle($channelid = '', $typeid = '', $orderby = '', $orderway = '', $limit = 12)
    {
        $result = false;
        $where = [];
        if ($channelid) {
            $where['a.channel_id'] = ['in', $channelid];
        }

        if (!empty($typeid)) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $typeid)) {
                echo '标签arclist报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            // 过滤typeid中含有空值的栏目ID
            $logicArctype = new \app\index\logic\Arctype();
            $typeidArr_tmp = explode(',', $typeid);
            $typeidArr_tmp = array_unique($typeidArr_tmp);//过滤重复的
            $typeidArr_son = [];//得到子级栏目
            foreach ($typeidArr_tmp as $k => $v) {
                if (empty($v)) {
                    unset($typeidArr_tmp[$k]);
                } else {
                    $typeid_son = $logicArctype->getArctypeAllSon($v);
                    $typeid_son && $typeidArr_son = array_merge($typeidArr_son, $typeid_son);
                }
            }
            $typeidArr_tmp = array_merge($typeidArr_tmp, $typeidArr_son);
            $typeid = implode(',', $typeidArr_tmp);
            // end
        }

        if ($typeid) {
            $where['a.type_id'] = ['in', $typeid];
            $randMap['type_id'] = ['in', $typeid];
        }

        if ($this->aid) {
            $where['a.id'] = ['notin', $this->aid];
            $randMap['id'] = ['notin', $this->aid];
        }

        /*获取相关标签编号,获取 相同的 tid 文档  */
        $tagList = new \app\index\logic\Taglist();
        $tids = $tagList->getTaglistColumn(['aid' => $this->aid], 'tid');
        $aids = $tagList->getTaglistColumn(['tid' => ['in', $tids]], 'aid');
        $aids = array_merge(array_diff($aids, array($this->aid)));//排除自身
        $where['a.id'] = ['in', $aids];

        /*获取文档列表*/
        $logicArchives = new \app\index\logic\Archives();
        //排序
        switch ($orderby) {
            case 'rand':
                $rand_ids = $logicArchives->getArchivesColumn($randMap, 'id');
                $rand_cnt = count($rand_ids);
                $number = (count($rand_ids) > 15) ? '15' : $rand_cnt;
                $rand_id = array_rand_value($rand_ids, $number);
                $where['a.id'] = array('in', $rand_id);
                $orderby = 'create_time DESC';
                break;
            case 'click':
                $orderby = "a.click {$orderway}";
                break;
            case 'sort':
                $orderby = "a.sort {$orderway}";
                break;
            case 'pubdate':
                $orderby = "a.pubdate {$orderway}";
                break;
            default:
                $orderby = 'create_time DESC ';
                break;
        }
        //根据条件查出结果集
        $result = $logicArchives->getArchiveslikeList($where, '', $orderby, $limit);

        //获取文档栏目信息
        $logicArctype = new \app\index\logic\Arctype();
        foreach ($result as &$row) {
            $typeinfo = $logicArctype->getArctypeInfo(['id' => $row['type_id']],'id,typename,typedir,ispart');
            if ($typeinfo) {
                $row['typename'] = $typeinfo['typename'];
                $row['typeurl'] = $typeinfo['typeurl'];
            }
        }
        return $result;
    }
}