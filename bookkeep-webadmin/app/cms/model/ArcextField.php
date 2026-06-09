<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Author: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */
namespace app\cms\model;

/**
 * 频道字段模型
 */
class ArcextField extends CmsBase
{
    //扩展数据类型
    public function  field_type_text( $key = null ) {
        $data = array(
            "varchar" => array( 'name' => '单行文本(varchar)', 'type' => 'varchar' ),
            "textarea" => array( 'name' => '多行文本', 'type' => 'varchar' ),
            "htmltext" => array( 'name' => 'HTML文本', 'type' => 'varchar' ),
            "int" => array( 'name' => '整数类型', 'type' => 'int' ),
            "float" => array( 'name' => '小数类型', 'type' => 'float' ),
            "datetime" => array( 'name' => '时间类型', 'type' => 'datetime' ),
            "date" => array( 'name' => '日期类型', 'type' => 'date' ),
            "imgurl" => array( 'name' => '图片(仅网址))', 'type' => 'varchar' ),
            "option" => array( 'name' => '使用option下拉框', 'type' => 'varchar' ),
            "radio" => array( 'name' => '使用radio选项卡', 'type' => 'varchar' ),
            "img" => array( 'name' => '单图片', 'type' => 'varchar' ),
            "imgs" => array( 'name' => '多个图片', 'type' => 'varchar' ),
            "checkbox" => array( 'name' => 'Checkbox多选框', 'type' => 'varchar' ),
            "linkage" => array( 'name' => '系统内部关联', 'type' => 'varchar' )
        );
        return ( $key ) ? $data[ $key ] : $data;
    }


    //扩展字段表单显示
    //pararm $ext_table 		[description] 扩展表名
    //pararm field_val_arr 		[description] 需要展示的字段html
    //return html string
    public function field_ext_html( $ext_table, $field_val_arr = array() ) {
        $sql = "select * from cms_field_ext where ext_table='$ext_table' and visible='1' order by sort asc;";
        $list = $this->C( $this->cacheDir )->findAll( $sql );
        $htmltxt = "";
        foreach ( $list as $key => $row ) {
            //是否存在字段值
            $field_value = array_key_exists( $row[ "field_name" ], $field_val_arr ) ? $field_val_arr[ $row[ "field_name" ] ] : "";
            switch ( $row[ 'field_type' ] ) {
                case "varchar":
                    $htmltxt .= '<div class="form-group">
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
                    $option_arr = $this->cms_field_ext_linkage( $row[ 'default' ] );
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
										<div class="radio i-checks">
								';
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
										<div class="checkbox i-checks">
								';
                    $option_arr = explode( ',', $row[ 'default' ] );
                    foreach ( $option_arr as $va ) {
                        $htmltxt .= '<input type="checkbox" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . '/> ' . $va . ' ';
                    }
                    $htmltxt .= '
									  </div>
									</div>
								</div>';
                    break;
                default:
                    $htmltxt .= '';
            }
        }
        return $htmltxt;
    }


    //推展字段表单显示
    public function cms_field_ext_html_one( $ext_table, $field_name = '', $field_value = '' ) {
        $sql = "select * from cms_field_ext where ext_table='$ext_table' and field_name='$field_name' order by sort asc;";
        $row = $this->C( $this->cacheDir )->findOne( $sql );
        $htmltxt = "";
        switch ( $row[ 'field_type' ] ) {
            case "varchar":
                $htmltxt .= '<div class="form-group">
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
                $htmltxt .= '<div class="form-group text-left pd-b-5" >

								  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
								  <option value="" hassubinfo="true">选择' . $row[ "show_name" ] . '...</option>
							';
                $option_arr = explode( ',', $row[ 'default' ] );
                foreach ( $option_arr as $va ) {
                    $htmltxt .= '<option value="' . $va . '" hassubinfo="true">' . $va . '</option>';
                }
                $htmltxt .= '
								  </select>
							</div>';
                break;
            case "linkage":
                $htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
								  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
							';
                $option_arr = explode( ',', $row[ 'default' ] );
                $option_arr = $this->cms_field_ext_linkage( $row[ 'default' ] );
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
									<div class="radio i-checks">
							';
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
									<div class="checkbox i-checks">
							';
                $option_arr = explode( ',', $row[ 'default' ] );
                foreach ( $option_arr as $va ) {
                    $htmltxt .= '<input type="checkbox" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . '/> ' . $va . ' ';
                }
                $htmltxt .= '
								  </div>
								</div>
							</div>';
                break;
            default:
                $htmltxt .= '';
        }
        return $htmltxt;
    }


    //获得关联联动数据,主要为关联内部数据
    //param  $type 			[description]传入内部关联数据标识
    //return array() 		[description]返回一个二维数组
    public function cms_field_ext_linkage( $type ) {
        $data = array();
        switch ( $type ) {
            case "sys_user":
                $sql = "select id,name from fly_sys_user";
                $data = $this->C( $this->cacheDir )->findAll( $sql );
                break;
            default:
                echo "Your favorite fruit is neither apple, banana, or orange!";
        }
        return $data;
    }

    //获得为下拉选项的字段，
    public function cms_field_ext_option( $ext_table, $field_type ) {
        $sql = "select * from cms_field_ext where ext_table='$ext_table' order by sort asc;";
        $list = $this->C( $this->cacheDir )->findAll( $sql );
        $rtnArr = array();
        foreach ( $list as $row ) {
            if ( $row[ 'field_type' ] == $field_type ) {
                $rtnArr[] = $row[ 'field_name' ];
            }
        }
        return $rtnArr;
    }

}
