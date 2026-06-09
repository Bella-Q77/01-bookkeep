<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\cms\controller;

use DedeTagParse;
use think\Db;

use app\common\logic\TableField;

/**
 * 友情链接管理-控制器
 */
class OtherCms extends CmsBase
{

	public $db;
	public $prefix;

	/**
	 * 构造方法
	 */
	public function __construct()
	{

		// 执行父类构造方法
		parent::__construct();
		$dbconfig = session('dbconfig');
		$this->prefix = $dbconfig['prefix'];
		$this->db = Db::connect([
			// 数据库类型
			'type' => 'mysql',
			// 服务器地址
			'hostname' => $dbconfig['hostname'],
			// 数据库名
			'database' => $dbconfig['database'],
			// 用户名
			'username' => $dbconfig['username'],
			// 密码
			'password' => $dbconfig['password'],
			// 端口
			'hostport' => $dbconfig['hostport'],
		]);

		$this->tablefield = new TableField();

	}

	// 使用方法配置数据库连接
	public function config()
	{

		$dbconfig = session('dbconfig');

		if (IS_POST) {
			$db = $this->param;
			if (empty($db['hostname']) || empty($db['database']) || empty($db['username']) || empty($db['password']) || empty($db['hostport'])) {
				$this->jump([RESULT_ERROR, '请输入完整数据库配置参数']);
			}
			session('dbconfig', $this->param);

			$dbconfig = session('dbconfig');

			$this->db = Db::connect([
				// 数据库类型
				'type' => 'mysql',
				// 服务器地址
				'hostname' => $dbconfig['hostname'],
				// 数据库名
				'database' => $dbconfig['database'],
				// 用户名
				'username' => $dbconfig['username'],
				// 密码
				'password' => $dbconfig['password'],
				// 端口
				'hostport' => $dbconfig['hostport'],
			]);

			if ($this->db) {
				$this->jump([RESULT_SUCCESS, '数据库链接测试成功~']);
			} else {
				$this->jump([RESULT_ERROR, '请输入完整数据库配置参数']);
			}

		}

		$this->assign('dbconfig', $dbconfig);

		return $this->fetch('config');
//		// 查询数据，，，，和使用系统的DB类方法略有差异
//		$data = $this->db-> table("dede_arctype") -> select();
//		dump($data);

	}

