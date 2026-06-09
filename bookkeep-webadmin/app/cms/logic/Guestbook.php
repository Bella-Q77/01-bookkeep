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

namespace app\cms\logic;

use app\common\logic\TableField;
use think\Db;

/**
 *  留言表单管理逻辑
 */
class Guestbook extends CmsBase
{

    private $tablefield = null;


    /**
     * 析构函数
     */
    function __construct()
    {
        $this->tablefield = new TableField();
    }

    /**
     *  留言表单管理处列表
     */
    public function getGuestbookList($where = [], $field = true, $order = '', $paginate = DB_LIST_ROWS)
    {
        return $this->modelGuestbook->getList($where, $field, $order, $paginate)->toArray();
    }

    /**
     *  留言表单添加
     */
    public function guestbookAdd($data = [])
    {

        $validate_result = $this->validateGuestbook->scene('add')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateGuestbook->getError()];
        }

        $url = url('show');
        $data['maintable'] = 'guestbook';//主表
        $data['addtable'] = 'guestbook_' . $data['nid'];//扩展表

        //添加创建表
        $rtn = $this->tablefield->add_table(SYS_DB_PREFIX . $data['addtable'], $this->getAddTableSql(SYS_DB_PREFIX . $data['addtable']));
        if ($rtn[0] == RESULT_ERROR) return $rtn;

        $result = $this->modelGuestbook->setInfo($data);

        $result && action_log('新增', '新增留言表单，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '添加成功', $url] : [RESULT_ERROR, $this->modelGuestbook->getError()];
    }

    /**
     *  留言表单编辑
     */
    public function guestbookEdit($data = [])
    {

        $validate_result = $this->validateGuestbook->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validateGuestbook->getError()];
        }

        $url = url('guestbookList');

        $result = $this->modelGuestbook->setInfo($data);

        $result && action_log('编辑', '编辑 留言表单，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, ' 留言表单编辑成功', $url] : [RESULT_ERROR, $this->modelGuestbook->getError()];
    }

    /**
     *  留言表单删除
     */
    public function guestbookDel($where = [])
    {

        $list = $this->getGuestbookList($where);

        foreach ($list['data'] as $row) {
            $this->tablefield->drop_table(SYS_DB_PREFIX . $row['addtable']);
        }

        $result = $this->modelGuestbook->deleteInfo($where, true);

        $result && action_log('删除', '删除 留言表单，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, ' 留言表单删除成功'] : [RESULT_ERROR, $this->modelGuestbook->getError()];
    }

    /**
     *  留言表单管理处信息
     */
    public function getGuestbookInfo($where = [], $field = true)
    {

        return $this->modelGuestbook->getInfo($where, $field);
    }


    /**封装orderby
     * @param array $data
     * Author: lingqifei created by at 2020/3/2 0002
     */
    public function getOrderby($data = [])
    {
        $order_by = '';
        //排序操作
        if (!empty($data['orderField'])) {
            $orderField = $data['orderField'];
            $orderDirection = $data['orderDirection'];
        } else {
            $orderField = "";
            $orderDirection = "";
        }
        if ($orderField == 'by_name') {
            $order_by = "name $orderDirection";
        } else if ($orderField == 'by_mobile') {
            $order_by = "mobile $orderDirection";
        } else if ($orderField == 'by_linkman') {
            $order_by = "linkman $orderDirection";
        } else if ($orderField == 'by_tel') {
            $order_by = "tel $orderDirection";
        } else if ($orderField == 'by_sort') {
            $order_by = "sort $orderDirection";
        } else {
            $order_by = "sort asc";
        }
        return $order_by;
    }


    /**
     * 创建扩展表SQL
     */
    public function getAddTableSql($table)
    {
        $sql = "
CREATE TABLE `$table` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`gid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`content` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`reply` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`mobile` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`realname` VARCHAR(250) NULL DEFAULT '' COMMENT '0' COLLATE 'utf8_general_ci',
	`create_time` INT(11) NOT NULL DEFAULT '0',
	`update_time` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `gid` (`gid`)
)
COMMENT='留言表单扩展表'
COLLATE='utf8_general_ci'
ENGINE=MyISAM;";
        return $sql;
    }

    /**
     *  留言扩展列表
     */
    public function getGuestbookExtList($data = [], $paginate = DB_LIST_ROWS)
    {
        if (empty($data['gid'])) {
            return [RESULT_ERROR, '选择表单'];
        }
        $table = $this->getGuesbookExtTableInfo($data['gid']);
        $extfieldlist = $this->logicGuestbookField->getExtTableFieldList($table['maintable'], $table['addtable']);

        //扩展数据处理
        $where['gid'] = ['=', $data['gid']];

        if (!empty($data['bdate'])) {
            $where['create_time'] = ['>=', strtotime($data['bdate'])];
        }

        if (!empty($data['edate'])) {
            $where['create_time'] = ['<', strtotime($data['edate'])];
        }

        if (!empty($data['bdate']) && !empty($data['edate'])) {
            $date_range = [strtotime($data['bdate']), strtotime($data['edate'])];
            $where['create_time'] = ['between', $date_range];
        }
        $list = Db::table(SYS_DB_PREFIX . $table['addtable'])
            ->where($where)
            ->order('create_time desc')
            ->paginate($paginate)
            ->ToArray();

        foreach ($list['data'] as &$row) {
            $extcontent = '';
            $dwtcontent = '';
            $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
            foreach ($extfieldlist as $item) {
                $extcontent .= '<br><b>' . $item['show_name'] . '：</b>' . $row[$item['field_name']];
                $dwtcontent .= "\r\n";
                $dwtcontent .= $item['show_name'] . '：' . $row[$item['field_name']];
            }
            $extcontent .= '<br><b>内容：</b>' . $row['content'] . '<br>';
            $dwtcontent .= "\r\n";
            $dwtcontent .= '内容：' . $row['content'] . '<br>';
            $row['extcontent'] = $extcontent;
            $row['dwtcontent'] = $dwtcontent;
        }
        return $list;
    }

    /**
     *  留言扩展列表
     */
    public function getGuestbookExtListDown($data = [])
    {
        if (empty($data['gid'])) {
            return [RESULT_ERROR, '选择表单'];
        }
        $table = $this->getGuesbookExtTableInfo($data['gid']);
        $extfieldlist = $this->logicGuestbookField->getExtTableFieldList($table['maintable'], $table['addtable']);
        //扩展数据处理
        $where['gid'] = ['=', $data['gid']];
        if (!empty($data['bdate'])) {
            $where['create_time'] = ['>=', strtotime($data['bdate'])];
        }
        if (!empty($data['edate'])) {
            $where['create_time'] = ['<', strtotime($data['edate'])];
        }
        if (!empty($data['bdate']) && !empty($data['edate'])) {
            $date_range = [strtotime($data['bdate']), strtotime($data['edate'])];
            $where['create_time'] = ['between', $date_range];
        }
        $list = Db::table(SYS_DB_PREFIX . $table['addtable'])
            ->where($where)
            ->order('create_time desc')
            ->select();
        $titles = "手机号,内容,时间,回复";
        $fields = "mobile,content,create_time,rereply";
        foreach ($extfieldlist as $item) {
            $titles .= ','.$item['show_name'];
            $fields .= ','.$item['field_name'];
        }
        action_log('下载', '留言表单列表');
        export_excel($titles, $fields, $list, '表单列表-'.$table['name']);
    }

    /**
     *  留言表单编辑
     */
    public function guestbookExtReply($data = [])
    {
        $table = $this->getGuesbookExtTableInfo($data['gid']);
        $where['gid'] = ['=', $data['gid']];
        $where['id'] = ['=', $data['id']];
        $result = Db::table(SYS_DB_PREFIX . $table['addtable'])->where($where)->update($data);
        $url = url('ext_list');
        $result && action_log('留言回复', '留言回复：' . $data['reply']);
        return $result ? [RESULT_SUCCESS, ' 操作成功', $url] : [RESULT_ERROR, $this->modelGuestbook->getError()];
    }

    /**
     *  留言表单删除
     */
    public function guestbookExtDel($data = [])
    {

        $table = $this->getGuesbookExtTableInfo($data['gid']);

        $where['gid'] = ['=', $data['gid']];
        $where['id'] = ['=', $data['id']];
        $result = Db::table(SYS_DB_PREFIX . $table['addtable'])->where($where)->delete();

        $result && action_log('删除', '删除 留言表单，where：' . http_build_query($data));
        return $result ? [RESULT_SUCCESS, ' 删除成功'] : [RESULT_ERROR, $this->modelGuestbook->getError()];
    }

    /**
     *  留言表单管理处信息
     */
    public function getGuestbookExtInfo($data = [])
    {
        if (empty($data['gid'])) {
            return [RESULT_ERROR, '选择表单'];
        }

        $table = $this->getGuesbookExtTableInfo($data['gid']);

        $where['gid'] = ['=', $data['gid']];
        $where['id'] = ['=', $data['id']];
        $info = Db::table(SYS_DB_PREFIX . $table['addtable'])
            ->where($where)
            ->find();
        return $info;
    }

    /**获取表单数据表信息
     * @param int $gid
     * @return mixed
     * Author: lingqifei created by at 2020/3/2 0002
     */
    public function getGuesbookExtTableInfo($gid = 0)
    {
        $info = $this->modelGuestbook->getInfo(['id' => $gid], 'name,addtable,maintable');
        return $info;
    }

}
