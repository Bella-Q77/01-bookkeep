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

namespace app\index\taglib;

use think\Db;
use think\Request;


/**
 * 列表筛选字段
 */
class TagFilter extends Base
{
	public $tid = '';
	public $logicArchives = '';

	//初始化
	protected function _initialize()
	{
		parent::_initialize();

		//引用文档逻辑
		$this->logicArchives = new \app\index\logic\Archives();

		// 应用于栏目列表
		$this->tid = input("param.tid/s", '');

		/*应用于文档列表*/
		$aid = input('param.aid/d', 0);
		if ($aid > 0) {
			$this->tid = $this->logicArchives->getArchivesFieldValue(['id' => $aid], 'type_id');
		}
		/*--end*/

		/*tid为目录名称的情况下*/
		$this->tid = $this->getTrueTypeid($this->tid);
		/*--end*/
	}


	/**获取面包屑位置
	 * @param string $typeid
	 * @param string $symbol
	 * @param string $style
	 * @return string
	 * Author: lingqifei created by at 2020/2/27 0027
	 */
	public function getFilter($param)
	{
		$typeid = !empty($param['typeid']) ? $param['typeid'] : $this->tid;

		if (empty($param['channelid'])) {
			echo '标签filter报错：channelid必须为数字不能为空，请正确填写栏目ID（0~9）数字。';
			return false;
		} else {
			$where['channel_id'] = ['=', $param['channelid']];
		}

		if (empty($param['addfields'])) {
			echo '标签filter报错：addfields为扩展字段名，请正确填写栏目channel下的字段。';
			return false;
		} else {
			$where['field_name'] = ['=', $param['addfields']];
		}

		//查询字段
		$fieldname = $param['addfields'];
		$defaultvalue = Db::name('channel_field')
			->where($where)
			->value('default_value');

		//当前地址
		$request = Request::instance();
		$urlparam = $request->param();//所有参数
		$baseurl = $request->domain() . $request->baseUrl();

		switch ($param['type']) {
			case '1':

				$addfield_items = str2arr($defaultvalue);//字段值
				$fiterString = '';
				$filterArr = $urlparam;


				if (!empty($urlparam[$fieldname])) {//表示有参数，全部为空
					unset($filterArr[$fieldname]);
					$href = $baseurl . '?' . http_build_query($filterArr, null, '&');
					$fiterString .= '<a href="' . $href . '">全部</a>&nbsp;&nbsp;';
				} else {
					$fiterString .= '<span>全部</span>&nbsp;&nbsp;';
				}

				foreach ($addfield_items as $itemval) {

					if (!empty($urlparam[$fieldname])) {

						if(urlencode($urlparam[$fieldname]) == urlencode($itemval) ){
							$fiterString .= '<span>' . $itemval . '</span>&nbsp;&nbsp;';
						}else{
							$filterArr[$fieldname] = $itemval;
							$href = $baseurl . '?' . http_build_query($filterArr, null, '&');
							$fiterString .= '<a href="' . $href . '">' . $itemval . '</a>&nbsp;&nbsp;';
						}

					} else {

						$filterArr[$fieldname] = $itemval;
						$href = $baseurl . '?' . http_build_query($filterArr, null, '&');
						$fiterString .= '<a href="' . $href . '">' . $itemval . '</a>&nbsp;&nbsp;';

					}
				}
				break;
			default:

				break;

		}
		return $fiterString;
	}
}