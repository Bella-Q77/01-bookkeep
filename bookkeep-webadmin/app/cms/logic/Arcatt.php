<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Arctypeor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2020-02-12
 */

namespace app\cms\logic;
use think\Db;
/**
 * 栏目逻辑
 */
class Arcatt extends CmsBase
{

    /**
     * 模型管理处列表
     */
    public function getArcattList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        $list=$this->modelArcatt->getList($where, $field, $order, $paginate)->toArray();

        if($paginate===false) $list['data']=$list;

        foreach ($list['data'] as &$row){
            $row['ispart_text']=$this->modelArctype->ispart_text($row['ispart']);
        }
        return $list;
    }

    /**
     * 得到文章属性文本
     * @param int $att
     * @return string
     */
    public function getArcattName($att=0)
    {
		$reg_txt=str_replace(",","|",$att);
        $where['att']=['exp',Db::raw("REGEXP '(^|,)($reg_txt)(,|$)'")];
        $list=$this->modelArcatt->getList($where, '', '', false)->toArray();
        $attArr= array_column($list,'attname');
        return empty($attArr)?'':implode(',',$attArr);
    }

    /**文章属性的checkboxhtml
     * @param $inputname
     * @param null $stxt
     * @return string  htmlText
     */
    public function getArcattCheckbox($inputname, $stxt=null){
        $sarr=!empty($stxt)?explode(",",$stxt):array('1');
        $list=$this->modelArcatt->getList('', '', '', false)->toArray();
        $html="";
        foreach($list as $v){
            $ifcheck=in_array($v['att'],$sarr)?"checked":"";
            $html .="<input type='checkbox' name='".$inputname."[]' value='".$v['att']."' $ifcheck> ".$v['attname']."=".$v['att']." ";
        }
        return $html;
    }

}
