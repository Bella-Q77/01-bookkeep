<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Websiteor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\cms\logic;

use app\common\logic\TableField;
use think\Db;
/**
 * 网站管理=》逻辑层
 */
class Website extends CmsBase
{
    /**
     * 网站列表
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int|mixed $paginate
     * @return
     */
    public function getWebsiteList($where = [], $field = true, $order = 'sort asc', $paginate = DB_LIST_ROWS)
    {
        return $this->modelWebsite->getList($where, $field, $order, $paginate)->toArray();
    }

    /**
     * 网站编辑
     * @param array $data
     * @return array
     */
    public function setting($data = [])
    {

        foreach ($data as $key=>$value){
            $map['name']=['=',$key];
            $result=Db::name('website')->where('name',$key)->setField('value', $value);
        }
        $url = url('show');
        $result && action_log('编辑', '编辑网站配置');
        return  [RESULT_SUCCESS, '编辑成功', $url];
    }




    //得到系统配置参数
    public function getWebsiteInfoHtml($data){
        $where=[];
        $string ='';
        if(!empty($data['groupid'])){
            $where['group']=['=',$data['groupid']];
        }
        $list=$this->modelWebsite->getList($where, true, 'id asc', false)->toArray();
        foreach($list as $key=>$row){
            $string .=$this->getCreateHtml($row);
        }
        return $string;
    }


    /**
     * 根据字段创建展示代码
     * @param $row
     * @return string
     * Author: lingqifei created by at 2020/4/24 0024
     */
    public function getCreateHtml($row){
        $htmltxt='';
        switch ( $row[ 'type' ] ) {
            case "varchar":
                $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "title" ] . '</label>
									<div class="col-sm-8">
										<input name="' . $row[ "name" ] . '" class="form-control" type="text" value="' . $row[ "value" ] . '"/>
										<span class="help-block m-b-none">' . $row[ 'describe' ] . '</span> 
									</div>
									<div class="col-sm-2">' . $row[ 'name' ] . '</div>
								</div>';
                break;
            case "textarea":
                $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "title" ] . '</label>
									<div class="col-sm-8">
										<textarea name="' . $row[ "name" ] . '" class="form-control" >' . $row[ "value" ] . '</textarea>
										<span class="help-block m-b-none">' . $row[ 'describe' ] . '</span> 
									</div>
									<div class="col-sm-2">' . $row[ 'name' ] . '</div>
								</div>';
                break;
            case "int":
                $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "title" ] . '</label>
									<div class="col-sm-8">
										<input name="' . $row[ "name" ] . '" class="form-control" type="text" value="' . $row[ "value" ] . '"/>
										<span class="help-block m-b-none">' . $row[ 'describe' ] . '</span> 
									</div>
									<div class="col-sm-2">' . $row[ 'name' ] . '</div>
								</div>';
                break;
            case "float":
                $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "title" ] . '</label>
									<div class="col-sm-8">
										<input name="' . $row[ "name" ] . '" class="form-control" type="text" value="' . $row[ "value" ] . '"/>
										<span class="help-block m-b-none">' . $row[ 'describe' ] . '</span> 
									</div>
									<div class="col-sm-2">' . $row[ 'name' ] . '</div>
								</div>';
                break;
            case "bool":
                $row[ "value" ] && $checked='checked="checked"';
                $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "title" ] . '</label>
									<div class="col-sm-8">';
                if($row['value']==1){
                    $htmltxt .='<div class="checkbox i-checks">';
                    $htmltxt .= '<input name="' . $row[ "name" ] . '" type="radio" value="1" checked/>开启&nbsp;&nbsp; ';
                    $htmltxt .= '<input name="' . $row[ "name" ] . '" type="radio" value="0" /> 关闭';
                    $htmltxt .='</div>';
                }else{
                    $htmltxt .='<div class="radio i-checks">';
                    $htmltxt .= '<input name="' . $row[ "name" ] . '" type="radio" value="1"/>开启&nbsp;&nbsp; ';
                    $htmltxt .= '<input name="' . $row[ "name" ] . '" type="radio" value="0" checked/>关闭';
                    $htmltxt .='</div>';
                }
                $htmltxt .='<span class="help-block m-b-none">' . $row[ 'describe' ] . '</span> 
									</div>
									<div class="col-sm-2">' . $row[ 'name' ] . '</div>
								</div>';
                break;
            default:
                $htmltxt .= '';
        }
        return $htmltxt;
    }



    /**
     * 配置添加
     */
    public function websiteAdd($data = [])
    {

        $validate_result = $this->validateWebsite->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateWebsite->getError()];
        }

        $result = $this->modelWebsite->setInfo($data);

        $result && action_log('新增', '新增配置，name：' . $data['name']);
        $url = url('websiteList', array('group' => $data['group'] ? $data['group'] : 0));
        return $result ? [RESULT_SUCCESS, '配置添加成功', $url] : [RESULT_ERROR, $this->modelWebsite->getError()];
    }

    /**
     * 配置编辑
     */
    public function websiteEdit($data = [])
    {

        $validate_result = $this->validateWebsite->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateWebsite->getError()];
        }

        $result = $this->modelWebsite->setInfo($data);

        $result && action_log('编辑', '编辑配置，name：' . $data['name']);
        $url = url('websiteList', array('group' => $data['group'] ? $data['group'] : 0));
        return $result ? [RESULT_SUCCESS, '配置编辑成功', $url] : [RESULT_ERROR, $this->modelWebsite->getError()];
    }

    /**
     * 配置删除
     */
    public function websiteDel($where = [])
    {

        $result = $this->modelWebsite->deleteInfo($where);

        $result && action_log('删除', '删除配置，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '菜单删除成功'] : [RESULT_ERROR, $this->modelWebsite->getError()];
    }

    public function getWebsiteInfo($where = [],$field=true){
        return $this->modelWebsite->getInfo($where, $field);
    }


    /**
     * 参数分组
     * @return mixed
     * Author: lingqifei created by at 2020/4/24 0024
     */
    public function getWebsiteGroup(){
        $list=$this->modelWebsite->getGroup();
        return $list;
    }

    /**
     * 参数字段类型
     * @return mixed
     * Author: lingqifei created by at 2020/4/24 0024
     */
    public function getWebsiteType(){
        $list=$this->modelWebsite->getType();
        return $list;
    }

}
