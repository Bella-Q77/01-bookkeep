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
namespace app\portalmember\controller;


/**
 * 会员等级中心=》首页
 */
class MemberProductLevel extends PortalmemberBaseAuth
{


    /**
     * 会员等级提交
     */
    public function show()
    {
        if (IS_POST) {
            $list = $this->logicMemberProductLevel->getMemberProductLevelList();
            return $list;
        }
        return $this->fetch('show');
    }

    /**
     * 积分购买
     */
    public function buy()
    {
        IS_GET && $this->jump($this->logicMemberProductLevel->memberProductLevelBuy($this->param));
    }

    /**
     * 实名说明文件
     */
    public function rules()
    {
        $list=[
            ['name'=>'初始注册激活','integral'=>'+30','remark'=>'完善会员宣传资料'],
            ['name'=>'首次绑定微信登录','integral'=>'+10','remark'=>'解绑需扣除相应积分'],
            ['name'=>'每24小时登录一次','integral'=>'+1','remark'=>'绑定QQ/微信登录'],
            ['name'=>'发布新闻稿通过审核','integral'=>'+1','remark'=>'完善会员宣传资料'],
            ['name'=>'加客服微信为好友','integral'=>'+15','remark'=>'添加客服微信为好友'],
            ['name'=>'信息违规未通过审核','integral'=>'-1','remark'=>'以审核记录为准'],
            ['name'=>'发布条目超限，增发信息','integral'=>'-2','remark'=>'以系统显示为准'],
            ['name'=>'信息违规被审核删除','integral'=>'-2','remark'=>'以审核记录为准'],
            ['name'=>'修改账号所在省级地区','integral'=>'-30','remark'=>'已认证账号无法修改'],
            ['name'=>'修改账号已绑定手机','integral'=>'-20','remark'=>'每60天只能修改一次'],
            ['name'=>'修改账号已绑定邮箱','integral'=>'-20','remark'=>'每60天只能修改一次'],
            ['name'=>'实名认证/次','integral'=>'+20','remark'=>'通过审核后赠送'],
            ['name'=>'批量刷新','integral'=>'-1','remark'=>'需积分达30分使用此功能(VIP不扣积分)'],
            ['name'=>'修改课程信息','integral'=>'-1','remark'=>'第一次修改或者未审核不扣积分(VIP不扣积分)'],
            ['name'=>'查看学员报名/条','integral'=>'-5','remark'=>'VIP不扣积分'],
            ['name'=>'学员报名短信通知','integral'=>'-1','remark'=>'VIP不扣积分'],
            ['name'=>'找学员','integral'=>'-2起','remark'=>'2积分以上的VIP优惠1积分，视具体信息而定'],
        ];

        $this->assign('list', $list);

        return $this->fetch('member_product_integral_rules');
    }

}
?>