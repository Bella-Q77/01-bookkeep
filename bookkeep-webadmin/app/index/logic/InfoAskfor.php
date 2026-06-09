<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * InfoAskforor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\logic;

use think\Db;

/**
 *  留言表单管理逻辑
 */
class InfoAskfor extends IndexBase
{

    /**
     *  留言表单添加
     */
    public function infoAskforAdd($data = [])
    {


        $url = url('index/index');

        $addData=[
            'info_id'=>$data['info_id'],
            'type_id'=>$data['type_id'],
            'type_id2'=>$data['type_id2'],
            'member_id'=>$data['member_id'],
            'company_id'=>$data['company_id'],
            'province_id'=>$data['province_id'],
            'city_id'=>$data['city_id'],
            'mobile'=>$data['mobile'],
            'study'=>$data['study'],
            'mobile'=>$data['mobile'],
            'linkman'=>$data['linkman'],
            'weixin'=>$data['weixin'],
            'message'=>$data['message'],
            'ipaddr'=>$data['ipaddr'],
            'create_time'=>TIME_NOW,
            'update_time'=>TIME_NOW,
        ];

        if(!empty($data['mobile'])){
            $addData['mobile']=$data['mobile'];
        }
        if(!empty($data['content'])){
            $addData['content']=$data['content'];
        }
        $result=Db::name('info_askfor')->insert($addData);
        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelInfoAskfor->getError()];
    }

    /**
     *  留言表单管理处信息
     */
    public function getInfoAskforInfo($where = [], $field = true)
    {
        return $this->modelInfoAskfor->getInfo($where, $field);
    }

    public function  send_sms($data=[]){
        $parameter['nationCode']='86';
        $parameter['mobile']=$data['mobile'];
        $parameter['template_id']='659759';
        $parameter['params_array']=[$data['code'],'10'];
        $parameter['sign']='人人海外';
        $res= $this->serviceSms->driverTencent->sendSms($parameter);
        if($res){
            return [RESULT_SUCCESS,'发送成功'];
        }else{
            return [RESULT_ERROR,'发送失败'];
        }
    }

}