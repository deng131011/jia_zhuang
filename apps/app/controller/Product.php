<?php

namespace app\app\controller;

class Product extends Home {





    /**
     * 商品分类
     */
    public function product_type(){
        $post = $this->post;
        $result  = model('Product')->product_type($post);
        $this->appReturn($result);
    }

    /**
     * 普通商城首页
     */
    public function product_index(){
        $post = $this->post;
        $result  = model('Product')->product_index($post);
        $this->appReturn($result);
    }

    /**
     * 商品列表
     */
    public function product(){
        $post = $this->post;
        $result  = model('Product')->product($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功！','data'=>$result));
    }


    /**
     *支付成功界面相似商品
     */
    public function same_product(){
        $post = $this->post;
        $result  = model('Product')->same_product($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     * 商品详情
     */
    public function product_details(){
        $post = $this->post;

        if(empty($post['product_id'])){
           $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }

        $result  = model('Product')->product_details($post);
        $this->appReturn($result);
    }


    /**
     * 收藏
     */
    public function collect(){
        $post = $this->post;
        if(empty($post['return_id']) || empty($post['uid']) || empty($post['collect_type'])){
            $this->appReturn(array('code'=>201,'status'=>false,'msg'=>'参数错误！'));
        }
        $res = model('Product')->collect($post);
        $this->appReturn($res);
    }

    /**
     * 收藏列表
     */
    public function collect_list(){
        $post = $this->post;

        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'status'=>false,'msg'=>'参数错误！'));
        }
        $res = model('Product')->collect_list($post);
        $this->appReturn($res);
    }


    /**
     * 取消收藏
     */
    public function del_collect(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['collect_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result = model('Product')->del_collect($post);
        $this->appReturn($result);
    }




    /**
     * 点赞
     */
    public function add_click_list(){
        $post = $this->post;
        if(empty($post['return_id']) || empty($post['uid']) || empty($post['collect_type'])){
            $this->appReturn(array('code'=>201,'status'=>false,'msg'=>'参数错误！'));
        }
        $res = model('Product')->add_click_list($post);
        $this->appReturn($res);
    }





}