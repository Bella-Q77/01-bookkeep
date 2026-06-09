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
namespace app\bookkeep\model;

/**
 * 收支单项目明细=》模型
 */
class BkCategory extends BookkeepBase
{

    // 添加结算账户明细
    public function addOtherBillItem($billId, $datalist = [])
    {
        foreach ($datalist as $key => $row) {
            $intoDataList[] = [
                'other_bill_id' => $billId,
                'item_id' => $row["item_id"],
                'item_name' => $row["item_name"],
                'money' => $row["money"],
                'remark' => $row["remark"],
            ];
        }
        $this->modelFinOtherBillItem->deleteInfo(['other_bill_id' => $billId], true);//先删除，后添加
        $this->modelFinOtherBillItem->setList($intoDataList);
    }
}
