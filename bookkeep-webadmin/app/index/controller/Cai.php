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

class Cai extends IndexBase
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

        $url = 'https://www.qeo.cn/peixun.htm';

        $html = GetFile($url,2);
        //$content = getTagData($html, '<a name="fl3"></a>', '<a name="fl473"></a>');
        //$content = getTagData($html, '<a name="fl473"></a>', '<a name="fl472"></a>');//电脑设计培训
       //$content = getTagData($html, '<a name="fl472"></a>', '</li><a name="fl9"></a>');//餐饮
        //$content = getTagData($html, '</li><a name="fl9"></a>', '</li><a name="fl8"></a>');//学历教育
       //$content = getTagData($html, '</li><a name="fl8"></a>', '</li><a name="fl6"></a>');//<i>职业资格培训</i>
        //$content = getTagData($html, '</li><a name="fl6"></a>', '</li><a name="fl474"></a>');//<i>技能培训</i>
      // $content = getTagData($html, '</li><a name="fl474"></a>', '</li><a name="fl7"></a>');//<i><i>美妆培训</i></i>
       //$content = getTagData($html, '</li><a name="fl7"></a>', '</li><a name="fl57"></a>');//<i>才艺培训</i>
        //$content = getTagData($html, '</li><a name="fl57"></a>', '</li><a name="fl4"></a>');//<i>舞蹈培训</i>
      // $content = getTagData($html, '</li><a name="fl4"></a>', '</li><a name="fl2"></a>');//<i>会计培训</i>
      // $content = getTagData($html, '</li><a name="fl2"></a>', '</li><a name="fl5"></a>');//<i><i>语言培训</i></i>
    //    $content = getTagData($html, '</li><a name="fl5"></a>', '</li><a name="fl59"></a>');//<i>管理培训</i>
        $content = getTagData($html, '</li><a name="fl59"></a>', '</li><div id="note">');//<i>家教辅导</i>

        $urllist=getAllURL($content);


        print_r($urllist);
        echo "<hr>";
//        exit;


        session('urllist', $urllist);
        $t=count($urllist['name']);
        $url=url('index/Cai/getdata',array('s'=>0,'t'=>$t));

//        echo $url;exit;
        $this -> success('采集地址成功',$url,'3');
        //$this->redirect($url);

    }

    public function getdata(){

        //print_r($this->param);

        $s= input("param.s/d", '0');
        $t= input("param.t/d", '0');
        $urllist=session('urllist');
//        print_r($urllist);
//        echo "<hr>";
//        exit;
        if(!empty($urllist['name'][$s])){
            $typename=str_replace("培训","{sys_keywords_name}",$urllist['name'][$s]);
            $url2 ='https:'.$urllist['url'][$s];

            $html = GetFile($url2,2);
            $keywords = getTagData($html, '<h1>', '</h1>');
            $keywords=str_replace("培训","{sys_keywords_name}",$keywords);
            //print_r($html);exit;
            $data=[
                'parent_id'=>'12',
                'level'=>'2',
                'typename'=>$typename,
                'seotitle'=>$keywords,
                'keywords'=>$keywords,
                'description'=>$keywords,
            ];
print_r($data);
//exit;
            $info=$this->logicInfoType->getInfoTypeInfo(['typename'=>$typename]);
            if(empty($info)){
                $result=$this->logicInfoType->infoTypeAdd($data);
            }
            $s=(int)$s+1;
            $url=url('index/Cai/getdata',array('s'=>$s,'t'=>$t));

            $this -> success('采集内容成功',$url,'','30');
            //$result && $this->redirect($url);
        }else{
            echo "采集完成";
        }


//        foreach ($urllist['name'] as $key=>$typename){
//            $typename=str_replace("培训","{sys_keywords_name}",$typename);
//
//            $url2 ='https:'.$urllist['url'][$key];
//            $html = GetFile($url2,2);
//            $keywords = getTagData($html, '<h1>', '</h1>');
//            $keywords=str_replace("培训","{sys_keywords_name}",$keywords);
//
//            $data[]=[
//                'parent_id'=>'1',
//                'level'=>'2',
//                'typename'=>$typename,
//                'seotitle'=>$keywords,
//                'keywords'=>$keywords,
//                'description'=>$keywords,
//            ];
//            print_r($data);
//            exit;
//        }

    }


}



