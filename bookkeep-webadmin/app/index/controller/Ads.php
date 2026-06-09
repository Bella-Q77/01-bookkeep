<?php
/**
 * 零起飞07FLY-CMS
 * ============================================================================
 * 版权所有 2018-2028 成都零起飞科技有限公司，并保留所有权利。
 * 网站地址: http://www.07fly.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 开发人生 <goodkfrs@qq.com>
 * Date: 2021-01-01-3
 */

namespace app\index\controller;

use think\Controller;

class Ads extends IndexBase
{

    public $aid = '';
    public $type = '';

    /**
     * 广告位调用
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function v($aid = '')
    {
        $this->aid = input("param.aid", '0');
        if (!is_numeric($this->aid) || strval(intval($this->aid)) !== strval($this->aid)) {
            abort(404, 'aid页面不存在');
        }
        $this->aid = intval($this->aid);
        if (empty($this->aid)) {
            abort(404, 'aid 页面不存在');
            exit;
        } else {
            /**广告位处理**/
            $info = $this->logicAds->getAdsInfo(['id' => $this->aid]);
            if (empty($info)) {
                abort(404, 'info 页面不存在');
                exit;
            }

            //广告列表
            $list = $this->logicAdsList->getAdsListList(['ads_id' => $this->aid]);

