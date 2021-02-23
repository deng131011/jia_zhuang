<?php

namespace app\app\controller;

class Car extends Home {


    /**
    *加入购物车
     * addtype:1增加，2减少
     */
    public function add_car(){
        $post = $this->post;


        $result  = model('Car')->add_car($post);
        $this->appReturn($result);
    }

    /**
     *购物车列表
     */
    public function car_list(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result  = model('Car')->car_list($post);
        $this->appReturn($result);
    }

    /**
     *删除购物车
     */
    public function delete_car(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['car_id'])){
             $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result  = model('Car')->delete_car($post);
        $this->appReturn($result);
    }

    /**
     *购物车中移到收藏夹
     */
    public function move_collect(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['car_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result  = model('Car')->move_collect($post);
        $this->appReturn($result);
    }

    
}