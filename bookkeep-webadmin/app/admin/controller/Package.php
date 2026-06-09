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

namespace app\admin\controller;
use think\db;
/**
 * 模块控制器
 */
class Package extends AdminBase
{
    /**
     * 模块列表
     */
    public function show()
    {
        return  $this->fetch('show');
    }

    /**
     * 显示所在的模块
     * @return mixed
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/1/5 0005 18:00
     */
    public function show_json()
    {
        $where = [];
        if(!empty($this->param['keywords'])){
           $where['name|title|intro|author']=['like','%'.$this->param['keywords'].'%'];
        }
        $list=$this->logicSysModule->getSysModuleList($where,'','sort asc',20);
        return $list;
    }

    /**
     * 模块打包
     */
    public function addpack()
    {
        //$this->jump($this->logicPackage->createPack($this->param));

        IS_POST && $this->jump($this->logicPackage->createPack($this->param));

        $info = $this->logicSysModule->getSysModuleInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('addpack');
    }

	/**
	 * 系统框架打包
	 */
	public function addsys()
	{
        //IS_POST && $this->jump([RESULT_SUCCESS, '打包成功', '', 'test']);
        IS_POST && $this->jump($this->logicPackage->createSys($this->param));

        $this->addsysfile();
        $version=$this->logicUpgrade->getVersion();
        $this->assign('version', $version);
        return $this->fetch('addsys');
	}


    /**
     * 打包升级的文件列表
     * Author: 开发人生 goodkfrs@qq.com
     * Date: 2022/5/25 0025 17:19
     */
    public function addsysfile(){
        //2、升级包=移动需要打包的文件
        $handle_list = [
            'addon',
            'extend/lqf',
            'app/admin',
            'app/common',
            'app/lang',
            'app/command.php',
            'app/common.php',
            'app/extend.php',
            'app/function.php',
            'app/tags.php',
            'public/static/img',
            'public/static/addon/icon',
            'public/static/module/admin/css/07fly.css',
            'public/static/module/admin/css/style.css',
            'public/static/module/admin/js/lib',
            'public/static/module/admin/img',
        ];

        //2、安装包=移动需要打包的文件
        $handle_list_intsll = [
            'core',
            'addon',
            'extend',
            'vendor',
            'app/install',
            'app/config.php',
            'public/static/img',
            'public/static/addon/editor',
            'public/static/addon/file',
            'public/static/addon/region',
            'public/static/addon/icon',
            'public/static/module/admin',
            'public/static/module/install',
            'public/static/module/login',
            'public/index.php',
            'public/admin.php',
            'public/install.php',
            'public/router.php',
            'public/public.php',
        ];
        $handle_list=arr2str($handle_list,"\r\n");
        $handle_list_intsll=arr2str($handle_list_intsll,"\r\n");
        $this->assign('upgrade_list', $handle_list);
        $this->assign('install_list', $handle_list_intsll);
    }
}
