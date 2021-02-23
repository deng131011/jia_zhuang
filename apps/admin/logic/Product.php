<?php

namespace app\admin\logic;

use app\admin\model\AuthRule as AuthRuleModel;
use app\admin\model\AuthGroup as AuthGroupModel;
use app\admin\model\AuthGroupAccess as AuthGroupAccessModel;
use app\admin\model\AdminUser as AdminUserModel;

use eacoo\Tree;

class Product extends AdminLogic {


    protected function initialize()
    {
        parent::initialize();

    }
    
    /**
     * 商品分类
     */
    public function producttype($pid = 1){
        $where['pid']    = array('eq',$pid);
        $where['status'] = array('eq',1);
        $list = db('producttype')->where($where)->order('sort asc')->select();
        return $list;
    }


    /**
     * 商品标签
     */
    public function tab_list($pid = 0){
        $where['pid']    = array('eq',1);
        $where['status'] = array('eq',1);
        $list = db('category')->where($where)->order('sort asc')->select();
        return $list;
    }




}