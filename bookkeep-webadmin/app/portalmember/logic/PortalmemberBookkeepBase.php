<?php
/*
*
* cms.Archives  内容发布系统-频道模型
*
* =========================================================
* 零起飞网络 - 专注于网站建设服务和行业系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2021 07FLY Network Technology Co,LTD.
* @license    For licensing, see LICENSE.html or http://www.07fly.xyz/crm/license
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\portalmember\logic;

/**
 * 模块基类
 */
class PortalmemberBookkeepBase extends PortalmemberBase
{
    public function __construct()
    {
        parent::__construct();
        $this->memberId = member_is_login();
        $this->bookId = session('default_book_id');
    }

    public function getPeriodTypeDateRange($data)
    {
        $periodDate = '';
        if (!empty($data['period_date'])) {
            $periodDate = $data['period_date'];
        }
        // 根据 period_type 决定默认时间范围
        if (!empty($data['period_type'])) {
            switch ($data['period_type']) {
                case 'week':
                    $date_range = getWeekStartEndTime($periodDate);
                    break;
                case 'month':
                    $date_range = getMonthStartEndTime($periodDate);
                    break;
                case 'qutar':
                    $date_range = getQuarterStartEndTime($periodDate);
                    break;
                case 'year':
                    $date_range = getYearStartEndTime($periodDate);
                    break;
                case 'diy':
                    $date_range = rangedate2arr($periodDate);
                    $date_range['begin'] = $date_range[0];
                    $date_range['end'] = $date_range[1];
                    break;
                default:
                    $date_range = getYearStartEndTime($periodDate);
                    break;
            }
        } else {
            $date_range = getMonthStartEndTime($periodDate);
        }
        return [$date_range['begin'], $date_range['end']];
    }
}

?>