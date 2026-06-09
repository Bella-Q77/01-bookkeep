<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * BkShopor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\portalmember\logic;

use app\common\model\Sequence;
use think\Db;

/**
 * 统计=》逻辑层
 */
class BkStat extends PortalmemberBookkeepBase
{
    public function __construct()
    {
        parent::__construct();
        $this->memberId = MEMBER_ID;
        $this->bookId = session('default_book_id');
    }

    /**
     * 本月统计
     */
    public function getStatMonthTotal($data = [])
    {
        //本月统计，收入
        $where['book_id'] = ['=', $this->bookId];
        $where['member_id'] = ['=', $this->memberId];


        //获取时间范围
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        if ($data['period_type'] == 'month') {
            $statField = lang('本月');
        } else if ($data['period_type'] == 'week') {
            $statField = lang('本周');
        } else if ($data['period_type'] == 'quarter') {
            $statField = lang('本季度');
        } else if ($data['period_type'] == 'year') {
            $statField = lang('本年');
        } else if ($data['period_type'] == 'diy') {
            $statField = format_time($start_time, 'Y-m-d') . '-' . format_time($end_time, 'Y-m-d') . lang(':');
        }

        $where['transaction_date'] = ['between', [$start_time, $end_time]];


        $title = "较上月对比";
        $where['type'] = ['=', 1];
        $list['stat_month_income']['text'] = $statField . '收入';
        $list['stat_month_income']['curr'] = $this->modelBkTransaction->stat($where, 'sum', 'amount');
        $list['stat_month_income']['last'] = 0;

        $where['type'] = ['=', '-1'];
        $list['stat_month_expense']['text'] = $statField . '支出';
        $list['stat_month_expense']['curr'] = $this->modelBkTransaction->stat($where, 'sum', 'amount');
        $list['stat_month_expense']['last'] = 0;

        unset($where['type']);
        $list['stat_month_balance']['text'] = $statField . '结余';
        $list['stat_month_balance']['curr'] = $this->modelBkTransaction->stat($where, 'sum', 'amount');
        $list['stat_month_balance']['last'] = 0;


        //使用预算
        $usedAmount = $list['stat_month_expense']['curr'];
        $budgetAmount = $this->modelBkBudget->getBudgetAmount($this->bookId, $data['period_type']);
        $budgetUsedInfo = $this->modelBkBudget->calcBudgetRate($usedAmount, $budgetAmount);
        $list['stat_month_budget']['text'] = $statField . '使用预算率';
        $list['stat_month_budget']['curr'] = empty($budgetUsedInfo['budget_used_rate']) ? 0 : $budgetUsedInfo['budget_used_rate'];
        $list['stat_month_budget']['last'] = 0;

        $rtnArray = [];
        foreach ($list as $key => $row) {
            $curr = $row['curr'];
            $last = $row['last'];
            if ($last == 0) {
                $rate = ($curr - $last) * 100;
            } else {
                $rate = round(($curr - $last) / $last * 100, 3);
            }
            if ($rate == 0) {
                $tmp['color'] = 'text-info';
                $tmp['arrow'] = '';
            } elseif ($rate > 0) {
                $tmp['color'] = 'text-danger';
                $tmp['arrow'] = 'fa fa-long-arrow-up';
            } elseif ($rate < 0) {
                $tmp['rate'] = $rate;
                $tmp['color'] = 'text-success';
                $tmp['arrow'] = 'fa fa-long-arrow-down';
            }
            $tmp['cnt'] = $curr;
            $tmp['rate'] = $rate . '%';
            $tmp['title'] = $title;
            $tmp['text'] = $row['text'];;
            $rtnArray[$key] = $tmp;
        }
        return $rtnArray;
    }


    //预算使用统计
    public function getBudgetUseStat($data = [])
    {
        //本月统计，收入
        $where['book_id'] = ['=', $this->bookId];
        $where['member_id'] = ['=', $this->memberId];

        //获取时间范围
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        if ($data['period_type'] == 'month') {
            $statField = lang('本月');
        } else if ($data['period_type'] == 'week') {
            $statField = lang('本周');
        } else if ($data['period_type'] == 'quarter') {
            $statField = lang('本季度');
        } else if ($data['period_type'] == 'year') {
            $statField = lang('本年');
        } else if ($data['period_type'] == 'diy') {
            //自定义时间,预算周期默认为年
            $statField = format_time($start_time, 'Y-m-d') . '-' . format_time($end_time, 'Y-m-d') . lang(':');
            $statField = lang('按年');
            $data['period_type'] = 'year';
        }

        //使用预算
        $usedAmount = $this->modelBkStat->getTransactionAmount($this->bookId, $start_time, $end_time, '-1');//获取支出
        $budgetAmount = $this->modelBkBudget->getBudgetAmount($this->bookId, $data['period_type']);//获取预算
        $budgetUsedInfo = $this->modelBkBudget->calcBudgetRate($usedAmount, $budgetAmount);//预算使用率

        $rtnArray = [
            'text' => $statField . '预算',
            'color' => 'text-info',
        ];
        $rtnArray = array_merge($rtnArray, $budgetUsedInfo);
        return $rtnArray;
    }

