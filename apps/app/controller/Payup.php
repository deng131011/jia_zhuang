<?php

namespace app\app\controller;
/**
 *充值
 */
class Payup extends Home {

    /**
    *提交充值
     */
    public function add_payup(){
        $post = $this->post;

        $result  = model('Payup')->add_payup($post);
        $this->appReturn($result);
    }


    /**
    *微信支付宝发起支付(充值)
     * pay_type:1支付宝，2微信
    **/
    public function weixin_alipay_payup(){
        $post = $this->post;
        $result = model('Payup')->weixin_alipay_payup($post);
        $this->appReturn($result);
    }



    /**
     *余额明细
     **/
    public function flowater(){
        $post = $this->post;
        $result = model('Payup')->flowater($post);
        $this->appReturn($result);
    }



}