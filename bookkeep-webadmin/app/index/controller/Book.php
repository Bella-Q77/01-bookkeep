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

class Book extends IndexBase
{

    public $bookid = '';
    public $chapid = '';

	/**
	 * 构造方法
	 */
	public function __construct()
	{
		// 执行父类构造方法
		parent::__construct();

		$booklist=$this->logicBook->getBookList();
		$this->assign('booklist', $booklist);


	}

    /**
     * 文档展示
     *
     * @return mixed
     * created by Administrator at 2020/2/24 0024 15:15
     */
    public function read($bookid = '')
    {
        $this->bookid = input("param.bookid", '0');
        $this->chapid = input("param.chapid", '0');
//        if (!is_numeric($this->bookid) || strval(intval($this->bookid)) !== strval($this->bookid)) {
//            abort(404, 'bookid 不存在');
//        }

		if (!is_numeric($this->bookid) || strval(intval($this->bookid)) !== strval($this->bookid)) {
			$map_book = array('pinyin' => $this->bookid);
		} else {
			$map_book = array('id' => $this->bookid);
		}

        if (empty($this->bookid)) {
            abort(404, 'aid 页面不存在');
            exit;
        } else {
            /**文档处理**/
            $bookinfo = $this->logicBook->getBookInfo($map_book);
            if (empty($bookinfo)) {
                abort(404, 'bookid 页面不存在');
                exit;
            }


            // 文章列表
            $chap_list = $this->logicBook->getBookChapList(['book_id' => $bookinfo['id']]);

            //得到文档树
            $chapmenu=$this->chap_list_tree_html($bookinfo['id'],$bookinfo['pinyin']);

            $map_chap['book_id']=['=',$bookinfo['id']];
            $this->chapid = intval($this->chapid);
            if(!empty($this->chapid)){
                $map_chap['id']=['=',$this->chapid];
            }
            $chapinfo=$this->logicBook->getBookChapInfo($map_chap);


            //未传入章节ID时
            if(empty($this->chapid)){
				//更新文档的浏览数据
				$this->logicBook->setBookClick($map_book);
			}else{
				//更新文章浏览量
				$this->logicBook->setBookChapClick($map_chap);
			}

            $pajx = input("param.pajx", '0');
            if(!empty($pajx)){
                return $chapinfo;
                exit;
            }

            $rtnArray = array(
                'chapmenu' => $chapmenu,
                'chap_list' => $chap_list,
                'bookinfo' => $bookinfo,
                'chapinfo' => $chapinfo,
                'chapid' => $this->chapid,
            );

            /*模板文件*/
            if(empty($tpfile)){
                $tpfile = 'book_read.html';
            }
            $viewfile = !empty($tpfile) ? strtolower($tpfile) : $tpfile;
            $this->typeinfo = $rtnArray;
            $this->assign('fly', $this->typeinfo);
            return $this->fetch($viewfile);

        }
    }


	/**
	 * 文档展示
	 *
	 * @return mixed
	 * created by Administrator at 2020/2/24 0024 15:15
	 */
	public function readChap($bookid = '')
	{
		$this->bookid = input("param.bookid", '0');
		$this->chapid = input("param.chapid", '0');
		if (empty($this->bookid)) {
			abort(404, 'aid 页面不存在');
			exit;
		} else {

			$map_chap=[];
			$this->chapid = intval($this->chapid);
			if(!empty($this->chapid)){
				$map_chap['id']=['=',$this->chapid];
			}
			$chapinfo=$this->logicBook->getBookChapInfo($map_chap);
			$pajx = input("param.pajx", '0');
			if(!empty($pajx)){
				return $chapinfo;
				exit;
			}
		}
	}

    //左边栏目输出
    public function chap_list_tree_html($bookid,$bookpinyin=''){
        // 文章列表
        $chap_list = $this->logicBook->getBookChapList(['book_id' => $bookid],'id,book_id,pid,title','sort asc');
        foreach ($chap_list as &$row){
        	$row['bookid']=empty($bookpinyin)?$row['id']:$bookpinyin;
		}
        $tree_list  = list2tree($chap_list,0,0,'id','pid','title');

        $html ="<ul>";
        $html .=$this->chap_list_tree_to($tree_list);
        $html .="</ul>";
        return $html;
    }

    public function chap_list_tree_to($list){
        $html ='';
        foreach ($list as $key=>$row){
            $url=url('index/book/read',array('bookid'=>$row['bookid'],'chapid'=>$row['id']));
            if(empty($row['nodes'])){
                $html .="<li data-id='".$row['id']."'>";
                $html .='<span><i></i></span><a href="'.$url.'">'.$row['title'].'</a>';
                $html .="</li>";
            }else{
                $html .='<li class="has_child">';
                $html .='<span><i class="icon-plus-sign"></i></span><a href="'.$url.'">'.$row['title'].'</a>';
                $html .="<ul style='display: none;'>";
                $html .=$this->chap_list_tree_to($row['nodes']);
                $html .="</ul>";
                $html .="</li>";
            }
        }
        return $html;
    }


	/**
	 * 后台调用方法，可以配合路由配置
	 * @return mixed
	 * Author: kfrs <goodkfrs@QQ.com> created by at 2020/11/2 0002
	 */
	public function adminread()
	{
		return $this->read($this->param);
	}

}
