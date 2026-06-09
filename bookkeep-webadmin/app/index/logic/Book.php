<?php
/**
 * 零起飞-(07FLY-CRM)
 * ==============================================
 * 版权所有 2015-2028   成都零起飞网络，并保留所有权利。
 * 网站地址: http://www.07fly.xyz
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ==============================================
 * Channelor: kfrs <goodkfrs@QQ.com> 574249366
 * Date: 2019-10-3
 */

namespace app\index\logic;
use \think\Db;
/**
 * 频道栏目管理=》逻辑层
 */
class Book extends IndexBase
{

    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getBookList($where = [], $field = '', $order = '', $paginate = false,$limit='')
    {
        $this->modelBook->alias('a');
        $list= $this->modelBook->getList($where, $field, $order, $paginate);
        foreach ($list as &$row){
            $row['litpic'] =get_picture_url($row['litpic']);
			$row['bookid']=empty($row['pinyin'])?$row['id']:$row['pinyin'];
            $row['bookurl'] = url('index/book/read',array('bookid'=>$row['bookid']));
        }

        return $list;
    }

    /**信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBookInfo($where = [], $field = true)
    {
        $info=$this->modelBook->getInfo($where, $field);
		$info['bookurl']=$this->getBookUrl($info);
		return $info;
    }


	/**返回文档链接地址
	 * @param array $data
	 * @return string
	 * Author: 开发人生 goodkfrs@qq.com
	 * Date: 2021/5/7 0007 14:59
	 */
	public function getBookUrl($data=[]){
    	$url='';
		$data['bookid']=empty($data['pinyin'])?$data['id']:$data['pinyin'];
		$url=url('index/book/read',array('bookid'=>$data['bookid']));
		return $url;
	}


    /**文章列表查询
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * Author: lingqifei created by at 2020/2/27 0027
     */
    public function getBookChapList($where = [], $field = true, $order = '', $paginate = 15)
    {
        $list=Db::name('book_chap')
            ->where($where)
            ->field($field)
            ->order('sort asc,id asc')
            ->select();
        return $list;
    }

    /**信息
     * @param array $where
     * @param bool $field
     * @return
     */
    public function getBookChapInfo($where = [], $field = true)
    {
        return $this->modelBookChap->getInfo($where, $field);
    }


	/**设置文档点击
	 * @param array $data
	 * @return mixed|string
	 * Author: lingqifei created by at 2020/2/27 0027
	 */
	public function setBookClick($where = [])
	{
		Db::name('book')->where($where)->setInc('click_a','1');
	}

	/**设置文档的文章点击
	 * @param array $data
	 * @return mixed|string
	 * Author: lingqifei created by at 2020/2/27 0027
	 */
	public function setBookChapClick($where = [])
	{
		Db::name('book_chap')->where($where)->setInc('click','1');
	}

}
