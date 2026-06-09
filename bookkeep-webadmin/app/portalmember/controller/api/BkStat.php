<?php
// +----------------------------------------------------------------------
// | 07FLYERP [基于ThinkPHP5.0开发]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2021 http://www.07fly.xyz
// +----------------------------------------------------------------------
// | Professional because of focus  Persevering because of happiness
// +----------------------------------------------------------------------
// | Author: 开发人生 <goodkfrs@qq.com>
// +----------------------------------------------------------------------

namespace app\portalmember\controller\api;

use think\Hook;

/**
 * 账本控制器
 */
class BkStat extends PortalmemberBookkeepApiBase
{
    // 获取当前账本信息
    public function getStatInfo()
    {
        $rtnArray = ['code' => 1, 'msg' => 'success', 'data' => []];
        if (!empty($this->param['listtype'])) {
            switch ($this->param['listtype']) {
                case 'statmonth':
                    $rtnArray['data'] = $this->logicBkStat->getStatMonthTotal($this->param);
                    break;
                case 'budgetuse':
                    $rtnArray['data'] = $this->logicBkStat->getBudgetUseStat($this->param);
                    break;
                case 'incomeexpensetrend':
                    $rtnArray['data'] = $this->logicBkStat->getIncomeExpenseTrendStat($this->param);
                    break;
                case 'categoryincome':
                    $rtnArray['data'] = $this->logicBkStat->getCategoryIncomeStat($this->param);
                    break;
                case 'categoryincomerank':
                    $rtnArray['data'] = $this->logicBkStat->getCategoryIncomeRankStat($this->param);
                    break;
                case 'categoryexpense':
                    $rtnArray['data'] = $this->logicBkStat->getCategoryExpenseStat($this->param);
                    break;
                case 'categoryexpenserank':
                    $rtnArray['data'] = $this->logicBkStat->getCategoryExpenseRankStat($this->param);
                    break;
                case 'personincome':
                    $rtnArray['data'] = $this->logicBkStat->getPersonIncomeStat($this->param);
                    break;
                case 'personexpense':
                    $rtnArray['data'] = $this->logicBkStat->getPersonExpenseStat($this->param);
                    break;
                case 'projectincome':
                    $rtnArray['data'] = $this->logicBkStat->getProjectIncomeStat($this->param);
                    break;
                case 'projectexpense':
                    $rtnArray['data'] = $this->logicBkStat->getProjectExpenseStat($this->param);
                    break;
                case 'shopincome':
                    $rtnArray['data'] = $this->logicBkStat->getShopIncomeStat($this->param);
                    break;
                case 'shopexpense':
                    $rtnArray['data'] = $this->logicBkStat->getShopExpenseStat($this->param);
                    break;
                default:
                    $rtnArray['data'] = [];
                    $rtnArray['code'] = 0;
                    $rtnArray['msg'] = '列表类型错误';
                    break;
            }
        } else {
            $rtnArray['data'] = [];
            $rtnArray['code'] = 0;
            $rtnArray['msg'] = '列表类型错误';
        }
        return $this->apiReturn($rtnArray);
    }

    public function getInfo()
    {
        $info = $this->logicBkBook->getBkBookInfo(['id' => $this->param['id']]);
        return $this->apiReturn($info);
    }

}
