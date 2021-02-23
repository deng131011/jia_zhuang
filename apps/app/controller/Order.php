<?php

namespace app\app\controller;
use think\Db;

/**
*订单
 */
class Order extends Home {




    /**
     *订单结算页面信息
     * enter_type:1直接购买进入结算，2购物车选择进入结算
     */


    public function order_info(){
        $post = $this->post;

        $user = $this->userinfo;
        $result['base_info'] = model('Order')->order_info($post,$user);
        if(!empty($result['code']) && $result['code']==201){
            $this->appReturn($result);
        }

        //默认地址
        $default = model('Order')->default_address($post);
        $result['default_address']  = !empty($default) ? $default : '';
        $result['send_price'] = model('Order')->send_price($default,$result['base_info']['total_price']); //运费
        //用户信息
        $result['user_info'] = $user;

        //用户优惠券
        $coupon = model('Coupon')->check_coupon($post['uid'], $result['base_info']['total_price'],1);
        $result['couppon_list'] = $coupon['code']==200 ? $coupon['data'] : [];

        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }



    /**
    *获取运费
     **/
    public function get_send_price(){
        $post = $this->post;
        if(empty($post['city_id']) || empty($post['total_price'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $send_price = model('Order')->send_price($post,$post['total_price']); //运费
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$send_price));
    }




    /**
     *添加普通订单
     * enter_type:1直接购买进入结算，2购物车选择进入结算
     */
    public function add_order(){
        $post = $this->post;
        $user = $this->userinfo;
        $result = model('Order')->add_order($post,$user);
        $this->appReturn($result);
    }


    //订单商品
    public function order_product(){
        $post = $this->post;
        if( empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数！'));
        }
        $map['l.order_id']     = array('eq',$post['order_id']);
        $sonlist = db('order_list l')->join('product p','p.id=l.product_id')->where($map)->field('p.title,p.icon,p.guige,l.*')->select();
        foreach ($sonlist as $ks=>$vs){
            $sonlist[$ks]['imgurl'] = get_image($vs['icon']);
        }
        $this->appReturn(array('code'=>200,'msg'=>'成功！','data'=>$sonlist));
    }


    /**
     *单个订单信息
     **/
    public function one_details(){
        $post = $this->post;
        if( empty($post['order_listid'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数！'));
        }
        $vo = db('order_list o')->join('product p','p.id=o.product_id')->field('p.*,o.num')->find();
        $vo['imgurl'] = get_image($vo['icon']);
        $this->appReturn(array('code'=>200,'msg'=>'成功！','data'=>$vo));
    }







    /**
     *物流详情
     */
    public function logistics_info(){
        $post = $this->post;
		
		
        if(empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数！'));
        }
		
        $result = model('Order')->logistics_info($post['order_id']);
		
        $this->appReturn($result);
    }







    /*
     *
     * */

}