<?php

namespace app\app\controller;

class Buyafter extends Home {



    /**
     *申请售后原因列表
     */
    public function apply_reason(){
        $post = $this->post;
        $result = model('Buyafter')->apply_reason($post);
        $this->appReturn($result);
    }

    /**
     *添加售后申请
     * apply_type:1仅退款，2退货退款
     */
    public function add_apply(){
        $post = $this->post;

        $result = model('Buyafter')->add_apply($post);
        $this->appReturn($result);
    }

    /**
     *退款详情
     */
    public function refund_details(){
        $post = $this->post;

        $result = model('Buyafter')->refund_details($post);
        $this->appReturn($result);
    }

    /**
     *取消退款
     */
    public function return_refund(){
        $post = $this->post;

        $result = model('Buyafter')->return_refund($post);
        $this->appReturn($result);
    }


    /**
     *添加退货快递信息
     */
    public function add_kuaidi(){
        $post = $this->post;

        $result = model('Buyafter')->add_kuaidi($post);
        $this->appReturn($result);
    }


    /**----------------------------------  酒店申请退款  -------------------------------------*/



    
}