            if (!empty($list['data'])) {

                $one = array_rand_value($list['data'], 1);//随机选择一条广告，随机数

                if (!empty($one[0])) {
                    $ads = $one[0];
                    //更新点击
                    $this->logicAdsList->updateAdsListView(['id' => $ads['id']]);

                    $text = '';
                    $gotolink = DOMAIN .url('index/Ads/gotolink',array('id'=>$ads['id']));
                    switch ($ads['ads_type']) {
                        case '0':
                            $text = '<a href="' . $gotolink . '" ' . $ads['target'] . '>
                            <img src="' . DOMAIN . '' . $ads['litpic'] . '" width="' . $info['width'] . '" height="' . $info['height'] . '">
                            </a>';
                            break;
                        case '1':
                            $text = '<a href="' . $gotolink . '" ' . $ads['target'] . '>' . $ads['intro'] . '</a>';
                            break;
                        case '2':
                            $text = $ads['intro'];
                            break;
                    }
                    if ($ads['set_time'] == 0) {
                        $adbody = $text;
                    } else {
                        $ntime = time();
                        if ($ntime > $ads['stop_time'] || $ntime < $ads['start_time']) {
                            $adbody = $ads['expintro'];
                        } else {
                            $adbody = $text;
                        }
                    }
                    $adbody = str_replace('"', '\"', $adbody);
                    $adbody = str_replace("\r", "\\r", $adbody);
                    $adbody = str_replace("\n", "\\n", $adbody);
                    $adbody = "<!--\r\ndocument.write(\"{$adbody}\");\r\n-->\r\n";
                }
                echo $adbody;
            }
        }
    }

    /**
     * 单广调用
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function p($aid = '')
    {
        $this->aid = input("param.aid", '0');
        if (!is_numeric($this->aid) || strval(intval($this->aid)) !== strval($this->aid)) {
            abort(404, 'aid页面不存在');
        }
        $this->aid = intval($this->aid);
        if (empty($this->aid)) {
            abort(404, 'aid 页面不存在');
            exit;
        } else {
            /**广告信息**/
            $info = $this->logicAdsList->getAdsListInfo(['id' => $this->aid]);
            if (empty($info)) {
                abort(404, 'info 页面不存在');
                exit;
            }

            $text = '';
            switch ($info['ads_type']) {
                case '0':
                    $text = '<a href="' . $info['links'] . '" ' . $info['target'] . '><img src="' . get_picture_url($info['litpic']) . '"></a>';
                    break;
                case '1':
                    $text = '<a href="' . $info['links'] . '" ' . $info['target'] . '>' . $info['intro'] . '</a>';
                    break;
                case '2':
                    $text = $info['intro'];
                    break;
            }
            //时间设置
            if ($info['set_time'] == 0) {
                $adbody = $text;
            } else {
                $ntime = time();
                if ($ntime > $info['stop_time'] || $ntime < $info['start_time']) {
                    $adbody = $info['expintro'];
                } else {
                    $adbody = $text;
                }
            }
            $adbody = str_replace('"', '\"', $adbody);
            $adbody = str_replace("\r", "\\r", $adbody);
            $adbody = str_replace("\n", "\\n", $adbody);
            $adbody = "<!--\r\ndocument.write(\"{$adbody}\");\r\n-->\r\n";
        }
        echo $adbody;

        //更新点击
        $this->logicAdsList->updateAdsListView(['id' => $info['id']]);

    }

    /**
     * 会员广告位调用
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function madv($aid = '')
    {
        $this->aid = input("param.aid", '0');
        if (!is_numeric($this->aid) || strval(intval($this->aid)) !== strval($this->aid)) {
            abort(404, 'mem ads aid 不存在');
        }
        $this->aid = intval($this->aid);
        if (empty($this->aid)) {
            abort(404, 'mem ads aid 不存在');
            exit;
        } else {
            /**广告位处理**/
            $adv = $this->logicMemberAdv->getMemberAdvInfo(['id' => $this->aid]);

            if (empty($adv)) {
                abort(404, 'mem adv  页面不存在');
                exit;
            }
            //会员广告列表
            $ads = $this->logicMemberAdv->getMemberAdvDis(['adv_id' => $this->aid]);

            if (!empty($ads)) {
                //更新浏览
                $this->logicMemberAdvDis->updateMemberAdvDisView(['id' => $ads['id']]);

                // d($ads->toArray());exit;

                $text = '';
                switch ($adv['ad_type']) {
                    case '0':
                        $text = '<a href="' . $ads['links'] . '" ><img src="' . get_picture_url($ads['litpic']) . '" width="' . $adv['width'] . '" height="' . $adv['height'] . '"></a>';
                        break;
                    case '1':
                        $text = '<a href="' . $ads['links'] . '" ' . $ads['target'] . '>' . $ads['body'] . '</a>';
                        break;
                    case '2':
                        $text = $ads['body'];
                        break;
                }
                $ntime = format_time(time(), 'Y-m-d');
                if ($ntime < $ads['start_date'] || $ntime > $ads['stop_date']) {
                    $adbody = $adv['body'];
                } else {
                    $adbody = $text;
                }

                $adbody = str_replace('"', '\"', $adbody);
                $adbody = str_replace("\r", "\\r", $adbody);
                $adbody = str_replace("\n", "\\n", $adbody);
                $adbody = "<!--\r\ndocument.write(\"{$adbody}\");\r\n-->\r\n";
            } else {
                $adbody = '<a href="' . $adv['links'] . '" ><img src="' . get_picture_url($adv['litpic']) . '" width="' . $adv['width'] . '" height="' . $adv['height'] . '"></a>';
                $adbody = str_replace('"', '\"', $adbody);
                $adbody = str_replace("\r", "\\r", $adbody);
                $adbody = str_replace("\n", "\\n", $adbody);
                $adbody = "<!--\r\ndocument.write(\"{$adbody}\");\r\n-->\r\n";
            }
            echo $adbody;
        }
    }

    /**
     * 会员广告位跳转
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function gotolink($id = '')
    {
        $this->id = input("param.id", '0');
        if (!is_numeric($this->id) || strval(intval($this->id)) !== strval($this->id)) {
            abort(404, 'mem ads id 不存在');
        }
        $this->id = intval($this->id);
        if (empty($this->id)) {
            abort(404, 'mem ads id 不存在');
            exit;
        } else {
            /**广告信息**/
            $info = $this->logicAdsList->getAdsListInfo(['id' => $this->id]);
            if (empty($info)) {
                abort(404, 'info 页面不存在');
                exit;
            }
            //更新点击
            $this->logicAdsList->updateAdsListClick(['id' => $info['id']]);
            //根据地址跳转
            if ($info['links'] != '') {
                $this->redirect($info['links']);
            } else {
                $this->redirect('/');
            }
        }
    }
}
