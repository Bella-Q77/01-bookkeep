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

use app\admin\model\AdminBase;
use think\Loader;

/**
 * Admin基础逻辑
 */
class AdminLogicBase extends AdminBase
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 重写获取器 兼容 模型|逻辑|验证|服务 层实例获取
     */
    public function __get($name)
    {
        $layer = $this->getLayerPrefix($name);

        if (false === $layer) {

            return parent::__get($name);
        }
        $model = sr($name, $layer);
        //  return LAYER_VALIDATE_NAME == $layer ? validate($model) : model($model, $layer);

        if (LAYER_VALIDATE_NAME == $layer){
            return validate($model);
        }else{
            return Loader::model($model, $layer, false, 'admin');
        }
    }
}