	public function add()
	{
		$data = $this->param;

		switch ($data['step']) {
			case 1://模型
				$list = $this->db->name($this->prefix . "channeltype")
					->where('id','>','0')
					->field('id,nid,typename,maintable,addtable,fieldset')
					->select();
				Db::name('channel')->where('id', '>', 0)->delete();
				foreach ($list as $key => $row) {
					$intoData = [
						'id' => $row['id'],
						'nid' => $row['nid'],
						'name' => $row['typename'],
						'maintable' => 'archives',
						'addtable' => 'archives_' . $row['nid'],
						'fieldset' => $row['fieldset'],
					];
					Db::name('channel')->insert($intoData);
					//创建表
					$this->tablefield->drop_table(SYS_DB_PREFIX . $intoData['addtable']);
					$this->tablefield->add_table(SYS_DB_PREFIX . $intoData['addtable'], $this->logicChannel->getAddTableSql($intoData['addtable']));
				}
				$rtn = ['code' => 1, 'msg' => '=>模型主表导入完成'];
				break;
			case 2://分类表
				$list = $this->db->name($this->prefix . "arctype")
					->field('id,reid,typename,typedir,channeltype,tempindex,templist,temparticle,seotitle,keywords,description,content,sortrank,ispart')
					->select();
				Db::name('arctype')->where('id', '>', 0)->delete();
				foreach ($list as $key => $row) {
					$tempindex = cut_str($row['tempindex'], '/', -1);


					$templist = cut_str($row['templist'], '/', -1);
					$temparticle = cut_str($row['temparticle'], '/', -1);

					$tempindex = preg_replace('/.htm$/i', '.html', $tempindex);
					$templist = preg_replace('/.htm$/i', '.html', $templist);
					$temparticle = preg_replace('/.htm$/i', '.html', $temparticle);

					$intoData = [
						'id' => $row['id'],
						'parent_id' => $row['reid'],
						'typename' => $row['typename'],
						'typedir' => cut_str($row['typedir'], '/', -1),
						'channel_id' => $row['channeltype'],
						'temp_index' => $tempindex,
						'temp_list' => $templist,
						'temp_article' => $temparticle,
						'seotitle' => $row['seotitle'],
						'keywords' => $row['keywords'],
						'description' => $row['description'],
						'content' => $row['content'],
						'sort' => $row['sortrank'],
						'ispart' => $row['ispart'],
					];
					Db::name('arctype')->insert($intoData);
				}
				$rtn = ['code' => 1, 'msg' => '=>栏目表导入完成'];
				break;

			case 3://主表
				$list = $this->db->name($this->prefix . "archives")
					->where('arcrank', '<>', '-2')
					->field('id,typeid,channel,title,flag,shorttitle,writer,source,keywords,description,keywords,description,litpic')
					->select();
				Db::name('archives')->where('id', '>', 0)->delete();
				foreach ($list as $key => $row) {
					$intoData = [
						'id' => $row['id'],
						'channel_id' => $row['channel'],
						'type_id' => $row['typeid'],
						'title' => $row['title'],
						'flag' => $row['flag'],
						'shorttitle' => $row['shorttitle'],
						'keywords' => $row['keywords'],
						'description' => $row['description'],
						'writer' => $row['writer'],
						'source' => $row['source'],
						'litpic' => $row['litpic'],
						'pubdate' => format_time(),
						'click' => 100,
					];
					Db::name('archives')->insert($intoData);
				}
				$rtn = ['code' => 1, 'msg' => '=>文章主表导入完成'];
				break;
			case 4://配置表
				$info = $this->db->name($this->prefix . "sysconfig")
					->column('value', 'varname');
				Db::name('website')->where('name', 'web_name')->setField('value', $info['cfg_webname']);
				Db::name('website')->where('name', 'web_title')->setField('value', $info['cfg_webname']);
				Db::name('website')->where('name', 'web_keywords')->setField('value', $info['cfg_keywords']);
				Db::name('website')->where('name', 'web_description')->setField('value', $info['cfg_description']);
				Db::name('website')->where('name', 'web_copyright')->setField('value', $info['cfg_powerby']);
				$rtn = ['code' => 1, 'msg' => '=>配置导入完成'];
				break;

			case 5://附加表
				$this->addontable();
				$rtn = ['code' => 1, 'msg' => '=>附表结构导入完成'];
				break;

			case 6://附加表
				$this->addondata();
				$rtn = ['code' => 1, 'msg' => '=>附表数据导入完成'];
				break;

			case 7://tag 索引
				$this->tagindexdata();
				$rtn = ['code' => 1, 'msg' => '=>标签索引数据导入完成'];
				break;

			case 8://tag 文档
				$this->taglistdata();
				$rtn = ['code' => 1, 'msg' => '=>标签文档数据导入完成'];
				break;
			case 9://友情链接
				$this->flinkdata();
				$rtn = ['code' => 1, 'msg' => '=>友情链接数据导入完成'];
				break;

			default:
				$rtn = ['code' => 1, 'step' => -1, 'msg' => '=>全部导入完成'];
				return $rtn;
				break;
		}

		$rtn['step'] = $data['step'] + 1;
		$rtn['url'] = url('OtherCms/add');
		return $rtn;
	}


