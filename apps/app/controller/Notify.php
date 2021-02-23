<?php

namespace app\app\controller;
use think\Controller;
use app\app\model\Pay as appPayModel;
/**
*订单
 */
class Notify extends Controller {

    function _initialize()
    {
        parent::_initialize();
        $list = file_get_contents('php://input');
        $post = json_decode($list, true);
        $this->post = $post;
    }


    /**---------------------------------------  商品购买回调 --------------------------------------*/

    /**
     * 支付宝（商品支付）回调
     */
    public function product_alipay_notify(){
        $post = $_POST;
        $order_number = trim($post['out_trade_no']);
		
		file_put_contents('dddd.txt',$order_number);
		
        if($post['trade_status'] == 'TRADE_SUCCESS'){
            $appPayModel = new appPayModel();
            $res = $appPayModel->pay_success($order_number,1,json_encode($post));

            if($res['code']==200){
                echo "success";
            }else{
                echo "failure";
            }
        }
    }
    /**
     * 微信（商品支付）回调
     */
    public function product_wxpay_notify(){
        $post = file_get_contents("php://input");
        $postObj = simplexml_load_string($post, 'SimpleXMLElement', LIBXML_NOCDATA );
        $order_number = trim($postObj->out_trade_no); //订单号

        if(trim($postObj->result_code)=='SUCCESS'){
            $appPayModel = new appPayModel();
            $res = $appPayModel->pay_success($order_number,2,$post);
            if($res['code']==200){
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                return sprintf('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
            }else{
                return false;
            }
        }else{
            return false;
        }
    }



    /**---------------------------------------  余额充值回调 --------------------------------------*/

    /**
     * 支付宝（充值）回调
     */
    public function payup_alipay_notify(){
        $post = $_POST;
        $order_number = trim($post['out_trade_no']);
        if($post['trade_status'] == 'TRADE_SUCCESS'){
            $where['order_number'] = array('eq',$order_number);
            $orderinfo = db('payup_order')->where($where)->find();
            $res = model('Payup')->payup_success($orderinfo['uid'],$orderinfo,1,$post);
            if($res['code']==200){
                echo "success";
            }else{
                echo "failure";
            }
        }
    }

    /**
     * 微信（充值）回调
     */
    public function payup_wxpay_notify(){
        $postdata = file_get_contents("php://input");
        $postObj = simplexml_load_string($postdata, 'SimpleXMLElement', LIBXML_NOCDATA );
        $order_number = trim($postObj->out_trade_no); //订单号
        if(trim($postObj->result_code)=='SUCCESS'){
            $where['order_number'] = array('eq',$order_number);
            $orderinfo = db('payup_order')->where($where)->find();
            $res = model('Payup')->payup_success($orderinfo['uid'],$orderinfo,2,$postdata);
            if($res['code']==200){
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                return sprintf('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


}