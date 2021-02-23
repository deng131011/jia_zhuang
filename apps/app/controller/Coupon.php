<?php

namespace app\app\controller;

class Coupon extends Home {


    /**
    *没领的优惠券列表
     */
    public function noget_coupon(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result  = model('Coupon')->noget_coupon($post);
        $this->appReturn($result);
    }

    /**
     *领取优惠券
     */
    public function get_coupon(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['coupon_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }

        $result  = model('Coupon')->get_coupon($post);
        $this->appReturn($result);
    }






    /**
     *我的优惠券列表
     */
    public function my_coupon(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $post['type'] = !empty($post['type']) ? $post['type'] : 1;
        $result  = model('Coupon')->my_coupon($post);
        $this->appReturn($result);
    }


    /**
     *获取可用的优惠券
     */
    public function canuse_coupon(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result  = model('Coupon')->check_coupon($post['uid'],$post['total_price']);
        $this->appReturn($result);
    }









}