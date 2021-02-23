<?php

namespace app\app\controller;

class Score extends Home {

    /**
     * 积分商城首页
     */
    public function product_index(){
        $post = $this->post;
        $result  = model('Score')->product_index($post);
        $this->appReturn($result);
    }


    /**
     * 积分商品列表
     */
    public function product(){
        $post = $this->post;
        $result  = model('Score')->product($post);
        $this->appReturn($result);
    }


    /**
     * 提交积分商品订单
     * enter_type:1直接购买进入结算，2购物车选择进入结算
     */
    public function addScoreOrder(){
        $post = $this->post;
        if(empty($post['uid'])||empty($post['token']) || empty($post['address_id'])  || empty($post['product_info']) || empty($post['enter_type'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }

        $result  = model('Score')->addScoreOrder($post);
        $this->appReturn($result);
    }

    /**
    *默认地址
     */
    public function default_address(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        //默认地址
        $map['uid']        = array('eq',$post['uid']);
        $map['is_default'] = array('eq',1);
        $default_address = db('address')->where($map)->find();
        $default_address = model('Order')->addressInfo($default_address);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$default_address));
    }

    /**
     *积分明细
     */
    public function score_flowater(){
        $post = $this->post;
        if( empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result  = model('Score')->score_flowater($post);
        $this->appReturn($result);
    }

    
}