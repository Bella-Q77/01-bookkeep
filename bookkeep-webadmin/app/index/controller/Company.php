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

class Company extends IndexBase
{

    public $tid = '';
    public $type = '';


    /**
     * 构造方法
     */
    public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();

    }


    /**
     * 详细展页面
     *
     * @param string $cid
     * @return mixed
     * Author: kfrs <goodkfrs@QQ.com> created by at 2021/1/22 0022
     */
    public function view($cid = ''){

        $this->cid = input("param.cid", '0');
        if (!is_numeric($this->cid) || strval(intval($this->cid)) !== strval($this->cid)) {
            abort(404,'cid页面不存在');
        }
        $this->cid = intval($this->cid);
        if(empty($this->cid)){
            abort(404, '页面不存在');
            exit;
        }else{
            /**文档处理**/
            $info=$this->logicMemberCompany->getMemberCompanyInfo(['id'=>$this->cid]);
            if(empty($info)){
                abort(404, '公司页面不存在');
                exit;
            }
            //替换当前地区标签
            $city=$this->logicRegion->getRegionCityName(['id'=>$info['city_id']]);
            if(!empty($city['city_name'])){
                $paramUrl=array('province'=>$city['province_code'],'city'=>$city['city_code'],);
                $url=url("index/City/index",$paramUrl);
                $this->assign('sys_city_web_url', $url);
                $this->assign('sys_city_web_title', $city['city_name'].'培训');
                $this->sys_city_name=$city['city_name'];
            }
            //右侧分类
            $type_list_right=$this->logicInfoType->getInfoTypeSelfSonChannel(['tid'=>$info['category_id']]);
            //公司发布课程
            $company_info_list=$this->logicInfo->getInfoList(['company_id'=>$info['id']],"",'',false,1);

            //字段封装
            $rtnArray  = array(
                'type_list_right' => $type_list_right,
                'company_info_list' => $company_info_list,
                'field' => $info,
            );
            //更新点击
            $this->logicMemberCompany->setMemberCompanyClick(['id'=>$this->cid]);
        }

        /*模板文件*/
        $tpfile='company_show.html';
        $this->typeinfo =$rtnArray ;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($tpfile);
    }

    /**
     * 分类列表显示显示
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function lists($data = [])
    {
        $tid = input("param.tid/s", '');
        $province = input("param.province", '');
        $city = input("param.city", '');
        $county = input("param.county", '');
        $where=[];
        //城市筛选
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

        //右边显示
        $info_list_right=$this->logicInfo->getInfoList($where,"a.id,a.title,a.pubdate_time,a.litpic,a.city_id",'',false,10);
        $company_list_right=$this->logicMemberCompany->getMemberCompanyList($where,'a.name,a.litpic,a.intro,a.id,a.city_id','',false,10);

        $rtnArray=array();
        /*获取当前栏目ID以及模型ID*/
        if (!empty($tid)) {
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
            $tid=$type['id'];
            //栏目内容列表
            if(!empty($tid)){
                $pid=$this->logicInfoType->getInfoTypeAllPid($tid);
                $pid[]=$tid;
                $where['category_id']=['in',$pid];
            }
        }

        if(!empty($type)){
            $rtnArray=array('type'=>$type);
        }else{
            $rtnArray=[
                'type'=>[
                    'id'=>'0',
                    'typename'=>'',
                ]
            ];
        }

        $list=$this->logicMemberCompany->getMemberCompanyList($where);
        $pages=$list->render('pre,next,pageno',DB_LIST_ROWS);
        $listArr=array(
            'list' => $list,
            'info_list_right' => $info_list_right,
            'company_list_right' => $company_list_right,
            'pages' => $pages,
        );

        $rtnArray = array_merge($rtnArray,$listArr);
        /*模板文件*/
        if(empty($tpfile)){
            $tpfile = 'company_list.html';
        }
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
