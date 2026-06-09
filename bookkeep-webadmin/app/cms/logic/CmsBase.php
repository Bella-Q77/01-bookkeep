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
namespace app\cms\logic;

use app\common\logic\LogicBase;

/**
 * Admin基础逻辑
 */
class CmsBase extends LogicBase
{

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
     * 数据排序设置
     */
    public function setField($model = null, $param = null)
    {
        $model_str = LAYER_MODEL_NAME . $model;

        $obj = $this->$model_str;

        $result = $obj->setFieldValue(['id' => (int)$param['id']], $param['name'], (int)$param['value']);

        $result && action_log('数据更新', '数据更新调整' . '，model：' . $model . '，id：' . $param['id'] . '，name：' . $param['name']. '，value：' . $param['value']);

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $obj->getError()];
    }

    /**
     * 数据排序设置
     */
    public function setModelField($model = null, $param = null)
    {

        if(empty($param['name'])){
            return [RESULT_ERROR, '请传递完整的参数~name~'];
            eixt;
        }
        if(empty($param['id'])){
            return [RESULT_ERROR, '请传递完整的参数~id~'];
            eixt;
        }
        if(empty($param['value'])){
            return [RESULT_ERROR, '请传递完整的参数~value~'];
            eixt;
        }
        $model_str = LAYER_MODEL_NAME . $model;

        $obj = $this->$model_str;
        $result = $obj->setFieldValue(['id' => (int)$param['id']], $param['name'], (int)$param['value']);

        $result && action_log('数据更新', '数据更新调整' . '，model：' . $model . '，id：' . $param['id'] . '，name：' . $param['name']. '，value：' . $param['value']);

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $obj->getError()];
    }


    /**
     * 获取列表树结构
     */
    public function getListTree($list = [])
    {

        if (is_object($list)) {

            $list = $list->toArray();
        }

        return list_to_tree(array_values($list), 'id', 'pid', 'child');
    }

}