    //收支趋势
    public function getIncomeExpenseTrendStat($data = [])
    {
        //统计的时间段
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        //获取本账本
        if (!empty($data['book_id'])) {
            $bookId = $data['book_id'];
        } else {
            $bookId = $this->bookId;
        }

        //本账本收支
        $where2create = " And book_id = '" . $bookId . "'";

        switch ($data['period_type']) {
            case 'week':
                $xAxis = getDatesBetweenToWeeks($start_time, $end_time);
                $statField = "from_unixtime(create_time, '%Y-%u') as xdate";
                break;
            case 'month':
                $xAxis = getDatesBetweenTwoDays($start_time, $end_time);
                $statField = "from_unixtime(create_time, '%Y-%m-%d') as xdate";
                break;
            case 'quarter':
                $xAxis = getDatesBetweenToMonths($start_time, $end_time);
                $statField = "from_unixtime(create_time, '%Y-%m') as xdate";
                break;
            case 'year':
                $xAxis = getDatesBetweenToMonths($start_time, $end_time);
                $statField = "from_unixtime(create_time, '%Y-%m') as xdate";
                break;
            case 'diy':
                //自定义时间,预算周期默认为天，
                $xAxis = getDatesBetweenTwoDays($start_time, $end_time);
                $statField = "from_unixtime(create_time, '%Y-%m-%d') as xdate";
                //30天以上,按月显示
                if (count($xAxis) > 30) {
                    $xAxis = getDatesBetweenToMonths($start_time, $end_time);
                    $statField = "from_unixtime(create_time, '%Y-%m') as xdate";
                }
                break;
            default:
                $xAxis = getDatesBetweenToMonths($start_time, $end_time);
                $statField = "from_unixtime(create_time, '%Y-%m') as xdate";
                break;
        }

        $rtnData['title']['text'] = '收支趋势';
        $rtnData['legend'] = ['收入', '支出', '结余'];
        $rtnData['xaxis'] = $xAxis;

        // 统一查询方法
        $seriesData = $this->modelBkStat->getTrendSeriesData($bookId, $statField, $start_time, $end_time);

        // 构建收入系列
        $rtnData['series'][] = $this->buildTrendSeriesData('收入', $xAxis, $seriesData['income'], 'line');

        // 构建支出系列
        $rtnData['series'][] = $this->buildTrendSeriesData('支出', $xAxis, $seriesData['expense'], 'line', true);

        // 构建结余系列
        $rtnData['series'][] = $this->buildTrendSeriesData('结余', $xAxis, $seriesData['balance'], 'line');

        return $rtnData;
    }

    // 构建系列数据
    // $name:系列名称
    // $xAxis:X轴数据
    // $data:系列数据
    // $type:图表系列类型
    // $absValue:是否取绝对值
    private function buildTrendSeriesData($name, $xAxis, $data, $type, $absValue = false)
    {
        $series = [
            'name' => $name,
            'type' => $type,
            'label' => [
                'show' => true,
                'position' => 'top',
                'formatter' => '{c}'
            ],
            'data' => []
        ];
        foreach ($xAxis as $axis) {
            $value = $data[$axis] ?? 0;
            $series['data'][] = $absValue ? abs($value) : $value;
        }
        return $series;
    }

    // 构建分类统计数据
    private function buildCategoryStatData($datalist, $title, $totalLabel)
    {
        $convertData = [];
        $allTotalAmount = abs(get_2arr_sum($datalist, 'total_amount'));
        $allTotalCount = abs(get_2arr_sum($datalist, 'total_count'));
        foreach ($datalist as $row) {
            $oneTotalAmount = abs($row['total_amount']);
            $percentage = $allTotalAmount > 0 ? round($oneTotalAmount / $allTotalAmount * 100, 2) : 0;

            $convertData[] = [
                'name' => $row['category_name'],
                'value' => $oneTotalAmount,
                'percentage' => $percentage,
                'label' => [
                    'normal' => [
                        'formatter' => "{b}\n\n￥{c}（{d}%）"
                    ]
                ]
            ];
        }
        $rtnData['title']['text'] = $title;
        $rtnData['title']['subtext'] = $totalLabel . ':￥' . abs($allTotalAmount) . ' 总笔数:' . $allTotalCount;
        $rtnData['series']['data'] = $convertData;
        return $rtnData;
    }

