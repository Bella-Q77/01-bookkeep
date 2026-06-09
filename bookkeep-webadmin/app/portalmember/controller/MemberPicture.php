<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2021 07FLY Network Technology Co,LTD.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/
namespace app\portalmember\controller;


/**
 * 会员中心图片管理=》首页
 */
class MemberPicture extends PortalmemberBaseAuth
{

    /**
     * 显示
     */
    public function show()
    {
        $map['member_id']=['=',$this->member['id']];
        $list=$this->logicMemberPicture->getMemberPictureList($map,'','',24);
        $totals=$list->total();
        $pages=$list->render('info,pre,next,pageno',24);
        $this->assign('totals', $totals);
        $this->assign('pages', $pages);
        $this->assign('list', $list);
        return $this->fetch('member_picture_show');
    }


    /**
     * 上传
     */
    public function add()
    {
        IS_POST && $this->jump($this->logicMemberPicture->memberPictureAdd($this->param));
        $map['member_id']=['=',$this->member['id']];
        $totals=$this->logicMemberPicture->getMemberPictureStat($map);
        $this->assign('totals', $totals);
        return $this->fetch('member_picture_add');
    }

    /**
     * 上传图片
     */
    public function edit()
    {
        IS_POST && $this->jump($this->logicMemberCompany->memberCompanyEdit($this->param));

        $map['member_id']=['=',$this->member['id']];
        $info=$this->logicMemberCompany->getMemberCompanyInfo($map);
        $this->assign('info', $info);
        return $this->fetch('company_edit');
    }

    /**
     * 删除
     */
    public function del()
    {
       $this->jump($this->logicMemberPicture->memberPictureDel($this->param));
    }

    /**
     * 选择
     */
    public function lookup()
    {
        if(!empty($this->param['img_id'])){
            $this->assign('img_id', $this->param['img_id']);
        }else{
            $this->assign('img_id', 'img_id');
        }

        if(!empty($this->param['img_html'])){
            $this->assign('img_html', $this->param['img_html']);
        }else{
            $this->assign('img_html', 'img_html');
        }
        $map['member_id']=['=',$this->member['id']];
        $map['status']=['=','1'];
        $list=$this->logicMemberPicture->getMemberPictureList($map,'','',16);
        $pages=$list->render('info,pre,next,pageno',16);
        $this->assign('pages', $pages);
        $this->assign('list', $list);
        return $this->fetch('member_picture_lookup');
    }

    /**
     * 选择=>编辑器选择
     */
    public function editor_manager()
    {
        $map['member_id']=['=',$this->member['id']];
        $list=$this->logicMemberPicture->getMemberPictureList($map,'','',100);
        $data=[
            'moveup_dir_path'=>'',
            'current_dir_path'=>'',
            'current_url'=>'',
            'total_count'=>count($list),
        ];
        $tmp=[];
        foreach ($list as $key=>$row){
            $tmp[$key]['is_dir']=false;
            $tmp[$key]['has_file']=false;
            $tmp[$key]['filesize']=100;
            $tmp[$key]['dir_path']='';
            $tmp[$key]['is_photo']=true;
            $tmp[$key]['filename']=get_picture_url2($row['path']);
            //$tmp[$key]['filename']=$row['path'];
            $tmp[$key]['datetime']=format_time();
        }
        $data['file_list']=$tmp;
        return  json($data);
    }
}
?>