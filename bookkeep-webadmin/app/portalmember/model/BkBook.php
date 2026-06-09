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

/**
 * 账本明细=》模型
 */
class BkBook extends PortalmemberBase
{
    public function regInitBookCategory($memberId)
    {
        $where['is_default'] = 1;
        $where['is_template'] = 1;
        $defBook = $this->getInfo($where, 'id,name');
        if (!empty($defBook)) {

            // 初始化账本
            $initBook = [
                'member_id' => $memberId,
                'name' => $defBook['name'],
                'is_default' => 1
            ];
            $newBookId = $this->setInfo($initBook);

            // 初始化分类
            $cateList = $this->modelBkCategory->getList(['book_id' => $defBook['id']], 'id,name,parent_id,category_icon,type,sort');
            $cateData = [];
            foreach ($cateList as $row) {
                $cateData[] = [
                    'member_id' => $memberId,
                    'book_id' => $newBookId,
                    'name' => $row['name'],
                    'parent_id' => $row['parent_id'],
                    'category_icon' => $row['category_icon'],
                    'type' => $row['type'],
                    'sort' => $row['sort']
                ];
            }
            $this->modelBkCategory->setList($cateData);


            // 初始化账户银行
            $accData = [];
            $accTypeList = $this->modelBkAccount->account_type();
            foreach ($accTypeList as $key => $row) {
                $accData[] = [
                    'member_id' => $memberId,
                    'book_id' => $newBookId,
                    'name' => $row['name'],
                    'type' => $key,
                    'account_type' => $key,
                    'is_debt' => $row['is_debt']
                ];
            }
            $this->modelBkAccount->setList($accData);
        }
    }
}