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
class ChannelField extends CmsBase
{
    /**
     * 析构函数
     */
    function __construct()
    {
        $this->tablefield = new TableField();
    }

    /**
     * 模型字段处列表
     */
    public function getChannelFieldList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {

        $list = $this->modelChannelField->getList($where, $field, $order, $paginate)->toArray();
        if ($paginate === false) $list['data'] = $list;
        foreach ($list['data'] as &$row) {
            $row['field_type_text'] = $this->modelChannelField->field_type_text($row['field_type']);
        }
        return $list;
    }

    /**
     * 模型字段处列表
     */
    public function getExtTableFieldList($main_table = null, $ext_table = null)
    {
        if ($main_table && $ext_table) {
            $where['main_table'] = ['=', $main_table];
            $where['ext_table'] = ['=', $ext_table];
            return $this->modelChannelField->getList($where, "", '', false)->toArray();
        }
    }

    /**
     * 模型字段处列表
     */
    public function getChannelTypeList()
    {

        return $this->modelChannelField->field_type_text();
    }

    /**
     * 模型管理处信息
     */
    public function getChannelFieldInfo($where = [], $field = true)
    {

        return $this->modelChannelField->getInfo($where, $field);
    }

    /**
     * 模型添加
     */
    public function channelFieldAdd($data = [])
    {

        $validate_result = $this->validateChannelField->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateChannelField->getError()];
        }

        //为表添加字段
        $rtn = $this->tablefield->add_field(SYS_DB_PREFIX . $data['ext_table'], $data['field_name'], $data['field_type'], $data['maxlength'], $data['default_value'], $data['desc']);
        if ($rtn[0] == RESULT_ERROR) return $rtn;

        $result = $this->modelChannelField->setInfo($data);
        $url = url('show');
        $result && action_log('新增', '新增模块字段，table：' . $data['ext_table'] . ',field:' . $data['field_name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelChannelField->getError()];
    }

    /**
     * 模型编辑
     */
    public function channelFieldEdit($data = [])
    {

        $validate_result = $this->validateChannelField->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateChannelField->getError()];
        }

        //为表添加字段
        $rtn = $this->tablefield->modify_field(SYS_DB_PREFIX . $data['ext_table'], $data['field_name'], $data['field_type'], $data['maxlength'], $data['default_value'], $data['desc']);
        if ($rtn[0] == RESULT_ERROR) return $rtn;

        $url = url('show');

        $result = $this->modelChannelField->setInfo($data);

        $result && action_log('编辑', '编辑模型字段，name：' . $data['ext_table'] . ',field:' . $data['field_name']);

        return $result ? [RESULT_SUCCESS, '编辑模型字段', $url] : [RESULT_ERROR, $this->modelChannelField->getError()];
    }

    /**
     * 字段删除
     */
    public function channelFieldDel($where = [])
    {

        $list = $this->getChannelFieldList($where);

        foreach ($list['data'] as $row) {
            $this->tablefield->del_field(SYS_DB_PREFIX . $row['ext_table'], $row['field_name']);
        }

        $result = $this->modelChannelField->deleteInfo($where, true);

        $result && action_log('删除', '模型字段，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '字段删除成功'] : [RESULT_ERROR, $this->modelChannelField->getError()];
    }


    //扩展字段表单显示
    //pararm $ext_table 		[description] 扩展表名
    //pararm field_val_arr 		[description] 需要展示的字段html
    //return html string
    public function channelExtFieldHtml($ext_table, $field_val_arr = [])
    {
        $where['visible'] = ['=', '1'];
        $where['ext_table'] = ['=', $ext_table];
        $list = $this->modelChannelField->getList($where, "", 'sort asc', false)->toArray();
        $htmltxt = "";
        foreach ($list as $key => $row) {
            $field_value = array_key_exists($row["field_name"], $field_val_arr) ? $field_val_arr[$row["field_name"]] : "";//是否存在字段值

            switch ($row['field_type']) {
                case "varchar":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <input name="' . $row["field_name"] . '" class="form-control" type="text" value="' . $field_value . '"/>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
								        </div>';
                    break;
                case "textarea":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <textarea name="' . $row["field_name"] . '" class="form-control" >' . $field_value . '</textarea>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
                                    </div>';
                    break;
                case "text":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <textarea name="' . $row["field_name"] . '" class="form-control" >' . $field_value . '</textarea>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
                                    </div>';
                    break;
                case "htmltext":
                    $htmltxt .= '<div class="form-group">
									        <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
									        <div class="col-sm-10">
									
<textarea name="' . $row["field_name"] . '" class="form-control" >' . $field_value . '</textarea>
<span class="help-block m-b-none">' . $row['desc'] . '</span> 
<link rel="stylesheet" href="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/editor/kindeditor/themes/default/default.css" />
<script src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/editor/kindeditor/kindeditor-all-min.js"></script>
<script src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/editor/kindeditor/lang/zh-CN.js"></script>
<script type="text/javascript">
$(function(){
    var editor_' . $row["field_name"] . ';
    
    editor_' . $row["field_name"] . ' = KindEditor.create(\'textarea[name="' . $row["field_name"] . '"]\', {
            themesPath: KindEditor.basePath+\'/themes/\',//主题路径
            width: \'100%\',
            height: \'100px\',
            resizeType: 1,
            pasteType : 2,
            urlType : \'absolute\',
            fileManagerJson : \'\',
            uploadJson : "' . addons_url("editor://Upload/pictureUpload") . '",
            items : [
            \'source\', \'undo\', \'redo\', \'cut\', \'copy\',\'paste\', \'plainpaste\', \'wordpaste\',\'selectall\',
            \'justifyleft\',\'justifycenter\',\'justifyright\',\'justifyfull\',\'insertorderedlist\',\'insertunorderedlist\',\'indent\',
            \'outdent\',\'subscript\',\'superscript\',\'fontname\',\'fontsize\',\'forecolor\',\'hilitecolor\',\'bold\',
            \'italic\',\'underline\',\'strikethrough\',\'removeformat\',\'image\',\'multiimage\',\'table\',
            \'link\',\'unlink\',\'fullscreen\'
            ],
             afterCreate: function () {
                var editorObj = this;
                var doc = editorObj.edit.doc;
                $(doc.body).bind("paste", function (event) {
                    setTimeout(function () {
                        // 处理bug
                        var useless = $(doc.body).find(".__kindeditor_paste__");
                        if (useless) {
                            useless.removeAttr("style");
                            useless.removeClass("__kindeditor_paste__");
                        }
                        var imgs = $(doc.body).find("img");
                        $.each(imgs, function (index, item) {
                            // layer
                            layerindex = layer.load(1, {
                                shade: [0.3, "#fff"],
                                content: \'转存中\',
                                success: function (layero) {
                                    layero.find(\'.layui-layer-content\').css({
                                        \'padding-top\': \'39px\',
                                        \'width\': \'120px\',
                                        \'margin-left\': \'-60px\'
                                    });
                                }
                            });
                            var _that = $(this);
                            var imgSrc = decodeURIComponent(_that.attr("src"));
                            if (imgSrc.indexOf("file://") > -1) {
                                layer.close(layerindex);
                            }else if (imgSrc.indexOf("http://") > -1) {
                                layer.close(layerindex);
                            } else if (imgSrc.indexOf("https://") > -1) {
                                layer.close(layerindex);
                            } else if (imgSrc.indexOf("data:") > -1) {
                                var blob = dataURLtoBlob(imgSrc);
                                // 上传粘贴板中的截图到服务器
                                var form = document.imgForm;
                                var formData = new FormData(form);
                                formData.append("imgFile", blob);
                                $.ajax({
                                        type: "POST",
                                        url: "' . addons_url("editor://Upload/pictureUpload") . '",
                                        data: formData,
                                        dataType: "json",
                                        // async: false,
                                        processData: false,
                                        contentType: false,
                                        success: function (res) {
                                            log(res);
                                            layer.close(layerindex);
                                            if (res.error=="0") {
                                                _that.attr(\'src\',res.url);
                                                _that.attr(\'data-ke-src\',res.url);
                                                _that.attr(\'alt\', res.url);
                                            }
                                        },
                                        fail: function () {
                                                layer.close(layerindex);
                                        }
                                       
                                });
                            } else if (imgSrc.indexOf("/upload/") === -1) {
                                // ajax异步上传其他网络图片
                                $.ajax({
                                    type: "POST",
                                    url:"'.addons_url("editor://Upload/pictureUpload").'",
                                    data: JSON.stringify({ url: imgSrc }),
                                    dataType: "json",
                                    // async: false,
                                    processData: false,
                                    contentType: "application/json;charset=UTF-8",
                                    success: function (res) {
                                        layer.close(layerindex);
                                            // 重置图片
                                            _that.attr("src", res.url);
                                            _that.attr("data-ke-src", res.url);
                                            _that.attr("alt", res.name);
                                    },
                                    fail: function () {
                                        layer.close(layerindex);
                                    }
                                                
                                });     
                            } else {
                                // 本站网络图片不处理
                                layer.close(layerindex);
                            }
                        });
                        
                    }, 10);//end timeout
                    
                });//end bind paste
             },//end afterCreate
             extraFileUploadParams: { session_id : "'.session_id().'"}

    });//editor

    //ajax提交之前同步
    $(\'button[type="submit"],#submit,.ajax-post,#autoSave\').click(function(){
            editor_' . $row["field_name"] . '.sync();
    });
});
</script>
									</div>
								</div>';
                    break;
                case "int":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <input name="' . $row["field_name"] . '" class="form-control" type="text" value="' . $field_value . '"/>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
                                        </div>';
                    break;
                case "float":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <input name="' . $row["field_name"] . '" class="form-control" type="text" value="' . $field_value . '"/>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
                                        </div>';
                    break;
                case "datetime":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <input name="' . $row["field_name"] . '" class="form-control datetimepicker" type="text" value="' . $field_value . '"/>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
                                        </div>';
                    break;
                case "date":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <input name="' . $row["field_name"] . '" class="form-control datepicker" type="text" value="' . $field_value . '"/>
                                                <span class="help-block m-b-none">' . $row['desc'] . '</span> 
                                            </div>
                                        </div>';
                    break;
                case "option":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                              <select data-placeholder="选择' . $row["show_name"] . '..." name="' . $row["field_name"] . '" class="chosen-select ' . $row["field_name"] . '-chosen-select" style="width: 200px;" tabindex="2">
                                        ';
                    $option_arr = explode(',', $row['default_value']);
                    foreach ($option_arr as $va) {
                        $option_chk = ($va == $field_value) ? "selected" : "";
                        $htmltxt .= '<option value="' . $va . '" hassubinfo="true" ' . $option_chk . '>' . $va . '</option>';
                    }
                    $htmltxt .= '
                                              </select>
                                            </div>
                                        </div>';
                    break;
                case "select":
                    $htmltxt .= '<div class="form-group">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                              <select data-placeholder="选择' . $row["show_name"] . '..." name="' . $row["field_name"] . '" class="chosen-select ' . $row["field_name"] . '-chosen-select" style="width: 200px;" tabindex="2">
                                        ';
                    $option_arr = explode(',', $row['default_value']);
                    foreach ($option_arr as $va) {
                        $option_chk = ($va == $field_value) ? "selected" : "";
                        $htmltxt .= '<option value="' . $va . '" hassubinfo="true" ' . $option_chk . '>' . $va . '</option>';
                    }
                    $htmltxt .= '
                                              </select>
                                            </div>
                                        </div>';
                    break;
                case "linkage":
                    $htmltxt .= '
                                <div class="form-group">
									<label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
									<div class="col-sm-10">
									  <select data-placeholder="选择' . $row["show_name"] . '..." name="' . $row["field_name"] . '" class="chosen-select ' . $row["field_name"] . '-chosen-select" >
								';
                    $option_arr = $this->channelExtFieldLinkage($row['default_value']);
                    foreach ($option_arr as $row) {
                        $htmltxt .= '<option value="' . $row['id'] . '" hassubinfo="true">' . $row['name'] . '</option>';
                    }
                    $htmltxt .= '
									  </select>
									</div>
								</div>';
                    break;
                case "radio":
                    $htmltxt .= '<div class="form-group text-left">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">
                                                <div class="radio i-checks">
                                        ';
                    $option_arr = explode(',', $row['default_value']);
                    foreach ($option_arr as $va) {
                        $checked = ($va == $field_value) ? "checked" : "";
                        $htmltxt .= '<input type="radio" name="' . $row["field_name"] . '" value="' . $va . '" ' . $checked . ' /> ' . $va . ' ';
                    }
                    $htmltxt .= '
                                              </div>
                                            </div>
                                        </div>';
                    break;
                case "checkbox":
                    $htmltxt .= '<div class="form-group text-left">
									<label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
									<div class="col-sm-10">
										<div class="checkbox i-checks">
								';
                    $option_arr = explode(',', $row['default_value']);
                    foreach ($option_arr as $va) {
                        $htmltxt .= '<input type="checkbox" name="' . $row["field_name"] . '" value="' . $va . '" ' . $checked . '/> ' . $va . ' ';
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
                        $pic_div .= '<div style="cursor:pointer; color:red;" class="pic_del"  onclick="picDel' . $row["field_name"] . '(this)" ><img src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/uploadify-cancel.png" /></div>';
                        $pic_div .= ' <a target="_blank" href="' . $pic_url . '"><img  style="max-width:150px;" src="' . $pic_url . '"/></a>';
                    }
                    $htmltxt .= '
                                        <div class="form-group text-left">
                                            <label class="col-sm-2 control-label">' . $row["show_name"] . '</label>
                                            <div class="col-sm-10">';
                    $htmltxt .= '
                                        <link rel="stylesheet" href="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/Huploadify.css">
                                        <script src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/jquery.Huploadify.js"></script>
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
                                                var src =\'__STATIC__/upload/picture/\' + data.path;
                                                var src =src.replace(/\/static/g, \'\');
                                                $(".upload-img-box' . $row["field_name"] . '").html(\'<div class="upload-pre-item"> <a target="_blank" href="\' + src + \'"> <img style="max-width: \' + maxwidth + \';" src="\' + src + \'"/></a></div>\');
                                            }
                                        </script>                
                                        ';
                    $htmltxt .= '<span class="help-block m-b-none">' . $row['desc'] . '</span> ';
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
                                                <img src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/uploadify-cancel.png" />
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
                    
<link rel="stylesheet" href="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/Huploadify.css" />
<script src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/jquery.Huploadify.js"></script>
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
        $(".upload-img-box" + addons_name).append(\'<div class="upload-pre-item" style="float:left; margin: 10px;"> <div style="cursor:pointer; color:red;" class="pic_del"  onclick="picDel' . $row["field_name"] . '(this,\'+data.id+\')" ><img src="' . STATIC_DOMAIN . SYS_DS_PROS . SYS_STATIC_DIR_NAME . '/addon/file/uploadify-cancel.png" /></div> <a target="_blank" href="\' + src + \'"> <img style="max-width: \' + maxwidth + \';" src="\' + src + \'"/></a></div>\');
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

                    $htmltxt .= '<span class="help-block m-b-none">' . $row['desc'] . '</span> ';
                    $htmltxt .= '</div></div>';
                    break;
                default:
                    $htmltxt .= '';
            }
        }


        return $htmltxt;
    }

    public function channelExtFieldLinkage($linkpage)
    {
        $data = [];
        switch ($linkpage) {
            case "sys_user":
                $sql = "select id,name from fly_sys_user";
                $data = $this->C($this->cacheDir)->findAll($sql);
                break;
            default:
                echo "Your favorite fruit is neither apple, banana, or orange!";
        }
        return $data;
    }

}
