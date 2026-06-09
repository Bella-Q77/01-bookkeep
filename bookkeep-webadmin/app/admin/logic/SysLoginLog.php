<?php
// +----------------------------------------------------------------------
// | 07FLYCRM [基于ThinkPHP5.0开发]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2021 http://www.07fly.xyz
// +----------------------------------------------------------------------
// | Professional because of focus  Persevering because of happiness
// +----------------------------------------------------------------------
// | Author: 开发人生 <goodkfrs@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 登录日志
 */
class SysLoginLog extends AdminBase
{

    /**
     * 条件生成
     * @param $data
     * @return array
     * @author: 开发人生 goodkfrs@qq.com
     * @Time: 2023/6/21 15:38
     */
    public function getWhere($data = [])
    {
        $where = [];
        if (!empty($data['keywords'])) {
            $where['name|username|ip|url|describe'] = ['like', '%' . $data['keywords'] . '%'];
        }
        //创建时间
        if (!empty($data['create_time'])) {
            $range_date = rangedate2arr($data['create_time'], "-", 'int');
            $where['create_time'] = ['between', $range_date];
        }
        return $where;
    }

    /**
     * 获取日志列表
     */
    public function getSysLoginLogList($where = [], $field = true, $order = 'id desc', $paginate = DB_LIST_ROWS)
    {

        return $this->modelSysLoginLog->getList($where, true, 'create_time desc', $paginate);
    }

    /**
     * 日志删除
     */
    public function loginLogDel($where = [])
    {

        return $this->modelSysLoginLog->deleteInfo($where, true) ? [RESULT_SUCCESS, '日志删除成功'] : [RESULT_ERROR, $this->modelSysLoginLog->getError()];
    }

    /**
     * 日志添加
     */
    public function loginLogDelAdd($name = '', $describe = '')
    {
        $sys_user_info = session('sys_user_info');
        $request = request();
        $data['sys_user_id'] = $sys_user_info['id'];
        $data['username'] = $sys_user_info['username'];
        $data['ip'] = $request->ip();
        $data['url'] = $request->url();
        $data['name'] = $name;
        $data['describe'] = $describe;
        $this->modelSysLoginLog->is_update_cache_version = false;
        $this->modelSysLoginLog->setInfo($data);
    }
}