    //收入分类
    public function getCategoryIncomeStat($data = [])
    {
        //统计的时间段
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);
        if (empty($data['book_id'])) {
            $bookId = $this->bookId;
        } else {
            $bookId = $data['book_id'];
        }
        $datalist = $this->modelBkStat->getCategoryTransactionData($bookId, $start_time, $end_time, 1);

        return $this->buildCategoryStatData($datalist, '收入分类', '总收入');
    }

    //支出分类
    public function getCategoryExpenseStat($data = [])
    {
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        if (empty($data['book_id'])) {
            $bookId = $this->bookId;
        } else {
            $bookId = $data['book_id'];
        }
        $datalist = $this->modelBkStat->getCategoryTransactionData($bookId, $start_time, $end_time, -1);

        return $this->buildCategoryStatData($datalist, '支出分类', '总支出');

    }

    // 构建排行榜数据
    private function buildRankStatData($datalist, $title)
    {
        $yaxis_data = array_column($datalist, 'category_name');
        $money_data = array_column($datalist, 'total_amount');

        foreach ($money_data as $key => $value) {
            $money_data[$key] = abs($value);
        }
        // 按数值从高到低排序，同时保持键值对应关系
        array_multisort($money_data, SORT_ASC, $yaxis_data);
        $rtnData['title']['text'] = '';
        $rtnData['yAxis'] = $yaxis_data;
        $tmp = [
            'name' => $title,
            'type' => 'bar',
            'label' => [
                'show' => true,
                'position' => 'right',
                'valueAnimation' => true,
                'formatter' => '{c} 元'
            ],
            'data' => $money_data
        ];
        $rtnData['series'][] = $tmp;
        return $rtnData;
    }

    // 收入排行
    public function getCategoryIncomeRankStat($data = [])
    {
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);
        if (empty($data['book_id'])) {
            $bookId = $this->bookId;
        } else {
            $bookId = $data['book_id'];
        }
        $datalist = $this->modelBkStat->getCategoryTransactionData($bookId, $start_time, $end_time, 1);

        return $this->buildRankStatData($datalist, '收入排名');
    }

    // 支出排行
    public function getCategoryExpenseRankStat($data = [])
    {
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);
        if (empty($data['book_id'])) {
            $bookId = $this->bookId;
        } else {
            $bookId = $data['book_id'];
        }
        $datalist = $this->modelBkStat->getCategoryTransactionData($bookId, $start_time, $end_time, -1);

        return $this->buildRankStatData($datalist, '支出排名');
    }


    //成员分类=》收入
    public function getPersonIncomeStat($data = [])
    {
        //统计的时间段
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        $datalist = $this->modelBkStat->getPersonTransactionData($start_time, $end_time, 1);

        return $this->buildCategoryStatData($datalist, '成员收入', '总收入');
    }

    //成员分类=》支出
    public function getPersonExpenseStat($data = [])
    {
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        $datalist = $this->modelBkStat->getPersonTransactionData($start_time, $end_time, -1);

        return $this->buildCategoryStatData($datalist, '成员支出', '总支出');

    }

    //项目分类=》收入
    public function getProjectIncomeStat($data = [])
    {
        //统计的时间段
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        $datalist = $this->modelBkStat->getProjectTransactionData($start_time, $end_time, 1);

        return $this->buildCategoryStatData($datalist, '项目收入', '总收入');
    }

    //项目分类=》支出
    public function getProjectExpenseStat($data = [])
    {
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        $datalist = $this->modelBkStat->getProjectTransactionData($start_time, $end_time, -1);

        return $this->buildCategoryStatData($datalist, '项目支出', '总支出');

    }

    //商家分类=》收入
    public function getShopIncomeStat($data = [])
    {
        //统计的时间段
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        $datalist = $this->modelBkStat->getShopTransactionData($start_time, $end_time, 1);

        return $this->buildCategoryStatData($datalist, '商家收入', '总收入');
    }

    //商家分类=》支出
    public function getShopExpenseStat($data = [])
    {
        list($start_time, $end_time) = $this->getPeriodTypeDateRange($data);

        $datalist = $this->modelBkStat->getShopTransactionData($start_time, $end_time, -1);

        return $this->buildCategoryStatData($datalist, '商家支出', '总支出');

    }
}
