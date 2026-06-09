<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Channelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\cms\logic;

use app\common\logic\TableField;

/**
 * 模型字段逻辑
 */
class ArcextField extends CmsBase
{
    /**
     * 析构函数
     */
    function  __construct() {
        $this->tablefield = new TableField();
    }
    /**
     * 模型字段处列表
     */
    public function getArcextFieldList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {

        $list=$this->modelArcextField->getList($where, $field, $order, $paginate)->toArray();
        if($paginate===false) $list['data']=$list;
        foreach ($list['data'] as &$row){
            $row['field_type_text']=$this->modelArcextField->field_type_text($row['field_type']);
        }
        return $list;
    }

    /**
     * 模型字段处列表
     */
    public function getarcextTypeList()
    {
       return  $this->modelArcextField->field_type_text();
    }

    /**
     * 模型管理处信息
     */
    public function getArcextFieldInfo($where = [], $field = true)
    {
        return $this->modelArcextField->getInfo($where, $field);
    }

    /**
     * 模型添加
     */
    public function arcextFieldAdd($data = [])
    {

        $validate_result = $this->validateArcextField->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateArcextField->getError()];
        }

        //为表添加字段
        $rtn=$this->tablefield->add_field(SYS_DB_PREFIX.$data['ext_table'],$data['field_name'],$data['field_type'],$data['maxlength'],$data['default_value'], $data['desc']);
        if($rtn[0]==RESULT_ERROR)  return $rtn;

        $result = $this->modelArcextField->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增扩展表单字段，table：' . $data['ext_table'].',field:'. $data['field_name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelArcextField->getError()];
    }
    /**
     * 模型编辑
     */
    public function arcextFieldEdit($data = [])
    {

        $validate_result = $this->validateArcextField->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateArcextField->getError()];
        }

        //为表添加字段
        $rtn=$this->tablefield->modify_field(SYS_DB_PREFIX.$data['ext_table'],$data['field_name'],$data['field_type'],$data['maxlength'],$data['default_value'], $data['desc']);
        if($rtn[0]==RESULT_ERROR)  return $rtn;

        $url = url('show');

        $result = $this->modelArcextField->setInfo($data);

        $result && action_log('编辑', '编辑扩展表单字段，name：' .$data['ext_table'].',field:'. $data['field_name']);

        return $result ? [RESULT_SUCCESS, '编辑模型字段', $url] : [RESULT_ERROR, $this->modelArcextField->getError()];
    }
    /**
     * 字段删除
     */
    public function arcextFieldDel($where = [])
    {

        $list=$this->getArcextFieldList($where);

        foreach ($list['data'] as $row){
            $this->tablefield->del_field(SYS_DB_PREFIX.$row['ext_table'],$row['field_name']);
        }

        $result = $this->modelArcextField->deleteInfo($where,true);

        $result && action_log('删除', '模型字段，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '字段删除成功'] : [RESULT_ERROR, $this->modelArcextField->getError()];
    }


    /**
     * 模型字段处列表
     */
    public function getExtTableFieldList($main_table=null,$ext_table=null)
    {
        if($main_table && $ext_table){
            $where['main_table']=['=',$main_table];
            $where['ext_table']=['=',$ext_table];
            return $this->modelArcextField->getList($where, true, 'sort asc', false)->toArray();
        }
    }


    /**
     * 应用于添加、编辑，扩展字段表单显示，
     * @param $ext_table
     * @param array $field_val_arr ，是否有默认值
     * @return string
     * Author: lingqifei created by at 2020/3/2 0002
     */
    public function getExtTableFieldListHtml($ext_table, $field_val_arr = [] ) {
        $where['visible']=['=','1'];
        $where['ext_table']=['=',$ext_table];
        $list = $this->modelArcextField->getList($where, "", 'sort asc', false)->toArray();
        $htmltxt = "";
        foreach ( $list as $key => $row ) {
            //是否存在字段值
            $field_value = array_key_exists( $row[ "field_name" ], $field_val_arr ) ? $field_val_arr[ $row[ "field_name" ] ] : "";
            switch ( $row[ 'field_type' ] ) {
                case "varchar":
                    $htmltxt .= '
                                <div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
                    break;
                case "textarea":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<textarea name="' . $row[ "field_name" ] . '" class="form-control" >' . $field_value . '</textarea>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
                    break;
                case "int":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
                    break;
                case "float":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
                    break;
                case "datetime":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control datetimepicker" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
                    break;
                case "date":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control datepicker" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
                    break;
                case "option":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
									  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
								';
                                        $option_arr = explode( ',', $row[ 'default' ] );
                                        foreach ( $option_arr as $va ) {
                                            $htmltxt .= '<option value="' . $va . '" hassubinfo="true">' . $va . '</option>';
                                        }
                                        $htmltxt .= '
									  </select>
									</div>
								</div>';
                    break;
                case "linkage":
                    $htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
									  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
								';
                                            $option_arr = explode( ',', $row[ 'default' ] );
                                            $option_arr = $this->cst_field_ext_linkage( $row[ 'default' ] );
                                            foreach ( $option_arr as $row ) {
                                                $htmltxt .= '<option value="' . $row[ 'id' ] . '" hassubinfo="true">' . $row[ 'name' ] . '</option>';
                                            }
                                            $htmltxt .= '
									  </select>
									</div>
								</div>';
                    break;
                case "radio":
                    $htmltxt .= '<div class="form-group text-left">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<div class="radio i-checks">';
                                        $option_arr = explode( ',', $row[ 'default' ] );
                                        foreach ( $option_arr as $va ) {
                                            $checked = ( $va == $field_value ) ? "checked" : "";
                                            $htmltxt .= '<input type="radio" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . ' /> ' . $va . ' ';
                                        }
                                        $htmltxt .= '
									  </div>
									</div>
								</div>';
                    break;
                case "checkbox":
                    $htmltxt .= '<div class="form-group text-left">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<div class="checkbox i-checks">';
                                            $option_arr = explode( ',', $row[ 'default' ] );
                                            foreach ( $option_arr as $va ) {
                                                $checked = ( $va == $field_value ) ? "checked" : "";
                                                $htmltxt .= '<input type="checkbox" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . '/> ' . $va . ' ';
                                            }
                                            $htmltxt .= '
									  </div>
									</div>
								</div>';
                    break;
                case "img":
                    $pic_div = '';
                    if ($field_value) {
                        $pic_url = get_picture_url($field_value);
                        $pic_div .= '<div style="cursor:pointer; color:red;" class="pic_del"  onclick="picDel' . $row["field_name"] . '(this)" ><img src="' . PATH_PUBLIC . '/addon/file/uploadify-cancel.png" /></div>';
                        $pic_div  .= ' <a target="_blank" href="' . $pic_url . '"><img  style="max-width:150px;" src="' . $pic_url . '"/></a>';
                    }
                    $htmltxt .= '
                                        <div class="form-group text-left">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">';
                    $htmltxt .= '
                                        <link rel="stylesheet" href="' . PATH_PUBLIC . 'static/addon/file/Huploadify.css">
                                        <script src="' . PATH_PUBLIC . '/static/addon/file/jquery.Huploadify.js"></script>
                                        <div id="upload_picture_' . $row["field_name"] . '"></div>
                                        <input type="hidden" name="' . $row["field_name"] . '" id="' . $row["field_name"] . '" value="0">
                                        <div class="upload-img-box' . $row["field_name"] . '"> ' . $pic_div . '</div>
                                        <script type="text/javascript">
                                            var maxwidth = "150px";
                                            $("#upload_picture_' . $row["field_name"] . '").Huploadify({
                                                auto: true,
                                                height: 30,
                                                fileObjName: "file",
                                                buttonText: "上传图片",
                                                uploader: "' . url('admin/File/pictureUpload', array('session_id' => session_id())) . '",
                                                width: 120,
                                                removeTimeout: 1,
                                                fileSizeLimit:"51200",
                                                fileTypeExts: "*.jpg; *.png; *.gif;",
                                                onUploadComplete: uploadPicturelitpic    });
                                            function uploadPicturelitpic(file, data)
                                            {
                                                var data = $.parseJSON(data);
                                                $("#' . $row["field_name"] . '").val(data.id);
                                                var src =\'/public/static/upload/picture/\' + data.path;
                                                var src =src.replace(/\/static/g, \'\');
                                                $(".upload-img-box' . $row["field_name"] . '").html(\'<div class="upload-pre-item"> <a target="_blank" href="\' + src + \'"> <img style="max-width: \' + maxwidth + \';" src="\' + src + \'"/></a></div>\');
                                            }
                                        </script>                
                                        ';
                    $htmltxt .= '</div></div>';
                    break;
                case "imgs":
                    $pic_div = '';
                    if ($field_value) {
                        $pic_arr = explode(',', $field_value);
                        foreach ($pic_arr as $value) {
                            $pic_url = get_picture_url($value);
                            $pic_div .= '
                                        <div class="upload-pre-item" style="float:left; margin: 10px;">
                                            <div style="cursor:pointer; color:red;" class="pic_del"  onclick="picDel' . $row["field_name"] . '(this, ' . $value . ')" >
                                                <img src="' . PATH_PUBLIC . '/addon/file/uploadify-cancel.png" />
                                            </div>
                                            <a target="_blank" href="' . $pic_url . '"> <img style="width:150px;" src="' . $pic_url . '"/></a>
                                        </div>
                            ';
                        }
                    }
                    $htmltxt .= '
                                        <div class="form-group text-left">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">';
                    $htmltxt .= '
<link rel="stylesheet" href="' . PATH_PUBLIC . 'addon/file/Huploadify.css" />
<script src="' . PATH_PUBLIC . 'addon/file/jquery.Huploadify.js"></script>

<div id="upload_pictures_' . $row["field_name"] . '"></div>
<input type="hidden" name="' . $row["field_name"] . '" id="' . $row["field_name"] . '" value="' . $field_value . '"/>
<div class="upload-img-box' . $row["field_name"] . '">' . $pic_div . '</div>

<script type="text/javascript">
    var maxwidth = "150px";
    $("#upload_pictures_' . $row["field_name"] . '").Huploadify({
        auto: true,
        height          : 30,
        fileObjName     : "file",
        buttonText      : "上传多个图片",
       // uploader        : "{:url(\'admin/File/pictureUpload\',array(\'session_id\'=>session_id()))}",
        uploader: "' . url('admin/File/pictureUpload', array('session_id' => session_id())) . '",
        width         : 120,
        removeTimeout	  : 1,
        fileSizeLimit:"51200",
        fileTypeExts: "*.jpg; *.png; *.gif;",
        onUploadComplete : uploadPicture' . $row["field_name"] . '
    });

    function uploadPicture' . $row["field_name"] . '(file, data){
        var data = $.parseJSON(data);
        var addons_name = "' . $row["field_name"] . '";
        var img_ids = $("#" + addons_name).val();
        var add_id = data.id;
        if(img_ids){ var lastChar = img_ids.charAt(img_ids.length - 1);  if(lastChar != \',\'){  add_id = img_ids + \',\' + add_id; } }
        $("#" + addons_name).val(add_id);
        //var src = \'/upload/picture/\' + data.path;
        var src =\'__STATIC__/upload/picture/\' + data.path;
        var src =src.replace(/\/static/g, \'\');
        $(".upload-img-box" + addons_name).append(\'<div class="upload-pre-item" style="float:left; margin: 10px;"> <div style="cursor:pointer; color:red;" class="pic_del"  onclick="picDel' . $row["field_name"] . '(this,\'+data.id+\')" ><img src="' . PATH_PUBLIC . '/addon/file/uploadify-cancel.png" /></div> <a target="_blank" href="\' + src + \'"> <img style="max-width: \' + maxwidth + \';" src="\' + src + \'"/></a></div>\');
    }

    function picDel' . $row["field_name"] . '(obj, pic_id)
    {
        var addons_name = "' . $row["field_name"] . '";
        var img_ids = $("#" + addons_name).val();
        if(img_ids.indexOf(",") > 0)
        {
            img_ids.indexOf(pic_id) == 0 ? img_ids = img_ids.replace(pic_id + \',\', \'\') : img_ids = img_ids.replace(\',\' + pic_id, \'\');
            $("#" + addons_name).val(img_ids);
        }else{
            $("#" + addons_name).val(\'\');
        }
        $(obj).parent().remove();
    }
</script>
                                            ';
                    $htmltxt .= '</div></div>';
                    break;
                default:
                    $htmltxt .= '';
            }
        }
        return $htmltxt;
    }
}
