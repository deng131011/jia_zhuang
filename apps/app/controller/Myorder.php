<?php

namespace app\app\controller;
use think\Db;

/**
*我的订单
 */
class Myorder extends Home {

    /**
     *取消预约单
     **/
    public function cancel_yuyue(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Myorder')->cancel_yuyue($post);
        $this->appReturn($result);
    }


    /**
     *商品订单列表
     **/
    public function order_list(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Myorder')->order_list($post);
        $this->appReturn($result);
    }


    /**
     *取消订单
     **/
    public function deleteOrder(){
        $post = $this->post;

        $result = model('Myorder')->deleteOrder($post);
        $this->appReturn($result);
    }
	
	/**
     *删除商品订单
     **/
    public function delOrder(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Myorder')->delOrder($post);
        $this->appReturn($result);
    }


    /**
     *普通订单详情
     **/
    public function orderDetails(){
        $post = $this->post;

        if(empty($post['uid']) || empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        if(!empty($post['after_type']) && $post['after_type']==1){
            $result = model('Myorder')->afterDetails($post); //售后订单详情
        }else{
            $result = model('Myorder')->orderDetails($post); //普通订单详情
        }
        $this->appReturn($result);
    }

    /**
    *售后商品列表
     */
    public function shproduct_list(){
        $post = $this->post;

        if(empty($post['uid']) || empty($post['token'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Myorder')->shproduct_list($post);
        $this->appReturn($result);
    }





    /**
     *物流详情
     */
    public function logistics_info(){
        $post = $this->post;

        if(empty($post['order_id']) || empty($post['order_number'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }

        $result = model('Myorder')->logistics_info($post['order_id'],$post['order_number']);

        $this->appReturn($result);
    }

    /**
     *确认收货
     */
    public function sure_accept(){
        $post = $this->post;
        if(empty($post['order_id']) || empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Myorder')->sure_accept($post);
        $this->appReturn($result);

    }



}