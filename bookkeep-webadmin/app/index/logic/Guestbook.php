<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Guestbookor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\logic;

use think\Db;

/**
 *  留言表单管理逻辑
 */
class Guestbook extends IndexBase
{

    /**
     *  留言表单添加
     */
    public function guestbookAdd($data = [])
    {

        $table=$this->getGuesbookExtTableInfo($data['tid']);
        $extfieldlist=$this->logicGuestbookField->getExtTableFieldList($table['maintable'],$table['addtable']);

        $extfieldArr=array_column($extfieldlist,'field_name');

        $url=empty($data['url'])?url('index/index'):$data['url'];

        if(empty($extfieldlist)){
           return  [RESULT_ERROR, '请填写正确的表单号', $url];
           exit;
        }
        if(empty($data['addfield'])){
            return  [RESULT_ERROR, '请填写要保存的字段名称', $url];
            exit;
        }

        $addfieldArr=explode(',',$data['addfield']);
        $addData=[
            'gid'=>$data['tid'],
            'create_time'=>TIME_NOW,
            'update_time'=>TIME_NOW,
        ];

        if(!empty($data['mobile'])){
            $addData['mobile']=$data['mobile'];
        }
        if(!empty($data['content'])){
            $addData['content']=$data['content'];
        }
        foreach ($addfieldArr as $field){
            if(in_array($field,$extfieldArr)){
                $val=!empty($data[$field])?$data[$field]:'';
                $addData[$field]=$val;
            }
        }

        $result=Db::name($table['addtable'])->insert($addData);

        $result && action_log('新增', '新增留言信息，表单name：' . $data['addfield']);
        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelGuestbook->getError()];
    }

    /**
     *  留言表单管理处信息
     */
    public function getGuestbookInfo($where = [], $field = true)
    {

        return $this->modelGuestbook->getInfo($where, $field);
    }

    /**
     *  留言扩展列表
     */
    public function getGuestbookExtList($data=[])
    {
        if(empty($data['gid'])){
            return [RESULT_ERROR,'选择表单'];
        }
        $table=$this->getGuesbookExtTableInfo($data['gid']);
        $extfieldlist=$this->logicGuestbookField->getExtTableFieldList($table['maintable'],$table['addtable']);

        //扩展数据处理
        $where['gid']=['=',$data['gid']];
        $list=Db::name($table['addtable'])
            ->where($where)
            ->order('create_time desc')
            ->paginate(DB_LIST_ROWS)
            ->ToArray();

        foreach ($list['data'] as &$row){
            $extcontent='';
            $row['create_time']=date('Y-m-d H:i:s',$row['create_time']);
            foreach ($extfieldlist as $item){
                $extcontent .='<br><b>'.$item['show_name'].'：</b>'.$row[$item['field_name']];
            }
            $extcontent .='<br><b>内容：</b>'.$row['content'].'<br>';
            $row['extcontent']=$extcontent;
        }
        return $list;
    }

    /**获取表单数据表信息
     * @param int $gid
     * @return mixed
     * Author: lingqifei created by at 2020/3/2 0002
     */
    public function getGuesbookExtTableInfo($gid=0)
    {
        $info=$this->modelGuestbook->getInfo(['id'=>$gid],'addtable,maintable');
        return $info;
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