	/**扩展表创建
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * Author: 开发人生 goodkfrs@qq.com
	 * Date: 2021/10/12 0012 11:23
	 */
	public function addontable()
	{

		$channellist = $this->db->name($this->prefix . "channeltype")
			->where('id','>','0')
			->field('id,nid,typename,maintable,addtable,fieldset')
			->select();

		foreach ($channellist as $key=>$row){

			//删除当前模型字段管理数据
			Db::table(SYS_DB_PREFIX.'channel_field')
				->where('channel_id', '=', $row['id'])
				->delete();

			//解析操扩展表字段
			$intoTable=SYS_DB_PREFIX.'archives_'.$row['nid'];
			$fieldset = $row['fieldset'];
			$dtp = new \lqf\DedeTagParse();
			$dtp->SetNameSpace("field", "<", ">");
			$dtp->LoadSource($fieldset);
			//d($dtp->CTags);

			//分析创建表的字段
			foreach($dtp->CTags as $ctagid=>$ctag){
				$field=$ctag->CAttribute->Items['tagname'];
				$comment=$ctag->CAttribute->Items['itemname'];
				$type=$ctag->CAttribute->Items['type'];
				$default=$ctag->CAttribute->Items['default'];
				$maxlength=empty($ctag->CAttribute->Items['maxlength'])?'20':$ctag->CAttribute->Items['maxlength'];
				$this->tablefield->modify_field($intoTable,$field,$type,'',$default,$comment);

				if($field=='body') continue;
				//增加模型字段，管理数据
				$intoChannel=[
					'main_table'=>'archives',
					'ext_table'=>'archives_'.$row['nid'],
					'field_name'=>$field,
					'field_type'=>$type,
					'show_name'=>$comment,
					'default_value'=>$default,
					'maxlength'=>$maxlength,
					'channel_id'=>$row['id'],
				];
				Db::table(SYS_DB_PREFIX.'channel_field')->insert($intoChannel);
			}
		}

	}

	/**扩展表数据导坟
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 * Author: 开发人生 goodkfrs@qq.com
	 * Date: 2021/10/12 0012 11:24
	 */
	public function addondata()
	{

		$channellist = $this->db->name($this->prefix . "channeltype")
			->where('id','>','0')
			->field('id,nid,typename,maintable,addtable,fieldset')
			->select();

		foreach ($channellist as $key=>$row){

			//表数据整理
			$sourceTable=str_replace($this->prefix,$this->prefix,$row['addtable']);
			$sourceData = $this->db->name($sourceTable)->select();

			$intoTable=SYS_DB_PREFIX.'archives_'.$row['nid'];

			Db::table($intoTable)->where('id', '>', 0)->delete();

			foreach ($sourceData as $key=>&$row){
				$row['id']=$row['aid'];
				$row['type_id']=$row['typeid'];
				unset($row['aid']);
				unset($row['typeid']);
				Db::table($intoTable)->insert($row);
			}

		}

	}

	/**tag索引表
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 * Author: 开发人生 goodkfrs@qq.com
	 * Date: 2021/10/14 0014 11:22
	 */
	public function tagindexdata()
	{
		$list = $this->db->name($this->prefix . "tagindex")
			->where('id','>','0')
			->field('id,tag,typeid,count,total,weekcc,monthcc')
			->select();
		$intoTable=SYS_DB_PREFIX.'tagindex';
		Db::table($intoTable)->where('id', '>', 0)->delete();

		foreach ($list as $key=>$row){
			Db::table($intoTable)->insert($row);
		}

	}

	/**tag 文档表
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 * Author: 开发人生 goodkfrs@qq.com
	 * Date: 2021/10/14 0014 11:22
	 */
	public function taglistdata()
	{
		$list = $this->db->name($this->prefix . "taglist")
			->field('tid,aid,typeid,tag')
			->select();
		$intoTable=SYS_DB_PREFIX.'taglist';
		Db::table($intoTable)->where('id', '>', 0)->delete();
		foreach ($list as $key=>$row){
			Db::table($intoTable)->insert($row);
		}

	}

	/**友情链接
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 * @throws \think\exception\PDOException
	 * Author: 开发人生 goodkfrs@qq.com
	 * Date: 2021/10/20 0020 10:35
	 */
	public function flinkdata()
	{
		$list = $this->db->name($this->prefix . "flink")
			->where('id','>','0')
			->field('url,webname as title')
			->select();

		$intoTable=SYS_DB_PREFIX.'friendlink';
		Db::table($intoTable)->where('id', '>', 0)->delete();

		foreach ($list as $key=>$row){
			Db::table($intoTable)->insert($row);
		}

	}

}
