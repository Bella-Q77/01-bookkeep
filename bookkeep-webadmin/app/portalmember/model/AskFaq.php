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
namespace app\portalmember\model;

use think\Db;

/**
 * 问题列表管理=》模型
 */
class AskFaq extends PortalmemberBase
{
    public function status()
    {
        return [
            '0' => '待处理',
            '1' => '处理中',
            '2' => '已回复',
            '3' => '已解决',
            '4' => '已关闭'
        ];
    }

    // 状态
    public function getStatusText($value)
    {
        $status = $this->status();
        $name = empty($status[$value]) ? '' : $status[$value];
        switch ($value) {
            case '0':
                $className = 'label label-default';
                break;
            case '1':
                $className = 'label label-primary';
                break;
            case '2':
                $className = 'label label-info';
                break;
            case '3':
                $className = 'label label-success';
                break;
            case '4':
                $className = 'label label-danger';
                break;
        }
        $nameHtml = '<span class="' . $className . '">' . $name . '</span>';
        return $nameHtml;
    }

}
