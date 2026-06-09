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
 * 模型管理逻辑
 */
class Channel extends CmsBase
{

    private $tablefield = null;


    /**
     * 析构函数
     */
    function  __construct() {
        $this->tablefield = new TableField();
    }

    /**
     * 模型管理处列表
     */
    public function getChannelList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        return $this->modelChannel->getList($where, $field, $order, $paginate)->toArray();
    }
    
    /**
     * 模型添加
     */
    public function channelAdd($data = [])
    {
        
        $validate_result = $this->validateChannel->scene('add')->check($data);
        if (!$validate_result) {
            return [RESULT_ERROR, $this->validateChannel->getError()];
        }

        $url = url('show');
        $data['maintable']='archives';//主表
        $data['addtable']='archives_'.$data['nid'];//扩展表

        //添加创建表
        $rtn=$this->tablefield->add_table(SYS_DB_PREFIX.$data['addtable'],$this->getAddTableSql($data['addtable']));
        if($rtn[0]==RESULT_ERROR)  return $rtn;
        
        $result = $this->modelChannel->setInfo($data);

        $result && action_log('新增', '新增模块，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '模块添加成功', $url] : [RESULT_ERROR, $this->modelChannel->getError()];
    }
    
    /**
     * 模型编辑
     */
    public function channelEdit($data = [])
    {
        
        $validate_result = $this->validateChannel->scene('edit')->check($data);
        
        if (!$validate_result) {
         
            return [RESULT_ERROR, $this->validateChannel->getError()];
        }
        
        $url = url('channelList');
        
        $result = $this->modelChannel->setInfo($data);
        
        $result && action_log('编辑', '编辑模型，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '模型编辑成功', $url] : [RESULT_ERROR, $this->modelChannel->getError()];
    }
    
    /**
     * 模型删除
     */
    public function channelDel($where = [])
    {

        $list=$this->getChannelList($where);

        foreach ($list['data'] as $row){
            $this->tablefield->drop_table(SYS_DB_PREFIX.$row['addtable']);
        }

        $where['issystem']=['<>','1'];//排除系统模块
        $result = $this->modelChannel->deleteInfo($where,true);
        
        $result && action_log('删除', '删除模型，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '模型删除成功'] : [RESULT_ERROR, $this->modelChannel->getError()];
    }
    
    /**
     * 模型管理处信息
     */
    public function getChannelInfo($where = [], $field = true)
    {

        return $this->modelChannel->getInfo($where, $field);
    }

    /**
     * 创建扩展表SQL
     */
    public function getAddTableSql($table)
    {
        $table=SYS_DB_PREFIX.$table;
        $sql="
CREATE TABLE `$table` (
	`id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`type_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`body` MEDIUMTEXT NULL,
	`redirecturl` VARCHAR(255) NOT NULL DEFAULT '',
	`templet` VARCHAR(30) NOT NULL DEFAULT '',
	`userip` CHAR(15) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	INDEX `type_id` (`type_id`)
)
COMMENT='扩展表'
COLLATE='utf8_general_ci'
ENGINE=MyISAM;"	;
        return $sql;
    }


}
