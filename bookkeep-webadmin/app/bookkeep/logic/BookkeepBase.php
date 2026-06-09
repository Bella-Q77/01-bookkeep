<?php
/*
* bookkeep.logic  逻辑层基础类
*
* ----------------------------------------------
* 零起飞网络 - 专注于企业管理系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @Copyright Copyright (C) 2017-2025 07FLY Network Technology Co,LTD.
* @License For licensing, see LICENSE.html
* @Author ：kfrs <goodkfrs@QQ.com> 574249366
* @Version ：1.1.0
* @Link ：http://www.07fly.xyz
* @Date:2025-09-26 10:09:38
*/
namespace app\bookkeep\logic;
use app\common\logic\LogicBase;

/**
 * 模块基类
 */
class BookkeepBase extends LogicBase{
    /**
     * 数据排序设置
     */
    public function setSort($model = null, $param = null)
    {
        $model_str = LAYER_MODEL_NAME . $model;
        $obj = $this->$model_str;
        $result = $obj->setFieldValue(['id' => (int)$param['id']], 'sort', (int)$param['value']);
        $result && action_log('数据排序', '数据排序调整' . '，model：' . $model . '，id：' . $param['id'] . '，value：' . $param['value']);
        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $obj->getError()];
    }

    /**
     * 数据设置
     */
    public function setField($model = null, $param = null)
    {
        $model_str = LAYER_MODEL_NAME . $model;
        $obj = $this->$model_str;
        $result = $obj->setFieldValue(['id' => $param['id']], $param['name'], $param['value']);
        $result && action_log('数据更新', '数据更新调整' . '，model：' . $model . '，id：' . $param['id'] . '，name：' . $param['name'] . '，value：' . $param['value']);
        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $obj->getError()];
    }
}
?>