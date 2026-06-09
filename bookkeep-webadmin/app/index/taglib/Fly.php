<?php

namespace app\index\taglib;

use think\Db;
use think\template\TagLib;

/**
 * 07fly 签库解析类
 * @category   Think
 * @package  Think
 * @subpackage  Driver.Taglib
 * @author    开发人生 <574249366@qq.com>
 */
class Fly extends TagLib
{


    // 标签定义
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'php' => ['attr' => ''],
        'global' => ['attr' => 'name', 'close' => 0],
        'position' => ['attr' => 'symbol,style', 'close' => 0],
        'channel' => ['attr' => 'typeid,notypeid,reid,type,row,currentstyle,id,name,key,empty,mod,titlelen,offset,limit'],
        'arclist' => ['attr' => 'channelid,typeid,notypeid,cityid,row,offset,titlelen,limit,orderby,orderway,noflag,flag,infolen,empty,mod,name,id,key,addfields,tagid,pagesize,thumb,joinaid'],
        'arcview' => ['attr' => 'aid,empty,id,addfields,joinaid'],
        'arcclick'   => ['attr' => 'aid,value,type', 'close' => 0],
        'type' => ['attr' => 'typeid,empty,id,addfields,joinaid'],

        //文章扩展列表
        'arcextlist' => ['attr' => 'channelid,eid,aid,row,offset,titlelen,limit,orderby,orderway,infolen,empty,mod,name,id,key,addfields,tagid,pagesize,thumb,joinaid'],
        'arcsublist' => ['attr' => 'channelid,typeid,row,offset,titlelen,limit,orderby,orderway,infolen,empty,mod,name,id,key,addfields,tagid,pagesize,thumb,joinaid,linkfield,linkreg,linkvalue'],
        'arcsubcount' => ['attr' => 'channelid,typeid,linkfield,linkreg,linkvalue', 'close' => 0],//统计下级文章个数

        // 相关文章
        'likearticle'    => ['attr' => 'channelid,limit,row,titlelen,infolen,typeid,orderby,orderway,empty,mod,name,id,key,thumb'],
        'prenext'    => ['attr' => 'get,titlelen,id,empty'],

        //文章列表
        'list' => ['attr' => 'channelid,channelexttable,typeid,notypeid,cityid,pagesize,titlelen,orderby,orderway,noflag,flag,infolen,empty,mod,id,key,addfields,thumb'],
        'pagelist' => ['attr' => 'listitem,listsize', 'close' => 0],


        //分类信息
        'infolist' => ['attr' => 'typeid,notypeid,cityid,row,offset,titlelen,limit,orderby,orderway,noflag,flag,infolen,empty,mod,name,id,key,addfields,tagid,pagesize,thumb,joinaid'],
        'infotype' => ['attr' => 'typeid,notypeid,reid,type,row,currentstyle,id,name,key,empty,mod,titlelen,offset,limit'],

		//帮助文档模块
		'booklist' => ['attr' => 'typeid,notypeid,row,offset,titlelen,limit,orderby,orderway,noflag,flag,infolen,empty,mod,name,id,key,addfields,pagesize'],

        //查询地址
        'searchurl'  => ['attr' => '', 'close' => 0],
        'searchform' => ['attr' => 'channel,channelid,typeid,notypeid,flag,noflag,type,empty,id,mod,key', 'close'=>1],
        'ads' => ['attr' => 'aid,id'],
        'adslist' => ['attr' => 'adsid,row,order,where,id,empty,key,mod,currentstyle'],
        'tag' => ['attr' => 'aid,name,row,id,key,mod,typeid,getall,sort,empty,style,type'],
        'flink' => ['attr' => 'type,row,id,key,mod,titlelen,empty,limit'],
        'guestbookform'=> ['attr' => 'typeid,type,empty,id,mod,key,before,beforeSubmit'],

