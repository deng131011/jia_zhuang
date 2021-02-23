<?php

namespace app\app\controller;
use app\app\model\Culture as CultureModel;
use eacoo\Tree;

/**
*
 */
class Culture extends Home {


    /**
    *文章列表
    */
    public function article_list(){
        $post = $this->post;
        if(empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'请选择分类！'));
        }
        $Culture = new CultureModel();
        $result = $Culture->article_list($post);
        $this->appReturn($result);
    }

    /**
     *文章详情
     */
    public function article_details(){
        $post = $this->post;
        if(empty($post['article_id'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $Culture = new CultureModel();
        $result = $Culture->article_details($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *活动列表
     */
    public function activit_list(){
        $post = $this->post;


        $Culture = new CultureModel();
        $result['seven_days'] = $Culture->seven_days($post);

        $post['dates'] = !empty($post['dates']) ? $post['dates'] : date('Y-m-d');
        $result['list'] = $Culture->activit_list($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }


    /**
     *热搜关键字
     */
    public function hot_key(){
        $post = $this->post;
        if(empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }

        $map['type']   = array('eq',$post['type']);
        $map['status'] = array('eq',1);
        $list = db('hot_search')->where($map)->order('sort asc,id desc')->select();
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$list));
    }



    /**
     *系统消息
     *type: 2点赞  3 评论回复
     */
    public function system_msg(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $Culture = new CultureModel();
        $result = [];
        if($post['type']==1){
            $result = $Culture->zan_msg($post);//赞列表
        }else if($post['type']==2){
            $result = $Culture->shops_msg($post);//评论回复列表
        }
        $map['uid']=$post['uid'];
        $map['see']=1;

        $no_see['zan']     = $Culture->zan_msg($map,'count');//赞列表
        $no_see['comment'] = $Culture->shops_msg($map,'count');
        $this->appReturn(array('code'=>200,'msg'=>'成功！','data'=>$result,'no_see'=>$no_see));
    }


}