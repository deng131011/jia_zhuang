<?php

namespace app\app\controller;
/**
 *支付
 */
class Pay extends Home {

    /**
    *购买商品发起支付
     * data_type:1下单立即支付，2待支付订单支付
     * pay_type:1支付宝，2微信
     */
    public function pay(){
        $post = $this->post;
        if(empty($post['order_number']) || empty($post['uid'])||empty($post['pay_type']) || empty($post['data_type']) || empty($post['screen_type'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }

        $orderinfo = model('Pay')->start_check($post['order_number'],$post['uid']);//检测订单是否存在

        if(empty($orderinfo)){
            $this->appReturn(array('code'=>201,'msg'=>'非法操作，订单不存在'));
        }
        $outime = config('order_pay_out_time');
        if((time()-$orderinfo['create_time'])>=$outime){
            $this->appReturn(array('code'=>201,'msg'=>'订单已失效，不能再支付！'));exit;
        }

        db('order')->where('id',$orderinfo['id'])->update(['screen_type'=>$post['screen_type']]);

        if($post['pay_type']==1){
            //支付宝
            $arr['ordernumber']  = $orderinfo['order_number'];
            $arr['data_type']    = $post['data_type'];
            $arr['screen_type']  = $post['screen_type'];
            $arr['title']        = '商城-下单';
            $arr['pay_price']    = $orderinfo['pay_price'];
            $arr['subject']      = '商城-下单';
            $arr['body']         = '商城下单支付';

            $arr['notify_url']   = config('index_url').'/app/Notify/product_alipay_notify'; //回调地址

            $res = model('Pay')->ali_pay($arr); //调取三方sdk
            $this->appReturn($res);

        }else if($post['pay_type']==2){
            //微信
            $data['ordernumber']  = $orderinfo['order_number'];
            $data['pay_price']    = $orderinfo['pay_price'];
            $data['screen_type']  = $post['screen_type'];
            $data['data_type']    = $post['data_type'];
            $data['openid']       = !empty($post['openid']) ? $post['openid'] : '';
            $data['body']         = '商城下单支付';
            $data['notify_url']   = config('index_url').'/app/Notify/product_wxpay_notify'; //回调地址
            $res = model('Pay')->wx_pay($data);
            $this->appReturn($res);
        }else{
            $this->appReturn(array('code'=>201,'msg'=>'请正确选择支付方式'));
        }
    }


    /**
     *酒店预订发起支付
     * data_type:1下单立即支付，2待支付订单支付
     * pay_type:1支付宝，2微信
     */
    public function hotel_pay(){
        if(IS_POST){
            $post = input('param.');
        }else{
            $post = $this->post;
        }
        if(empty($post['order_number']) || empty($post['uid'])||empty($post['pay_type']) || empty($post['data_type']) || empty($post['screen_type'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }

        $orderinfo = model('Pay')->start_check($post['order_number'],$post['uid'],'hotel_order');//检测订单是否存在
        if(empty($orderinfo)){
            $this->appReturn(array('code'=>201,'msg'=>'非法操作，订单不存在'));
        }
        $outime = config('order_pay_out_time');
        if((time()-$orderinfo['create_time'])>=$outime){
            $this->appReturn(array('code'=>201,'msg'=>'订单已失效，不能再支付！'));exit;
        }

        db('hotel_order')->where('id',$orderinfo['id'])->update(['screen_type'=>$post['screen_type']]);

        if($post['pay_type']==1){
            //支付宝
            $arr['ordernumber']  = $orderinfo['order_number'];
            $arr['data_type']    = $post['data_type'];
            $arr['screen_type']  = $post['screen_type'];
            $arr['title']        = '酒店-下单';
            $arr['pay_price']    = $orderinfo['pay_price'];
            $arr['subject']      = '酒店-下单';
            $arr['body']         = '酒店预订下单支付';
            $arr['notify_url']   = config('index_url').'/app/Notify/hotel_alipay_notify'; //回调地址
            $res = model('Pay')->ali_pay($arr); //调取三方sdk
            $this->appReturn($res);

        }else if($post['pay_type']==2){
            //微信
            $data['ordernumber']  = $orderinfo['order_number'];
            $data['pay_price']    = $orderinfo['pay_price'];
            $data['screen_type']  = $post['screen_type'];
            $data['data_type']    = $post['data_type'];
            $data['openid']       = !empty($post['openid']) ? $post['openid'] : '';
            $data['body']         = '商城下单支付';
            $data['notify_url']   = config('index_url').'/app/Notify/hotel_wxpay_notify'; //回调地址
            $res = model('Pay')->wx_pay($data);
            $this->appReturn($res);
        }else{
            $this->appReturn(array('code'=>201,'msg'=>'请正确选择支付方式'));
        }
    }



    public function test(){
        $max_person = explode('>','9');
        p(count($max_person));

        $res = model('Pay')->hotel_pay_success('735311202012111809',2);
        p($res);
    }

}