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

class Info extends IndexBase{

    public $iid = '';
    public $type = '';


    /**
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index()
    {
        $where=[];
        //右边显示
        $list_info=$this->logicInfo->getInfoList($where,'a.id,a.title,a.pubdate_time,a.litpic,a.city_id','a.update_time desc',100);
        $list_company=$this->logicMemberCompany->getMemberCompanyListHot($where,'','',10);
        $type_list_right=$this->logicInfoType->getInfoTypeSelfSonChannel($this->param);
        $rtnArray = array(
            'list_info' => $list_info,
            'list_company' => $list_company,
            'type_list_right' => $type_list_right,
        );
        /*模板文件*/
        if(empty($tpfile)){
            $tpfile = 'index.html';
        }
        $viewfile = !empty($tpfile) ? strtolower($tpfile) : $tpfile;
        $this->typeinfo = $rtnArray;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($viewfile);
    }


    /**
     * 信息显示
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function view($iid = ''){
        $this->iid = input("param.iid", '0');

        if (!is_numeric($this->iid) || strval(intval($this->iid)) !== strval($this->iid)) {
            abort(404,'iid页面不存在');
        }
        $this->iid = intval($this->iid);
        if(empty($this->iid)){
            abort(404, '页面不存在');
            exit;
        }else{
            /**文档处理**/
            $info=$this->logicInfo->getInfoInfo(['id'=>$this->iid]);
            if(empty($info)){
                abort(404, '页面不存在');
                exit;
            }
            $city=$this->logicRegion->getRegionCityName(['id'=>$info['city_id']]);
            if(!empty($city['city_name'])){
                $paramUrl=array('province'=>$city['province_code'],'city'=>$city['city_code'],);
                $url=url("index/City/index",$paramUrl);
                $this->assign('sys_city_web_url', $url);
                $this->assign('sys_city_web_title', $city['city_name'].'培训');
                $this->sys_city_name=$city['city_name'];
            }

            $type_list_right=$this->logicInfoType->getInfoTypeSelfSonChannel(['tid'=>$info['type_id']]);
//            print_r($info['company_id']);exit;
            //栏目处理
            $type=$this->logicInfoType->getInfoTypeInfo(['id'=>$info['type_id']]);
            //字段封装
            $rtnArray  = array(
                'type' => $type,
                'type_list_right' => $type_list_right,
                'field' => $info,
            );
            //更新点击
            $this->logicInfo->setInfoClick(['id'=>$this->iid]);
        }

        /*模板文件*/
        $tpfile='info_show.html';
        $this->typeinfo =$rtnArray ;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($tpfile);
    }

    /**
     * 分类课时显示
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function lists($data = [])
    {
        $tid = input("param.tid/s", '');
        $province = input("param.province", '');
        $city = input("param.city", '');
        $county = input("param.county", '');
        /*获取当前栏目ID以及模型ID*/
        $page_tmp = input('param.page/s', 0);

//        print_r($this->param);

        if (empty($tid) || !is_numeric($page_tmp)) {
            abort(404, '页面不存在');
        }

        if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
            $map = array('typedir' => $tid);
        } else {
            $map = array('id' => $tid);
        }
        //栏目信息
        $type = $this->logicInfoType->getInfoTypeInfo($map);
        if (empty($type)) {
            echo "tid错误~";
            abort(404, '页面不存在');
            exit;
        }
        $where=[];
        if(!empty($province)){
            $province_id=$this->logicRegion->getRegionCityId($province);
            $where['province_id']=['in',$province_id];
        }

        if(!empty($city)){
            $city_id=$this->logicRegion->getRegionCityId($city,2);
            $where['city_id']=['in',$city_id];
        }

        if(!empty($county)){
            $county_id=$this->logicRegion->getRegionCityId($county,3,$city);
            $where['county_id']=['in',$county_id];
        }

        $info_list_right=$this->logicInfo->getInfoList($where,"a.id,a.title,a.pubdate_time,a.litpic,a.city_id",'',false,10);
        $company_list_right=$this->logicMemberCompany->getMemberCompanyList($where,'a.name,a.litpic,a.intro,a.id,a.city_id','',false,10);
        $type_list_right=$this->logicInfoType->getInfoTypeSelfSonChannel($this->param);

        $tid=$type['id'];
        //栏目内容列表
        if(!empty($tid)){
            $son=$this->logicInfoType->getInfoTypeAllSon($tid);
            $son[]=$tid;
            $where['type_id2']=['in',$son];
        }
        $list=$this->logicInfo->getInfoList($where);

        $pages=$list->render('pre,next,pageno',DB_LIST_ROWS);

        $rtnArray = array(
            'type' => $type,
            'list' => $list,
            'info_list_right' => $info_list_right,
            'company_list_right' => $company_list_right,
            'type_list_right' => $type_list_right,
            'pages' => $pages,
        );

        /*模板文件*/

        //判断栏目类型0=列表，1=封面
        if ($type['ispart'] == 0) {
            $tpfile = $type['temp_list'];
        } else if ($type['ispart'] == 1) {
            $tpfile = $type['temp_index'];
        }
        if(empty($tpfile)){
            $tpfile = 'info_list.html';
        }
        $viewfile = !empty($tpfile) ? strtolower($tpfile) : $tpfile;

        $this->typeinfo = $rtnArray;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($viewfile);
    }

    /**
     * 信息报名
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/2 0002
     */
    public function askfor(){

        $this->jump($this->logicInfoAskfor->infoAskforAdd($this->param));

    }
}
