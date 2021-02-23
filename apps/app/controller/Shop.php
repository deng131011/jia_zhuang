<?php

namespace app\app\controller;
use app\app\model\Shop as ShopModel;
class Shop extends Home {

    /**
     *商家列表
     */
    public function shop_list(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->shop_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *商家详情
     */
    public function details(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->details($post);
        $this->appReturn($result);
    }

    /**
     *商家推荐列表
     */
    public function shop_tjlist(){
        $post = $this->post;
        if(empty($post['shop_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $ShopModel = new ShopModel();
        $result = $ShopModel->shop_tjlist($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *商家菜单分类
     */
    public function menu_type(){
        $post = $this->post;
        if(empty($post['shop_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $ShopModel = new ShopModel();
        $list = $ShopModel->menu_type($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$list));
    }

    /**
     *商家菜单列表
     */
    public function menu_list(){
        $post = $this->post;
        if(empty($post['shop_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $ShopModel = new ShopModel();
        $list = $ShopModel->menu_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$list));
    }

    /**
     *提交预约订单
     */
    public function add_order(){
        $post = $this->post;
        if(empty($post['shop_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $ShopModel = new ShopModel();
        $res = $ShopModel->add_order($post);
        $this->appReturn($res);
    }

    /**
     *商家订单详情
     */
    public function order_details(){
        $post = $this->post;
        if(empty($post['order_number']) || empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $ShopModel = new ShopModel();
        $result = $ShopModel->order_details($post);
        $result['hexiao_tishi'] = config('hexiao_tishi_info'); //提示语
        $result['hexiao_rule'] = config('hexiao_rule_info'); //核销规则
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }



    /**
     *地图地名
     */
    public function map_name(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->map_name($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
     *日程列表
     */
    public function days_list(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->days_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
     *日程详情
     */
    public function days_details(){
        $post = $this->post;
        if(empty($post['list_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $ShopModel = new ShopModel();
        $result = $ShopModel->days_details($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *客服热线分类
     */
    public function kefu_type(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->kefu_type($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
     *客服热线列表
     */
    public function kefu_list(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->kefu_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
     *服务首页
     */
    public function fuwu_index(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->fuwu_index($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }



    /******************************  商家端 *********************************/

    /**
     *商家入驻广告
     */
    public function ruzhu_ad(){
        //$post = $this->post;
        $result['ruzhu_ad'] = getAdImages(2,'',1);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *商家入驻资料提交
     */
    public function ruzhu_add(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->ruzhu_add($post);
        $this->appReturn($result);
    }

    /**
     *商家申请详情
     */
    public function apply_details(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->apply_details($post);
        $this->appReturn($result);
    }

    /**
     *撤销商家申请
     */
    public function cancel_apply(){
        $post = $this->post;
        $ShopModel = new ShopModel();
        $result = $ShopModel->cancel_apply($post);
        $this->appReturn($result);
    }



}