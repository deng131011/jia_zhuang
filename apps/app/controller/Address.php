<?php

namespace app\app\controller;

class Address extends Home {



    /**
     *省市区
     * pid:上级id
     */
    public function zone_list(){
        $post = $this->post;
        $post['pid'] = $post['pid']>0 ? $post['pid'] : 0;
        $result  = model('Address')->zone_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }
    /**
     *所有省市区
     * pid:上级id
     */
    public function all_zone_list(){
        $post = $this->post;

        $result  = model('Address')->all_zone_list();
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
    *添加收货地址
    */
    public function add_address(){
        $post = $this->post;


        $result  = model('Address')->add_address($post);
        $this->appReturn($result);
    }

    /**
     *收货地址详情
     */
    public function zone_details(){
        $post = $this->post;

        if(empty($post['address_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数！'));
        }
        $result  = model('Address')->zone_details($post);
        $this->appReturn($result);
    }


    /**
     *添加收货地址
     */
    public function lists(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数！'));
        }

        $result  = model('Address')->lists($post);
        $this->appReturn($result);
    }


    /**
     *删除收货地址
     */
    public function delete_address(){
        $post = $this->post;
        $res = model('address')->delete_address($post);
        $this->appReturn($res);
    }



    /**
     *城市列表
     */
    public function city_list(){
        $post = $this->post;
        $result  = model('Address')->city_list($post);
        $this->appReturn($result);
    }



}