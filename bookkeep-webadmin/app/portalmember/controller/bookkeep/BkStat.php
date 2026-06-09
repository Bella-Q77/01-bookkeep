<?php
/*
*
* 零起飞进销系统（07FLY-DMS）
*
* ----------------------------------------------
* 零起飞网络 - 专注于企业管理系统开发
* 以质量求生存，以服务谋发展，以信誉创品牌 !
* ----------------------------------------------
* @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD All rights reserved.
* @license    For licensing, see LICENSE.html
* @author ：kfrs <goodkfrs@QQ.com> 574249366
* @version ：1.0
* @link ：http://www.07fly.xyz
*/

namespace app\portalmember\controller\bookkeep;

/**
 * 商家管理-控制器
 */
class BkStat extends PortalmemberBookkeepAuth
{

    public function __construct()
    {
        parent::__construct();
        $currMonth = getMonthStartEndTime();
        $rangedate=format_time($currMonth['begin'], 'Y/m/d').'-'.format_time($currMonth['end'], 'Y/m/d');
        $this->assign('rangedate', $rangedate);
    }

    /**
     * 工作台列表=》模板
     * @return mixed|string
     */
    public function index()
    {
        if (!empty($this->param['listtype'])) {
            $rtnArray=['code'=>1,'msg'=>'success','data'=>[]];
            switch ($this->param['listtype']) {
                case 'statmonth':
                    $rtnArray['data']= $this->logicBkStat->getStatMonthTotal($this->param);
                    break;
                case 'budgetuse':
                    $rtnArray['data']= $this->logicBkStat->getBudgetUseStat($this->param);
                    break;
                case 'incomeexpensetrend':
                    $rtnArray['data']=  $this->logicBkStat->getIncomeExpenseTrendStat($this->param);
                    break;
                case 'categoryincome':
                    $rtnArray['data']=  $this->logicBkStat->getCategoryIncomeStat($this->param);
                    break;
                case 'categoryincomerank':
                    $rtnArray['data']=  $this->logicBkStat->getCategoryIncomeRankStat($this->param);
                    break;
                case 'categoryexpense':
                    $rtnArray['data']=  $this->logicBkStat->getCategoryExpenseStat($this->param);
                    break;
                case 'categoryexpenserank':
                    $rtnArray['data']=  $this->logicBkStat->getCategoryExpenseRankStat($this->param);
                    break;
                case 'personincome':
                    $rtnArray['data']=  $this->logicBkStat->getPersonIncomeStat($this->param);
                    break;
                case 'personexpense':
                    $rtnArray['data']=  $this->logicBkStat->getPersonExpenseStat($this->param);
                    break;
                case 'projectincome':
                    $rtnArray['data']=  $this->logicBkStat->getProjectIncomeStat($this->param);
                    break;
                case 'projectexpense':
                    $rtnArray['data']=  $this->logicBkStat->getProjectExpenseStat($this->param);
                    break;
                case 'shopincome':
                    $rtnArray['data']=  $this->logicBkStat->getShopIncomeStat($this->param);
                    break;
                case 'shopexpense':
                    $rtnArray['data']=  $this->logicBkStat->getShopExpenseStat($this->param);
                    break;
                default:
                    $rtnArray['data']=  [];
                    $rtnArray['code']=  0;
                    $rtnArray['msg']= '列表类型错误';
                    break;
            }
            return json($rtnArray);
        }
        return $this->fetch('index');
    }

    /**
     * 收支分析=》模板
     * @return mixed|string
     */
    public function transaction()
    {
        return $this->fetch('transaction');
    }

    public function trend()
    {
        return $this->fetch('trend');
    }

    public function person()
    {
        return $this->fetch('person');
    }
    public function project()
    {
        return $this->fetch('project');
    }

    public function shop()
    {
        return $this->fetch('shop');
    }
}
