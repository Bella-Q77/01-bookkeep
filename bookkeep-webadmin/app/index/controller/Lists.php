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

class Lists extends IndexBase
{

    public $tid = '';
    public $type = '';

    /**
     * 列表主页
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index($data = [])
    {

        $tid = input("param.tid/s", '');

        /*获取当前栏目ID以及模型ID*/
        $page_tmp = input('param.page/s', 0);

        if (empty($tid) || !is_numeric($page_tmp)) {
            abort(404, '页面不存在');
        }

        if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
            $map = array('typedir' => $tid);
        } else {
            $map = array('id' => $tid);
        }

        //栏目详细
        $type = $this->logicArctype->getArctypeInfo($map);
		$type['title']=$type['typename'];
        if (empty($type)) {
            echo "tid错误~";
            //$this->redirect('/404.html');
            abort(404, '页面不存在');
            exit;
        }

        $rtnArray = array(
            'field' => $type,
        );
        $this->nid = $this->logicChannel->getChannelValue(['id' => $type['channel_id']], 'nid');

        /*模板文件*/
        $tpfile = 'lists_' . $this->nid;
        //判断栏目类型0=列表，1=封面
        if ($type['ispart'] == 0) {
            $tpfile = $type['temp_list'];
        } else if ($type['ispart'] == 1) {
            $tpfile = $type['temp_index'];
        }

        //start  动态添加搜索关键字变量 start *******************************************************

//        if(!empty($this->param['filterform'])){
//            $filterform=$this->param['filterform'];
//            $filterField=str2arr($filterform);
//            foreach ($filterField as $ffd){
//                if(!empty($this->param[$ffd])){//面积
//                    $this->assign($ffd, $this->param[$ffd]);
//                }else{
//                    $this->assign($ffd, '');
//                }
//            }
//        }


        if (!empty($this->param['orderway'])) {//升降
//            if($this->param['orderway']=='desc'){
//                $this->assign('orderway', 'asc');
//            }else{
//                $this->assign('orderway', 'desc');
//            }
            $this->assign('orderway', 'desc');
        } else {
            $this->assign('orderway', 'desc');
        }

        //end 动态添加搜索关键字变量 end *******************************************************

        $viewfile = !empty($tpfile) ? strtolower($tpfile) : $tpfile;
        $this->typeinfo = $rtnArray;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($viewfile);
    }

    /**
     * 后台调用方法，可以配合路由配置
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/2 0002
     */
    public function adminindex()
    {
        return $this->index($this->param);
    }

}
