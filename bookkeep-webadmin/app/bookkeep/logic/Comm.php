<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Commor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\bookkeep\logic;

use think\Db;

/**
 * 共享数据管理=》逻辑层
 */
class Comm extends BookkeepBase
{

    /**
     * suggest根据关联数据
     * @return mixed
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/8/2 0002 11:20
     */
    public function getSuggestDataList($data = [])
    {
        $list = [];
        $where = [];
        if (!empty($data['datatype'])) {
            switch ($data['datatype']) {
                case "category":
                    if (!empty($data['type'])) {
                        $where['type'] = ['=', $data['type']];
                    }
                    if (!empty($data['keywords'])) {
                        $where['name'] = ['like', '%' . $data['keywords'] . '%'];
                    }
                    if (tableExists('bk_category')) {
                        $list = Db::name('bk_category')->field('id,name')
                            ->where($where)
                            ->select();
                    }
                    break;
                case "account":
                    if (!empty($data['keywords'])) {
                        $where['name'] = ['like', '%' . $data['keywords'] . '%'];
                    }
                    if (tableExists('bk_account')) {
                        $list = Db::name('bk_account')->field('id,name')
                            ->where($where)
                            ->select();
                    }
                    break;
                case "book":
                    if (!empty($data['keywords'])) {
                        $where['name'] = ['like', '%' . $data['keywords'] . '%'];
                    }
                    if (tableExists('bk_book')) {
                        $list = Db::name('bk_book')->field('id,name')
                            ->where($where)
                            ->select();
                    }
                    break;
                case "person":
                    if (tableExists('bk_person')) {
                        $list = Db::name('bk_person')->field('id,name')
                            ->where($where)
                            ->select();
                    }
                    break;
                case "project":
                    if (!empty($data['keywords'])) {
                        $where['name'] = ['like', '%' . $data['keywords'] . '%'];
                    }
                    if (tableExists('bk_project')) {
                        $list = Db::name('bk_project')->field('id,name')
                            ->where($where)
                            ->select();
                    }
                    break;
                case "shop":
                    if (!empty($data['keywords'])) {
                        $where['name'] = ['like', '%' . $data['keywords'] . '%'];
                    }
                    if (tableExists('bk_shop')) {
                        $list = Db::name('bk_shop')->field('id,name')
                            ->where($where)
                            ->select();
                    }
                    break;
            }
        }
        return $list;
    }

    /**
     * 选中后回调数据=>ajax加载业务详细
     * @param array $data
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/8/3 0003 10:29
     */
    public function getSuggestDataInfo($data = [])
    {
        $info = [];
        if (!empty($data['bus_type'])) {
            switch ($data['bus_type']) {
                case "customer":
                    if (tableExists('cst_customer')) {
                        $info = Db::name('cst_customer')->field('*')
                            ->where(['id' => $data['id']])
                            ->find();
                    }
                    break;
            }
        }
        return $info;
    }

    /**
     * 回显 =>业务订单详细
     * @param array $data
     * @return array
     * Author: kfrs <goodkfrs@QQ.com> created by at 2020/10/9 0009
     */
    public function getBusinessTypeInfo($data = [])
    {
        $rtn = [
            'type' => '其它业务',
            'name' => '---',
            'id' => '0',
            'url' => '',
        ];
        if (!empty($data['bus_type'])) {
            switch ($data['bus_type']) {
                case "sal_contract":
                    $info = Db::name('sal_contract')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '销售合同',
                        'name' => $info['name'],
                        'id' => $info['id'],
                        'url' => url('erp/SalContract/detail', array('id' => $info['id'])),
                    ];
                    break;
                case "sal_order":
                    $info = Db::name('sal_order')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '销售订单',
                        'name' => $info['name'],
                        'id' => $info['id'],
                        'url' => url('erp/SalOrder/detail', array('id' => $info['id'])),
                    ];
                    break;
                case "project":
                    $info = Db::name('project')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '项目订单',
                        'name' => $info['name'],
                        'id' => $info['id'],
                        'url' => url('project/Project/detail', array('id' => $info['id'])),
                    ];
                    break;
                case "project_purchase":
                    $info = Db::name('project_purchase')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '项目采购单',
                        'name' => $info['name'],
                        'id' => $info['id'],
                        'url' => url('project/ProjectPurchase/detail', array('id' => $info['id'])),
                    ];
                    break;
                case  "fin_capital_record":
                    $info = Db::name('fin_capital_record')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'name' => '资金注入抽取',
                        'name' => '资金注入抽取:' . $info['id'],
                        'id' => $info['id'],
                        'url' => url('fms/SalOrder/detail', array('id' => $info['id'])),
                    ];
                    break;
                case "fin_expenses_record":
                    $info = Db::name('fin_expenses_record')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '其它支出',
                        'name' => $info['name'],
                        'id' => $info['id'],
                        'url' => url('erp/SalOrder/detail', array('id' => $info['id'])),
                    ];
                    break;
                case "fin_income_record":
                    $info = Db::name('fin_income_record')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '其它收入',
                        'name' => $info['name'],
                        'id' => $info['id'],
                        'url' => url('SalOrder/detail', array('id' => $info['id'])),
                    ];
                    break;
                case "pos_contract":
                    $info = Db::name('pos_contract')->where(['id' => $data['bus_id']])->find();
                    $rtn = [
                        'type' => '采购合同',
                        'name' => '' . $info['id'] . '=' . $info['name'],
                        'id' => $info['id'],
                        'url' => url('erp/PosContract/detail', array('id' => $info['id'])),
                    ];
                    break;
            }
        }
        return $rtn;
    }

    /**
     * 检查表是否存在
     * @param $table
     * @return bool
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2021/6/18 0018 17:00
     */
    public function tableExists($table)
    {
        $table = SYS_DB_PREFIX . $table;
        $isTable = db()->query('SHOW TABLES LIKE ' . "'" . $table . "'");
        if ($isTable) {
            return true;//表存在
        } else {
            return false;//表不存在
        }
    }
}
