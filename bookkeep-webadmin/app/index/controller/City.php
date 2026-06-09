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

class City extends IndexBase
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
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function index($data = [])
    {
        $province = input("param.province", '');
        $city = input("param.city", '');
        $county = input("param.county", '');

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
        $list_info=$this->logicInfo->getInfoList($where,'a.id,a.title,a.description,a.content,a.pubdate_time,a.litpic,a.city_id','a.update_time desc',false,100);//分类信息
        $list_company=$this->logicMemberCompany->getMemberCompanyList($where,false,'',false,12);//公司信息
        $type_list_right=$this->logicInfoType->getInfoTypeSelfSonChannel($this->param);//当前分类下一分类

        $rtnArray = array(
            'list_info' => $list_info,
            'list_company' => $list_company,
            'type_list_right' => $type_list_right,
        );

        /*模板文件*/
        if(empty($tpfile)){
            $tpfile = 'city_index.html';
        }
        $viewfile = !empty($tpfile) ? strtolower($tpfile) : $tpfile;
        $this->typeinfo = $rtnArray;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($viewfile);
    }

    /**
     * 热门城市函数
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function citys($data = [])
    {
        $list=$this->logicRegion->getInfoProvinceCityChannel(['id'=>'100000']);
        $rtnArray = array(
            'list' => $list,
        );
        /*模板文件*/
        if(empty($tpfile)){
            $tpfile = 'citys.html';
        }
        $viewfile = !empty($tpfile) ? strtolower($tpfile) : $tpfile;
        $this->typeinfo = $rtnArray;
        $this->assign('fly', $this->typeinfo);
        return $this->fetch($viewfile);
    }

}
