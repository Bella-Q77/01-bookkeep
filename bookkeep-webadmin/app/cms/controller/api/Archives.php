<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\cms\controller\api;

use app\common\controller\ControllerBase;
use think\Db;


/**
 * 信息管理-控制器
 */
class Archives extends ControllerBase
{

    /**
     * 构造方法
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();

    }

    /**验证登录
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/3/18 0018 8:55
     */
    public function chklogin()
    {
        if (empty($this->param['userid']) || empty($this->param['pwd'])) {
            echo "user or password not empty;";
            exit;
        }
        $map['username'] = $this->param['userid'];
        $map['password'] = data_md5_key($this->param['pwd']);
        $one = Db::name('sys_user')->where($map)->value('id');
        if (empty($one)) {
            echo "user or password fail;";
            exit;
        }
    }

    /**
     * 信息添加
     * @return mixed|string
     */
    public function addchk()
    {
        echo '[no]';
    }

    /**
     * 信息添加
     * @return mixed|string
     */
    public function add()
    {
        $this->chklogin();
        //d($this->param);
        //dlog($this->param);
        $data = $this->param;
        $data['title'] = $this->param['title'];
        $data['shorttitle'] = empty($this->param['shorttitle'])?'' : $this->param['shorttitle'];
        $data['keywords'] = $this->param['keywords'];
        $data['description'] = $this->param['description'];
        $data['body'] = $this->param['body'];
        $data['type_id'] = $this->param['typeid'];
        $data['type_id2'] = 0;
        $data['litpic'] = empty($this->param['litpic']) ? '' : $this->param['litpic'];
        $data['click'] = empty($this->param['click'])?1 : $this->param['click'];
        $data['writer'] = empty($this->param['writer']) ? '管理' : $this->param['writer'];
        $data['source'] = empty($this->param['source']) ? '未知' : $this->param['source'];
        $data['pubdate'] = format_time();
        $res = $this->logicArchives->archivesAdd($data);
        if ($res[0] == RESULT_SUCCESS) {
            echo "[ok]";
        } else {
            d($res);
        }
    }
    //在线投稿添加
    public function addTouGao()
    {
        //d($this->param);
        //dlog($this->param);
        $data = $this->param;
        $data['title'] = $this->param['title'];
        $data['shorttitle'] = empty($this->param['shorttitle']) ? '' : $this->param['shorttitle'];
        $data['keywords'] = empty($this->param['keywords']) ? '' : $this->param['keywords'];
        $data['description'] =empty($this->param['description']) ? '' : $this->param['description'];
        $data['source'] =empty($this->param['source']) ? '' : $this->param['source'];
        $data['body'] = $this->param['body'];
        $data['type_id'] = $this->param['typeid'];
        $data['type_id2'] = 0;
        $data['litpic'] = empty($this->param['litpic']) ? '' : $this->param['litpic'];
        $data['click'] = 1;
        $data['writer'] = empty($this->param['writer']) ? 'admin' : $this->param['writer'];
        $data['pubdate'] = format_time();
        $result = $this->logicArchives->archivesAdd($data);
        return $result;
    }
    //获取栏目
    public function getArctype(){
        $where['issend']=['=','1'];//是否支持投稿
        $listtree= $this->logicArctype->getArctypeListTree($where);
        $arctypelist= $this->logicArctype->getArctypeListSelect($listtree);
        return $arctypelist;
    }
}
