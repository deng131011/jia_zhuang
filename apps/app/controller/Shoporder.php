<?php

namespace app\app\controller;
/**
 *商家订单
 */
class Shoporder extends Home {

    /**
     *商家订单列表
     **/
    public function order_list(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result['count'] = model('Shoporder')->order_list($post,'count');
        $result['list'] = model('Shoporder')->order_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
     *订单详情
     **/
    public function order_details(){
        $post = $this->post;

        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Shoporder')->order_details($post); //普通订单详情
        $this->appReturn($result);
    }

    /**
     *提交核销
     **/
    public function sure_hexiao(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Shoporder')->sure_hexiao($post);
        $this->appReturn($result);
    }

    /**
     *提交金额折算记录
     */
    public function add_price_recode(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Shoporder')->add_price_recode($post);
        $this->appReturn($result);
    }


    /**
     *消费记录
     * uid:商家自己，m_uid：其他用户
     */
    public function sale_recode(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['m_uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result['count'] = model('Shoporder')->sale_recode($post,'count');
        $result['list'] = model('Shoporder')->sale_recode($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *删除消费记录
     * uid:商家自己
     */
    public function del_recode(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Shoporder')->del_recode($post);
        $this->appReturn($result);
    }

    /**
     *扫码添加不需要预约的订单
     * uid:商家自己，
     */
    public function saoma_addorder(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['m_uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Shoporder')->saoma_addorder($post);
        $this->appReturn($result);
    }


}