        //重写模板标签
        'assign' => ['attr' => 'name,value', 'close' => 0],
        'empty' => ['attr' => 'name'],
        'notempty' => ['attr' => 'name'],
        'foreach' => ['attr' => 'name,id,item,key,offset,length,mod', 'expression' => true],
        'volist' => ['attr' => 'name,id,offset,length,key,mod,limit,row', 'alias' => 'iterate'],
        'if' => ['attr' => 'condition', 'expression' => true],
        'elseif' => ['attr' => 'condition', 'close' => 0, 'expression' => true],
        'else' => ['attr' => '', 'close' => 0],
        'switch' => ['attr' => 'name', 'expression' => true],
        'case' => ['attr' => 'value,break', 'expression' => true],
        'default' => ['attr' => '', 'close' => 0],
        'compare' => ['attr' => 'name,value,type', 'alias' => ['eq,equal,notequal,neq,gt,lt,egt,elt,heq,nheq', 'type']],
    ];

    /**
     * 自动识别构建变量，传值可以使变量也可以是值
     * @access private
     * @param string $value 值或变量
     * @return string
     */
    private function varOrvalue($value)
    {
        $flag = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
        } else {
            $value = str_replace('"', '\"', $value);
            $value = '"' . $value . '"';
        }

        return $value;
    }

    /**
     * 栏目标签
     */
    public function tagChannel($tag, $content)
    {

        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $notypeid = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid = $this->varOrvalue($notypeid);

        $name = !empty($tag['name']) ? $tag['name'] : '';
        $type = !empty($tag['type']) ? $tag['type'] : 'son';
        $currentstyle = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 100;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }
        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' if(isset($ui_typeid) && !empty($ui_typeid)) : $typeid = $ui_typeid; else: $typeid = ' . $typeid . '; endif;';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = ' . $row . '; endif;';
        
        if ($name) { // 从模板中传入数据集
            $symbol = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }

            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $tagChannel = new \app\index\taglib\TagChannel;';
            $parseStr .= ' $_result = $tagChannel->getChannel($typeid, "' . $type . '", "' . $currentstyle . '", ' . $notypeid . ');';
            $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,' . $offset . ', $row, true) : $_result->slice(' . $offset . ', $row, true); ';
            } else {
                $parseStr .= ' if(intval($row) > 0) :';
                $parseStr .= ' $__LIST__ = is_array($_result) ? array_slice($_result,' . $offset . ', $row, true) : $_result->slice(' . $offset . ', $row, true); ';
                $parseStr .= ' else:';
                $parseStr .= ' $__LIST__ = $_result;';
                $parseStr .= ' endif;';
            }
        }

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $id . '["typename"] = text_msubstr($' . $id . '["typename"], 0, ' . $titlelen . ', false);';

        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * type标签解析 指定的单个栏目的链接
     * 格式：
     * {fly:type typeid='' empty=''}
     *  <a href="{$field:typeurl}">{$field:title}</a>
     * {/fly:type}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagType($tag, $content)
    {
        $typeid_tmp = isset($tag['typeid']) ? $tag['typeid'] : '0';
        $typeid = $this->varOrvalue($typeid_tmp);

        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $addfields = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields = $this->varOrvalue($addfields);

        $joinaid = isset($tag['joinaid']) ? $tag['joinaid'] : '';
        $joinaid = $this->varOrvalue($joinaid);

        $parseStr = '<?php ';
        // 声明变量
        if (!empty($typeid_tmp)) {
            $parseStr .= ' $typeid = ' . $typeid . ';';
        } else {
            $parseStr .= ' if(!isset($typeid) || empty($typeid)) : $typeid = ' . $typeid . '; endif;';
        }

        $parseStr .= ' $tagType = new \app\index\taglib\TagType;';
        $parseStr .= ' $_result = $tagType->getType($typeid, ' . $addfields . ',' . $joinaid . ');';
        $parseStr .= ' ?>';

        /*方式一*/
        $parseStr .= '<?php if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= '$' . $id . ' = $__LIST__;';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php unset($typeid); ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用
        /*--end*/

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * global 标签解析
     * 在模板中获取系统的变量值
     * 格式： {fly:global name="" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagGlobal($tag)
    {
        $name = $tag['name'];
        $name = $this->varOrvalue($name);
        $parseStr = '<?php ';
        // 查询数据库获取的数据集
        $parseStr .= ' $tagGlobal  = new \app\index\taglib\TagGlobal;';
        $parseStr .= ' $__VALUE__ = $tagGlobal->getGlobal(' . $name . ');';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * arclist标签解析 获取指定文档列表（兼容tp的volist标签语法）
     * 格式：
     * {fly:arclist channelid='1' typeid='1' row='10' offset='0' titlelen='30' orderby ='aid desc' flag='' infolen='160' empty='' id='field' mod='' name=''}
     *  {$field.title}
     *  {$field.typeid}
     * {/fly:arclist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagArclist($tag, $content)
    {
        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $notypeid = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid = $this->varOrvalue($notypeid);

        $typeid2 = !empty($tag['typeid2']) ? $tag['typeid2'] : '';
        $typeid2 = $this->varOrvalue($typeid2);

        $cityid = !empty($tag['cityid']) ? $tag['cityid'] : '';
        $cityid = $this->varOrvalue($cityid);

        $channelid = isset($tag['channelid']) ? $tag['channelid'] : '';
        $channelid = $this->varOrvalue($channelid);

        $addfields = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields = $this->varOrvalue($addfields);

        $joinaid = isset($tag['joinaid']) ? $tag['joinaid'] : '';
        $joinaid = $this->varOrvalue($joinaid);

        $name = !empty($tag['name']) ? $tag['name'] : '';
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $orderby = isset($tag['orderby']) ? $tag['orderby'] : '';
        if (isset($tag['orderWay'])) {
            $orderway = $tag['orderway'];
        } else {
            $orderway = isset($tag['orderway']) ? $tag['orderway'] : 'desc';
        }
        $flag = isset($tag['flag']) ? $tag['flag'] : '';
        $noflag = isset($tag['noflag']) ? $tag['noflag'] : '';
        $tagid = isset($tag['tagid']) ? $tag['tagid'] : ''; // 标签ID
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 0;
        $thumb = !empty($tag['thumb']) ? $tag['thumb'] : 'on';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }

        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' if(isset($ui_typeid) && !empty($ui_typeid)) : $typeid = $ui_typeid; else: $typeid = ' . $typeid . '; endif;';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = ' . $row . '; endif;';
        $parseStr .= ' $channelid = ' . $channelid . ';';

        if ($name) { // 从模板中传入数据集
            $symbol = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }

            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $param = array(';
            $parseStr .= '      "typeid"=> $typeid,';
            $parseStr .= '      "typeid2"=> ' . $typeid2 . ',';
            $parseStr .= '      "notypeid"=> ' . $notypeid . ',';
            $parseStr .= '      "cityid"=> ' . $cityid . ',';
            $parseStr .= '      "flag"=> "' . $flag . '",';
            $parseStr .= '      "noflag"=> "' . $noflag . '",';
            $parseStr .= '      "channelid"=> $channelid,';
            $parseStr .= '      "joinaid"=> ' . $joinaid . ',';
            $parseStr .= ' );';
            $parseStr .= ' $tag = ' . var_export($tag, true) . ';';
            $parseStr .= ' $tagArclist = new \app\index\taglib\TagArclist;';
            $parseStr .= ' $_result = $tagArclist->getArclist($param, $row, "' . $orderby . '", ' . $addfields . ',"' . $orderway . '","' . $tagid . '",$tag,"' . $pagesize . '","' . $thumb . '");';

            $parseStr .= 'if(is_array($_result["list"]) || $_result["list"] instanceof \think\Collection || $_result["list"] instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= ' $__LIST__ = is_array($_result["list"]) ? array_slice($_result["list"],' . $offset . ', $row, true) : $_result["list"]->slice(' . $offset . ', $row, true); ';
            } else {
                $parseStr .= ' $__LIST__ = $_result["list"];';
            }
            $parseStr .= ' $__TAG__ = $_result["tag"];';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $' . $id . '["id"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        $parseStr .= '$' . $id . '["description"] = text_msubstr($' . $id . '["description"], 0, ' . $infolen . ', true);';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * arcextlist标签解析 获取指定文档扩展列表（兼容tp的volist标签语法）
     * 格式：
     * {fly:arclist channelid='1' typeid='1' row='10' offset='0' titlelen='30' orderby ='aid desc'  infolen='160' empty='' id='field' mod='' name=''}
     *  {$field.title}
     *  {$field.typeid}
     * {/fly:arclist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagArcextlist($tag, $content)
    {
        $eid = !empty($tag['eid']) ? $tag['eid'] : '';
        $eid = $this->varOrvalue($eid);

        $aid = !empty($tag['aid']) ? $tag['aid'] : '';
        $aid = $this->varOrvalue($aid);

        $name = !empty($tag['name']) ? $tag['name'] : '';
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $orderby = isset($tag['orderby']) ? $tag['orderby'] : '';
        if (isset($tag['orderWay'])) {
            $orderway = $tag['orderWay'];
        } else {
            $orderway = isset($tag['orderway']) ? $tag['orderway'] : 'desc';
        }
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 0;
        $thumb = !empty($tag['thumb']) ? $tag['thumb'] : 'on';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }
        $parseStr = '<?php ';
        // 声明变量
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = ' . $row . '; endif;';

        if ($name) { // 从模板中传入数据集
            $symbol = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }
            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $param = array(';
            $parseStr .= '      "aid"=> '.$aid.',';
            $parseStr .= '      "eid"=> ' . $eid . ',';
            $parseStr .= ' );';
            $parseStr .= ' $tag = ' . var_export($tag, true) . ';';
            $parseStr .= ' $tagArcextlist = new \app\index\taglib\TagArcextlist;';
            $parseStr .= ' $_result = $tagArcextlist->getArcextlist($param, $row, "' . $orderby . '", "' . $orderway . '","' . $pagesize . '","' . $thumb . '");';

            $parseStr .= 'if(is_array($_result["list"]) || $_result["list"] instanceof \think\Collection || $_result["list"] instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= ' $__LIST__ = is_array($_result["list"]) ? array_slice($_result["list"],' . $offset . ', $row, true) : $_result["list"]->slice(' . $offset . ', $row, true); ';
            } else {
                $parseStr .= ' $__LIST__ = $_result["list"];';
            }
            $parseStr .= ' $__TAG__ = $_result["tag"];';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $' . $id . '["id"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        $parseStr .= '$' . $id . '["content"] = text_msubstr($' . $id . '["content"], 0, ' . $infolen . ', true);';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * arcextlist标签解析 获取指定文档扩展列表（兼容tp的volist标签语法）
     * 格式：
     * {fly:arclist channelid='1' typeid='1' row='10' offset='0' titlelen='30' orderby ='aid desc'  infolen='160' empty='' id='field' mod='' name=''}
     *  {$field.title}
     *  {$field.typeid}
     * {/fly:arclist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagArcsublist($tag, $content)
    {

        $aid = !empty($tag['aid']) ? $tag['aid'] : '';
        $aid = $this->varOrvalue($aid);

        $linkfield = !empty($tag['linkfield']) ? $tag['linkfield'] : '';
        $linkfield = $this->varOrvalue($linkfield);

        $linkreg = !empty($tag['linkreg']) ? $tag['linkreg'] : '';
        $linkreg = $this->varOrvalue($linkreg);

        $linkvalue = !empty($tag['linkvalue']) ? $tag['linkvalue'] : '';
        $linkvalue = $this->varOrvalue($linkvalue);

        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $notypeid = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid = $this->varOrvalue($notypeid);

        $channelid = isset($tag['channelid']) ? $tag['channelid'] : '';
        $channelid = $this->varOrvalue($channelid);

        $name = !empty($tag['name']) ? $tag['name'] : '';
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $orderby = isset($tag['orderby']) ? $tag['orderby'] : '';
        if (isset($tag['orderWay'])) {
            $orderway = $tag['orderWay'];
        } else {
            $orderway = isset($tag['orderway']) ? $tag['orderway'] : 'desc';
        }
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 0;
        $thumb = !empty($tag['thumb']) ? $tag['thumb'] : 'on';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }
        $parseStr = '<?php ';
        // 声明变量
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = ' . $row . '; endif;';

        if ($name) { // 从模板中传入数据集
            $symbol = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }
            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $param = array(';
            $parseStr .= '      "aid"=> '.$aid.',';
            $parseStr .= '      "typeid"=> '.$typeid.',';
            $parseStr .= '      "channelid"=> '.$channelid.',';
            $parseStr .= '      "notypeid"=> '.$notypeid.',';
            $parseStr .= '      "linkfield"=> ' . $linkfield . ',';
            $parseStr .= '      "linkreg"=> ' . $linkreg . ',';
            $parseStr .= '      "linkvalue"=> ' . $linkvalue . ',';
            $parseStr .= ' );';
            $parseStr .= ' $tag = ' . var_export($tag, true) . ';';
            $parseStr .= ' $tagArcsublist = new \app\index\taglib\TagArcsublist;';
            $parseStr .= ' $_result = $tagArcsublist->getArcsublist($param, $row, "' . $orderby . '", "' . $orderway . '","' . $pagesize . '","' . $thumb . '");';

            $parseStr .= 'if(is_array($_result["list"]) || $_result["list"] instanceof \think\Collection || $_result["list"] instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= ' $__LIST__ = is_array($_result["list"]) ? array_slice($_result["list"],' . $offset . ', $row, true) : $_result["list"]->slice(' . $offset . ', $row, true); ';
            } else {
                $parseStr .= ' $__LIST__ = $_result["list"];';
            }
            $parseStr .= ' $__TAG__ = $_result["tag"];';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $' . $id . '["id"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        $parseStr .= '$' . $id . '["description"] = text_msubstr($' . $id . '["description"], 0, ' . $infolen . ', true);';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


	/**
	 * booklist标签解析 获取指定文档列表（兼容tp的volist标签语法）
	 * 格式：
	 * {fly:arclist typeid='1' row='10' offset='0' titlelen='30' orderby ='aid desc' flag='' infolen='160' empty='' id='field' mod='' name=''}
	 *  {$field.title}
	 *  {$field.typeid}
	 * {/fly:arclist}
	 * @access public
	 * @param array $tag 标签属性
	 * @param string $content 标签内容
	 * @return string|void
	 */
	public function tagBooklist($tag, $content)
	{

		$typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
		$typeid = $this->varOrvalue($typeid);
		$notypeid = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
		$notypeid = $this->varOrvalue($notypeid);

		$name = !empty($tag['name']) ? $tag['name'] : '';
		$id = isset($tag['id']) ? $tag['id'] : 'field';
		$key = !empty($tag['key']) ? $tag['key'] : 'i';
		$empty = isset($tag['empty']) ? $tag['empty'] : '';
		$empty = htmlspecialchars($empty);
		$mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
		$orderby = isset($tag['orderby']) ? $tag['orderby'] : '';
		if (isset($tag['orderWay'])) {
			$orderway = $tag['orderway'];
		} else {
			$orderway = isset($tag['orderway']) ? $tag['orderway'] : 'desc';
		}
		$flag = isset($tag['flag']) ? $tag['flag'] : '';
		$noflag = isset($tag['noflag']) ? $tag['noflag'] : '';
		$tagid = isset($tag['tagid']) ? $tag['tagid'] : ''; // 标签ID

		$pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 0;

		$titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
		$infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;
		$offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
		$row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
		if (!empty($tag['limit'])) {
			$limitArr = explode(',', $tag['limit']);
			$offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
			$row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
		}

		$parseStr = '<?php ';

		$parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = ' . $row . '; endif;';

		if ($name) { // 从模板中传入数据集
			$symbol = substr($name, 0, 1);
			if (':' == $symbol) {
				$name = $this->autoBuildVar($name);
				$parseStr .= '$_result=' . $name . ';';
				$name = '$_result';
			} else {
				$name = $this->autoBuildVar($name);
			}

			$parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
			// 设置了输出数组长度
			if (0 != $offset || 'null' != $row) {
				$parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
			} else {
				$parseStr .= ' $__LIST__ = ' . $name . ';';
			}

		} else { // 查询数据库获取的数据集
			$parseStr .= ' $param = array(';
			$parseStr .= '      "typeid"=> '.$typeid.',';
			$parseStr .= '      "notypeid"=> ' . $notypeid . ',';
			$parseStr .= '      "flag"=> "' . $flag . '",';
			$parseStr .= '      "noflag"=> "' . $noflag . '",';
			$parseStr .= '      "pagesize"=> "' . $pagesize . '",';
			$parseStr .= ' );';
			$parseStr .= ' $tag = ' . var_export($tag, true) . ';';
			$parseStr .= ' $tagBooklist = new \app\index\taglib\TagBooklist;';
			$parseStr .= ' $_result = $tagBooklist->getBooklist($param, "'.$row.'", "' . $orderby . '","' . $orderway . '","' . $tagid . '",$tag);';

			$parseStr .= 'if(is_array($_result["list"]) || $_result["list"] instanceof \think\Collection || $_result["list"] instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
			// 设置了输出数组长度
			if (0 != $offset || 'null' != $row) {
				$parseStr .= ' $__LIST__ = is_array($_result["list"]) ? array_slice($_result["list"],' . $offset . ', '.$row.', true) : $_result["list"]->slice(' . $offset . ', $row, true); ';
			} else {
				$parseStr .= ' $__LIST__ = $_result["list"];';
			}
            $parseStr .= ' $__PAGES__ = $_result["list"];';//返回查询对象，供分页标签调用
			$parseStr .= ' $__TAG__ = $_result["tag"];';
		}
		$parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
		$parseStr .= 'else: ';
		$parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
		$parseStr .= '$aid = $' . $id . '["id"];';
		$parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
		$parseStr .= '$' . $id . '["description"] = text_msubstr($' . $id . '["description"], 0, ' . $infolen . ', true);';

		$parseStr .= '$' . $key . '= intval($key) + 1;?>';
		$parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
		$parseStr .= $content;
		$parseStr .= '<?php ++$e; ?>';
		$parseStr .= '<?php $aid = 0; ?>';
		$parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
		$parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

		if (!empty($parseStr)) {
			return $parseStr;
		}
		return;
	}

    /**
     * arcview标签解析 指定的单个栏目的链接
     * 格式：
     * {fly:arcview aid='' empty=''}
     *  <a href="{$field:arcurl}">{$field:title}</a>
     * {/fly:arcview}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagArcview($tag, $content)
    {
        $aid_tmp = isset($tag['aid']) ? $tag['aid'] : '0';
        $aid = $this->varOrvalue($aid_tmp);

        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $addfields = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields = $this->varOrvalue($addfields);

        $joinaid = isset($tag['joinaid']) ? $tag['joinaid'] : '';
        $joinaid = $this->varOrvalue($joinaid);

        $parseStr = '<?php ';
        // 声明变量
        if (!empty($aid_tmp)) {
            $parseStr .= ' $aid = ' . $aid . ';';
        } else {
            $parseStr .= ' if(!isset($aid) || empty($aid)) : $aid = ' . $aid . '; endif;';
        }

        $parseStr .= ' $tagArcview = new \app\index\taglib\TagArcview;';
        $parseStr .= ' $_result = $tagArcview->getArcview($aid, ' . $addfields . ',' . $joinaid . ');';
        $parseStr .= ' ?>';

        /*方式一*/
        $parseStr .= '<?php if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= '$' . $id . ' = $__LIST__;';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php unset($aid); ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用
        /*--end*/

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * likearticle标签解析 获取指定相关文档列表
     * 格式：
     * {fly:likearticle mytypeid='0' limit='0,10' titlelen='30' infolen='160' id='field'}
     * <a href="{$field:arcurl}">{$field:title}</a>
     * {/fly:likearticle}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagLikearticle($tag, $content)
    {
        $channelid = !empty($tag['channelid']) ? intval($tag['channelid']) : '';
        $channelid = $this->varOrvalue($channelid);

        $mytypeid = !empty($tag['mytypeid']) ? $tag['mytypeid'] : '';
        $mytypeid = $this->varOrvalue($mytypeid);

        if (empty($tag['mytypeid'])) {
            $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
            $mytypeid = $this->varOrvalue($typeid);
        }
        $name    = !empty($tag['name']) ? $tag['name'] : '';
        $id      = isset($tag['id']) ? $tag['id'] : 'field';
        $key     = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $empty   = htmlspecialchars($empty);
        $mod     = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen  = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;
        $row      = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 12;
        $limit    = !empty($tag['limit']) ? $tag['limit'] : '';
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }
        $orderby = isset($tag['orderby']) ? $tag['orderby'] : '';
        if (isset($tag['orderway'])) {
            $orderway = $tag['orderway'];
        } else {
            $orderway = isset($tag['orderway']) ? $tag['orderway'] : 'desc';
        }

        $parseStr = '<?php ';
        if ($name) { // 从模板中传入数据集
            $symbol = substr($name, 0, 1);
            if (':' == $symbol) {
                $name     = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name     = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }

            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection : $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if ( 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $row . ', true) : ' . $name . '->slice(' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $tagLikearticle = new \app\index\taglib\TagLikearticle;';
            $parseStr .= ' $_result = $tagLikearticle->getLikearticle('.$channelid.','.$mytypeid.', "'.$orderby.'", "'.$orderway.'", "'.$limit.'");';
            $parseStr .= 'if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            $parseStr .= ' $__LIST__ = $_result;';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $' . $id . '["id"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        $parseStr .= '$' . $id . '["description"] = text_msubstr($' . $id . '["description"], 0, ' . $infolen . ', true);';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * prenext 标签解析
     * 在模板中获取内容页的上下篇
     * 格式：
     * {fly:prenext get='pre'}
     *  <a href="{$field:arcurl}">{$field:title}</a>
     * {/fly:prenext}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPrenext($tag, $content)
    {
        $get  =  !empty($tag['get']) ? $tag['get'] : 'pre';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        if (isset($tag['empty'])) {
            $style = 1; // 第一种默认标签写法，带属性empty
        } else {
            $style = 2; // 第二种支持判断写法，可以 else
        }


        if (1 == $style) {
//            $empty     = isset($tag['empty']) ? $tag['empty'] : '暂无';
//            $empty  = htmlspecialchars($empty);
//            $parseStr = '<?php ';
//            $parseStr .= ' $tagPrenext = new \app\index\taglib\TagPrenext;';
//            $parseStr .= ' $_result = $tagPrenext->getPrenext("'.$get.'");';
//            $parseStr .= 'if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
//            $parseStr .= ' $__LIST__ = $_result;';
//            $parseStr .= 'if( empty($__LIST__) ) : echo htmlspecialchars_decode("' . $empty . '");';
//            $parseStr .= 'else: ';
//            $parseStr .= '$'.$id.' = $__LIST__;';
//            $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';
//
/*            $parseStr .= '?>';*/
//            $parseStr .= $content;
/*            $parseStr .= '<?php endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';*/

        } else {

            $parseStr = '<?php ';
            $parseStr .= ' $tagPrenext = new \app\index\taglib\TagPrenext;';
            $parseStr .= ' $_result = $tagPrenext->getPrenext("'.$get.'");';
            $parseStr .= '?>';

            $parseStr .= '<?php if(!empty($_result) || (($_result instanceof \think\Collection || $_result instanceof \think\Paginator ) && $_result->isEmpty())): ?>';
            $parseStr .= '<?php $'.$id.' = $_result; ?>';
            $parseStr .= '<?php $' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false); ?>';
            $parseStr .= $content;
            $parseStr .= '<?php endif; ?>';
        }

        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * list 标签解析 获取指定文档分页列表（兼容tp的volist标签语法）
     * 格式：
     * {fly:list channelid='1' typeid='1' row='10' titlelen='30' orderby ='aid desc' flag='' infolen='160' empty='' id='field' mod='' name=''}
     *  {$field.title}
     *  {$field.typeid}
     * {/fly:list}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagList($tag, $content)
    {
        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $notypeid = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid = $this->varOrvalue($notypeid);

        $cityid = isset($tag['cityid']) ? $tag['cityid'] : '';
        $cityid = $this->varOrvalue($cityid);

        $channelid = isset($tag['channelid']) ? $tag['channelid'] : '';
        $channelid = $this->varOrvalue($channelid);

        $channelexttable = isset($tag['channelexttable']) ? $tag['channelexttable'] : '';
        $channelexttable = $this->varOrvalue($channelexttable);

        $addfields = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields = $this->varOrvalue($addfields);

        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 10;
        $thumb = !empty($tag['thumb']) ? $tag['thumb'] : 'on';
        $orderby = isset($tag['orderby']) ? $tag['orderby'] : '';
        if (isset($tag['orderway'])) {
            $orderway = $tag['orderway'];
        } else {
            $orderway = isset($tag['orderway']) ? $tag['orderway'] : 'desc';
        }
        $flag = isset($tag['flag']) ? $tag['flag'] : '';
        $noflag = isset($tag['noflag']) ? $tag['noflag'] : '';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;

        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = ' . $typeid . '; ';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $param = array(';
        $parseStr .= '      "typeid"=> $typeid,';
        $parseStr .= '      "notypeid"=> ' . $notypeid . ',';
        $parseStr .= '      "cityid"=> ' . $cityid . ',';
        $parseStr .= '      "flag"=> "' . $flag . '",';
        $parseStr .= '      "noflag"=> "' . $noflag . '",';
        $parseStr .= '      "channelid"=> ' . $channelid . ',';
        $parseStr .= '      "channelexttable"=> ' . $channelexttable . ',';
        $parseStr .= ' );';
        // $parseStr .= ' $orderby = "'.$orderby.'";';
        $parseStr .= ' $tagList = new \app\index\taglib\TagList;';
        $parseStr .= ' $_result_tmp = $tagList->getList($param, ' . $pagesize . ', "' . $orderby . '", ' . $addfields . ', "' . $orderway . '", "' . $thumb . '");';

        $parseStr .= 'if(is_array($_result_tmp) || $_result_tmp instanceof \think\Collection || $_result_tmp instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result = $_result_tmp["list"];';
        $parseStr .= ' $__PAGES__ = $_result_tmp["pages"];';//返回查询对象，供分页标签调用

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $' . $id . '["id"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        $parseStr .= '$' . $id . '["description"] = text_msubstr($' . $id . '["description"], 0, ' . $infolen . ', true);';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * pagelist 标签解析
     * 在模板中获取列表的分页
     * 格式： {fly:pagelist listitem='info,index,end,pre,next,pageno' listsize='2'/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPagelist($tag)
    {
        $listitem = !empty($tag['listitem']) ? $tag['listitem'] : '';
        $listsize = !empty($tag['listsize']) ? intval($tag['listsize']) : '';

        $parseStr = ' <?php ';
        $parseStr .= ' $__PAGES__ = isset($__PAGES__) ? $__PAGES__ : "";';
        $parseStr .= ' $tagPagelist = new \app\index\taglib\TagPagelist;';
        $parseStr .= ' $__VALUE__ = $tagPagelist->getPagelist($__PAGES__, "' . $listitem . '", "' . $listsize . '");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }

    /**
     * position 标签解析
     * 在模板中获取列表的分页
     * 格式： {fly:position typeid="" symbol=" > "/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPosition($tag)
    {
        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $symbol = isset($tag['symbol']) ? $tag['symbol'] : '';
        $style = !empty($tag['style']) ? $tag['style'] : '';

        $parseStr = ' <?php ';

        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = ' . $typeid . ';';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/

        $parseStr .= ' $tagPosition = new \app\index\taglib\TagPosition;';
        $parseStr .= ' $__VALUE__ = $tagPosition->getPosition($typeid, "' . $symbol . '", "' . $style . '");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }


    /**
     * arcsubcount 标签解析
     * 在模板中获取列表的分页
     * 格式： {fly:arcsubcount channelid=''  linkfield='' linkreg='' linkvalue=''  typeid="" > "/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagArcsubcount($tag)
    {
        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $channelid = !empty($tag['channelid']) ? $tag['channelid'] : '';
        $channelid = $this->varOrvalue($channelid);

        $linkfield = !empty($tag['linkfield']) ? $tag['linkfield'] : '';
        $linkfield = $this->varOrvalue($linkfield);

        $linkreg = !empty($tag['linkreg']) ? $tag['linkreg'] : '';
        $linkreg = $this->varOrvalue($linkreg);

        $linkvalue = !empty($tag['linkvalue']) ? $tag['linkvalue'] : '';
        $linkvalue = $this->varOrvalue($linkvalue);

        $parseStr = ' <?php ';
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        //$parseStr .= ' $typeid = ' . $typeid . ';';
//        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $param = array(';
        $parseStr .= '      "typeid"=> '.$typeid.',';
        $parseStr .= '      "channelid"=> ' . $channelid . ',';
        $parseStr .= '      "linkfield"=> ' . $linkfield . ',';
        $parseStr .= '      "linkreg"=> ' . $linkreg . ',';
        $parseStr .= '      "linkvalue"=> ' . $linkvalue . ',';
        $parseStr .= ' );';

        $parseStr .= ' $tagArcsubcount = new \app\index\taglib\TagArcsubcount;';
        $parseStr .= ' $__VALUE__ = $tagArcsubcount->getArcsubcount($param);';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }


    /**
     * ads标签解析 指定的单个广告的信息
     * 格式：
     * {fly:ads aid=''}
     *  <a href="{$field:links}">{$field:title}</a>
     * {/fly:ads}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagAds($tag, $content)
    {


        $aid = isset($tag['aid']) ? $tag['aid'] : '0';
        $aid = $this->varOrvalue($aid);
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $row = !empty($tag['row']) ? $tag['row'] : '10';
        $key = !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $currentstyle = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagAds = new \app\index\taglib\TagAds;';
        $parseStr .= ' $_result = $tagAds->getAds(' . $aid . ');';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if ('null' != $row) {
            $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,0, ' . $row . ', true) : $_result->slice(0, ' . $row . ', true); ';
        } else {
            $parseStr .= ' $__LIST__ = $_result;';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';

        $parseStr .= ' if ($' . $key . ' == 0) :';
        $parseStr .= ' $' . $id . '["currentstyle"] = "' . $currentstyle . '";';
        $parseStr .= ' else: ';
        $parseStr .= ' $' . $id . '["currentstyle"] = "";';
        $parseStr .= ' endif;';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * adslist 广告标签
     * 在模板中给某个变量赋值 支持变量赋值
     * 格式：
     * {fly:adslist pid='' limit=''}
     *  <a href="{$field:links}" {$field.target}>{$field:title}</a>
     * {/fly:adslist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagAdslist($tag, $content)
    {
        $adsid = !empty($tag['adsid']) ? $tag['adsid'] : '0';// 返回的变量pid
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $orderby = !empty($tag['orderby']) ? $tag['orderby'] : ''; //排序
        $row = !empty($tag['row']) ? $tag['row'] : '10';
        $key = !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $currentstyle = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';

        $param = ['adsid' => $adsid, 'orderby' => $orderby];

        $parseStr = '<?php ';
        // 查询数据库获取的数据集
        $parseStr .= ' $tagAdslist = new \app\index\taglib\TagAdslist;';
        $parseStr .= ' $_result = $tagAdslist->getAdslist("' . $adsid . '","' . $orderby . '");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if ('null' != $row) {
            $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,0, ' . $row . ', true) : $_result->slice(0, ' . $row . ', true); ';
        } else {
            $parseStr .= ' $__LIST__ = $_result;';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';

        $parseStr .= ' if ($' . $key . ' == 0) :';
        $parseStr .= ' $' . $id . '["currentstyle"] = "' . $currentstyle . '";';
        $parseStr .= ' else: ';
        $parseStr .= ' $' . $id . '["currentstyle"] = "";';
        $parseStr .= ' endif;';

        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * flink 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {fly:flink row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li>
     * {/fly:fly}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagFlink($tag, $content)
    {
        $type = !empty($tag['type']) ? $tag['type'] : 'text';
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $row = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit = !empty($tag['limit']) ? $tag['limit'] : '20';
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagFlink = new \app\index\taglib\TagFlink;';
        $parseStr .= ' $_result = $tagFlink->getFlink("' . $type . '", "' . $limit . '");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * searchurl 标签解析
     * 在模板中获取搜索的URL
     * 格式： {fly:searchurl /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagSearchurl($tag)
    {
        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagSearchurl = new \app\index\taglib\TagSearchurl;';
        $parseStr .= ' $__VALUE__ = $tagSearchurl->getSearchurl();';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= '?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * searchform 搜索表单标签解析 TAG调用
     * {fly:searchform type='default'}
     * {$field.searchurl}
     * {/fly:searchform}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSearchform($tag, $content)
    {
        $channelid   = !empty($tag['channelid']) ? $tag['channelid'] : '';
        if (empty($channelid)) {
            $channelid   = !empty($tag['channel']) ? $tag['channel'] : '';
        }
        $channelid  = $this->varOrvalue($channelid);
        $typeid   = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);
        $notypeid   = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid  = $this->varOrvalue($notypeid);
        $flag   = !empty($tag['flag']) ? $tag['flag'] : '';
        $flag  = $this->varOrvalue($flag);
        $noflag   = !empty($tag['noflag']) ? $tag['noflag'] : '';
        $noflag  = $this->varOrvalue($noflag);
        $type   = !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);




        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagSearchform = new \app\index\taglib\TagSearchform;';
        $parseStr .= ' $_result = $tagSearchform->getSearchform('.$typeid.','.$channelid.','.$notypeid.','.$flag.','.$noflag.');';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        //$parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, ' . $titlelen . ', false);';
        //$parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }



    /**
     * tag 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {fly:tag row='1' getall='0' sort=''}
     *  <li><a href='{$field.link}'>{$field.tag}</a> </li>
     * {/fly:tag}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagTag($tag, $content)
    {
        $aid   = !empty($tag['aid']) ? $tag['aid'] : '0';
        $aid  = $this->varOrvalue($aid);
        $typeid   = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);
        $getall   = !empty($tag['getall']) ? $tag['getall'] : '0';
        $name   = !empty($tag['name']) ? $tag['name'] : '';
        $style   = !empty($tag['style']) ? $tag['style'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 100;
        $sort   = !empty($tag['sort']) ? $tag['sort'] : 'new';
        $type  = !empty($tag['type']) ? $tag['type'] : '';

        $parseStr = '<?php ';

        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = '.$typeid.';';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        // 声明变量
        $parseStr .= ' if(!isset($aid) || empty($aid)) : $aid = '.$aid.'; endif;';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $tagTag = new \app\index\taglib\TagTag;';
        $parseStr .= ' $_result = $tagTag->getTag('.$getall.', $typeid, $aid, '.$row.', "'.$sort.'", "'.$type.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if ('null' != $row) {
            $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,0, '.$row.', true) : $_result->slice(0, '.$row.', true); ';
        } else {
            $parseStr .= ' $__LIST__ = $_result;';
        }

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php unset($aid); ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }



    /**
     * guestbookform 留言表单标签解析 TAG调用
     * {fly:guestbookform type='default'}
     * {$field.value}
     * {/fly:guestbookform}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagGuestbookform($tag, $content)
    {
        $tid   = !empty($tag['tid']) ? $tag['tid'] : '';
        $tid  = $this->varOrvalue($tid);
        $type   = !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $addfield     = !empty($tag['addfield']) ? $tag['addfield'] : '';

        $parseStr = '<?php ';

        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $tid = '.$tid.';';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $tagGuestbookform = new \app\index\taglib\TagGuestbookform;';
        $parseStr .= ' $_result = $tagGuestbookform->getGuestbookform($tid, "'.$type.'", "'.$addfield.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach;';
        $parseStr .= 'endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * empty标签解析
     * 如果某个变量为empty 则输出内容
     * 格式： {fly:empty name="" }content{/fly:empty}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagEmpty($tag, $content)
    {
        $name = $tag['name'];
        $name = $this->autoBuildVar($name);
        $parseStr = '<?php if(empty(' . $name . ') || ((' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator ) && ' . $name . '->isEmpty())): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * notempty 标签解析
     * 如果某个变量不为empty 则输出内容
     * 格式： {fly:notempty name="" }content{/fly:notempty}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagNotempty($tag, $content)
    {
        $name = $tag['name'];
        $name = $this->autoBuildVar($name);
        $parseStr = '<?php if(!(empty(' . $name . ') || ((' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator ) && ' . $name . '->isEmpty()))): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * assign标签解析
     * 在模板中给某个变量赋值 支持变量赋值
     * 格式： {fly:assign name="" value="" /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagAssign($tag, $content)
    {
        $name = $this->autoBuildVar($tag['name']);
        $flag = substr($tag['value'], 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($tag['value']);
        } else {
            $value = '\'' . $tag['value'] . '\'';
        }
        $parseStr = '<?php ' . $name . ' = ' . $value . '; ?>';
        return $parseStr;
    }

    /**
     * foreach标签解析 循环输出数据集
     * 格式：
     * {fly:foreach name="userList" id="user" key="key" index="i" mod="2" offset="3" length="5" empty=""}
     * {user.username}
     * {/fly:foreach}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagForeach($tag, $content)
    {
        // 直接使用表达式
        if (!empty($tag['expression'])) {
            $expression = ltrim(rtrim($tag['expression'], ')'), '(');
            $expression = $this->autoBuildVar($expression);
            $parseStr = '<?php foreach(' . $expression . '): ?>';
            $parseStr .= $content;
            $parseStr .= '<?php endforeach; ?>';
            return $parseStr;
        }
        $name = $tag['name'];
        $key = !empty($tag['key']) ? $tag['key'] : 'key';
        $item = !empty($tag['id']) ? $tag['id'] : $tag['item'];
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $length = !empty($tag['length']) && is_numeric($tag['length']) ? intval($tag['length']) : 'null';

        $parseStr = '<?php ';
        // 支持用函数传数组
        if (':' == substr($name, 0, 1)) {
            $var = '$_' . uniqid();
            $name = $this->autoBuildVar($name);
            $parseStr .= $var . '=' . $name . '; ';
            $name = $var;
        } else {
            $name = $this->autoBuildVar($name);
        }
        $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): ';
        // 设置了输出数组长度
        if (0 != $offset || 'null' != $length) {
            if (!isset($var)) {
                $var = '$_' . uniqid();
            }
            $parseStr .= $var . ' = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $length . ', true) : ' . $name . '->slice(' . $offset . ',' . $length . ', true); ';
        } else {
            $var = &$name;
        }

        $parseStr .= 'if( count(' . $var . ')==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';

        // 设置了索引项
        if (isset($tag['index'])) {
            $index = $tag['index'];
            $parseStr .= '$' . $index . '=0; $e = 1;';
        }
        $parseStr .= 'foreach(' . $var . ' as $' . $key . '=>$' . $item . '): ';
        // 设置了索引项
        if (isset($tag['index'])) {
            $index = $tag['index'];
            if (!empty($tag['mod']) && is_numeric($tag['mod'])) {
                $mod = (int)$tag['mod'];
                $parseStr .= '$mod = ($e % ' . $mod . '); ';
            }
            $parseStr .= '++$' . $index . ';';
        }
        $parseStr .= '?>';
        // 循环体中的内容
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * if标签解析
     * 格式：
     * {fly:if condition=" $a eq 1"}
     * {fly:elseif condition="$a eq 2" /}
     * {fly:else /}
     * {/fly:if}
     * 表达式支持 eq neq gt egt lt elt == > >= < <= or and || &&
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagIf($tag, $content)
    {
        $condition = !empty($tag['expression']) ? $tag['expression'] : $tag['condition'];
        $condition = $this->parseCondition($condition);
        $parseStr = '<?php if(' . $condition . '): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * elseif标签解析
     * 格式：见if标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagElseif($tag, $content)
    {
        $condition = !empty($tag['expression']) ? $tag['expression'] : $tag['condition'];
        $condition = $this->parseCondition($condition);
        $parseStr = '<?php elseif(' . $condition . '): ?>';
        return $parseStr;
    }

    /**
     * else 标签解析
     * 格式：见if标签
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagElse($tag)
    {
        $parseStr = '<?php else: ?>';
        return $parseStr;
    }

    /**
     * switch标签解析
     * 格式：
     * {fly:switch name="a.name"}
     * {fly:case value="1" break="false"}1{/case}
     * {fly:case value="2" }2{/case}
     * {fly:default /}other
     * {/fly:switch}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagSwitch($tag, $content)
    {
        $name = !empty($tag['expression']) ? $tag['expression'] : $tag['name'];
        $name = $this->autoBuildVar($name);
        $parseStr = '<?php switch(' . $name . '): ?>' . $content . '<?php endswitch; ?>';
        return $parseStr;
    }

    /**
     * case标签解析 需要配合switch才有效
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagCase($tag, $content)
    {
        $value = !empty($tag['expression']) ? $tag['expression'] : $tag['value'];
        $flag = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
            $value = 'case ' . $value . ':';
        } elseif (strpos($value, '|')) {
            $values = explode('|', $value);
            $value = '';
            foreach ($values as $val) {
                $value .= 'case "' . addslashes($val) . '":';
            }
        } else {
            $value = 'case "' . $value . '":';
        }
        $parseStr = '<?php ' . $value . ' ?>' . $content;
        $isBreak = isset($tag['break']) ? $tag['break'] : '';
        if ('' == $isBreak || $isBreak) {
            $parseStr .= '<?php break; ?>';
        }
        return $parseStr;
    }

    /**
     * default标签解析 需要配合switch才有效
     * 使用： {fly:default /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagDefault($tag)
    {
        $parseStr = '<?php default: ?>';
        return $parseStr;
    }

    /**
     * compare标签解析
     * 用于值的比较 支持 eq neq gt lt egt elt heq nheq 默认是eq
     * 格式： {fly:compare name="" type="eq" value="" }content{/fly:compare}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagCompare($tag, $content)
    {
        $name = $tag['name'];
        $value = $tag['value'];
        $type = isset($tag['type']) ? $tag['type'] : 'eq'; // 比较类型
        $name = $this->autoBuildVar($name);
        $flag = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
        } else {
            $value = '\'' . $value . '\'';
        }
        switch ($type) {
            case 'equal':
                $type = 'eq';
                break;
            case 'notequal':
                $type = 'neq';
                break;
        }
        $type = $this->parseCondition(' ' . $type . ' ');
        $parseStr = '<?php if(' . $name . ' ' . $type . ' ' . $value . '): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * volist标签解析 循环输出数据集
     * 格式：
     * {fly:volist name="userList" id="user" empty=""}
     * {user.username}
     * {user.email}
     * {/fly:volist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagVolist($tag, $content)
    {
        $name = $tag['name'];
        $id = isset($tag['id']) ? $tag['id'] : 'field';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $empty = htmlspecialchars($empty);
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = !empty($tag['mod']) && is_numeric($tag['mod']) ? $tag['mod'] : '2';
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $length = !empty($tag['length']) && is_numeric($tag['length']) ? intval($tag['length']) : 'null';
        if (!empty($tag['row'])) {
            $length = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 'null';
        }
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $length = !empty($limitArr[1]) ? intval($limitArr[1]) : 'null';
        }
        // 允许使用函数设定数据集 <volist name=":fun('arg')" id="vo">{$vo.name}</volist>
        $parseStr = '<?php ';
        $flag = substr($name, 0, 1);
        if (':' == $flag) {
            $name = $this->autoBuildVar($name);
            $parseStr .= '$_result=' . $name . ';';
            $name = '$_result';
        } else {
            $name = $this->autoBuildVar($name);
        }

        $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if (0 != $offset || 'null' != $length) {
            $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $length . ', true) : ' . $name . '->slice(' . $offset . ',' . $length . ', true); ';
        } else {
            $parseStr .= ' $__LIST__ = ' . $name . ';';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= '<?php $mod = ($' . $key . ' % ' . $mod . ' ); ?>';
        $parseStr .= $content;

        /*用于下载模型的ajax下载文件*/
        $parseStr .= '<?php echo isset($' . $id . '["ey_1563185380"])?$' . $id . '["ey_1563185380"]:""; ?>';
        $parseStr .= '<?php echo (1 == $e && isset($' . $id . '["ey_1563185376"]))?$' . $id . '["ey_1563185376"]:""; ?>';
        /*end*/

        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $' . $id . ' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * php标签解析
     * 格式：
     * {fly:php}echo $name{/fly:php}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagPhp($tag, $content)
    {
        $parseStr = '<?php ' . $content . ' ?>';
        return $parseStr;
    }

}