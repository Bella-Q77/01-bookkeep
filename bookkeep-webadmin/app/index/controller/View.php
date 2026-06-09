<?php
/**
 * 零起飞07FLY-CMS
 * ============================================================================
 * 版权所有 2018-2028 成都零起飞科技有限公司，并保留所有权利。
 * 网站地址: http://www.07fly.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 开发人生 <goodkfrs@qq.com>
 * Date: 2021-01-01-3
 */

namespace app\index\controller;

use think\Controller;

class View extends IndexBase{

    public $aid = '';
    public $type = '';

    /**
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index($aid = ''){
        $this->aid = input("param.aid", '0');
        if (!is_numeric($this->aid) || strval(intval($this->aid)) !== strval($this->aid)) {
            abort(404,'页面不存在');
        }
        $this->aid = intval($this->aid);
        if(empty($this->aid)){
            echo "aid不能为空~";
            exit;
        }else{
            /**文档处理**/
            $archives=$this->logicArchives->getArchivesInfo(['id'=>$this->aid]);
            if(empty($archives)){
                echo "aid错误~";
                exit;
            }
            //栏目处理
            $type=$this->logicArctype->getArctypeInfo(['id'=>$archives['type_id']]);
            //模型处理
            $this->nid=$this->logicChannel->getChannelValue(['id'=>$type['channel_id']],'nid');
            //字段封装

			$archives['typename']=$type['typename'];
			$archives['typeurl']=$type['typeurl'];
            $rtnArray  = array(
                'type' => $type,
                'field' => $archives,
            );
            //更新点击
            $this->logicArchives->setArchivesClick(['id'=>$this->aid],$archives['click']);
        }

        /*模板文件*/
        $tpfile= !empty($type['temp_article'])?$type['temp_article']:'view_' . $this->nid.'.html';
        $this->typeinfo =$rtnArray ;

        $this->assign('fly', $this->typeinfo);


        if($this->aid=='17961') $tpfile='mb_show_test.html';



        return $this->fetch($tpfile);
    }

    /**
     * 后台调用方法，可以配合路由配置
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/2 0002
     */
    public function adminindex(){
       return  $this->index($this->param);
    }
}
