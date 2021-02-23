<?php
// 授权管理控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\logic;

use app\admin\model\AuthRule as AuthRuleModel;
use app\admin\model\AuthGroup as AuthGroupModel;
use app\admin\model\AuthGroupAccess as AuthGroupAccessModel;
use app\admin\model\AdminUser as AdminUserModel;

use eacoo\Tree;

class Typetree extends AdminLogic {


    protected function initialize()
    {
        parent::initialize();

    }
    
    /**
     * 后台菜单管理(规则)
     * @return [type] [description]
     */
    public function getAdminMenu($model=null,$map=array()){

        $menus = db($model)->where($map)->order('sort asc,id desc')->select();

        $menus = collection($menus)->toArray();

        $tree_obj = new Tree;
        return $menus = $tree_obj->toFormatTree($menus,'title');
    }




}