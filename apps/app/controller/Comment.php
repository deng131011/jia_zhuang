<?php

namespace app\app\controller;

class Comment extends Home {





    /**
     *添加单个评价
     * type:1商品  2课程，3直播，4题库
     **/
    public function add_one_comment(){
        $post = $this->post;

        $result = model('Comment')->add_one_comment($post);
        $this->appReturn($result);
    }


    /**
     *添加回复、评论别人的评论
     **/
    public function add_reply(){
        $post = $this->post;
		
        if(empty($post['uid']) || empty($post['xt_pid']) || empty($post['type']) || empty($post['pid']) || empty($post['content']) || empty($post['reply_uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Comment')->add_reply($post);
        $this->appReturn($result);
    }





    /**
     *商品评价列表
     * type:1商品  2商家，3帖子
     **/
    public function comment_list(){
        $post = $this->post;
        if(empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }

        //列表
        $result['list'] = model('Comment')->comment_list($post);

        $this->appReturn(['code'=>200,'msg'=>'成功','data'=>$result]);
    }



    /**
     *评价详情
     **/
    public function comment_details(){
        $post = $this->post;
        if(empty($post['comment_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Comment')->comment_details($post);
        $this->appReturn($result);
    }

    /**
     *删除自己的评价
     **/
    public function delmy_comment(){
        $post = $this->post;
        if(empty($post['comment_id']) || empty($post['uid']) || empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Comment')->delmy_comment($post);
        $this->appReturn($result);
    }


    /**
     *查看订单的评价
     **/
    public function order_comment(){
        $post = $this->post;
        if(empty($post['order_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Comment')->order_comment($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *我的商家评价
     **/
    public function myshop_comment(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $result = model('Comment')->myshop_comment($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


}