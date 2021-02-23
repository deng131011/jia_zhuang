<?php

namespace app\admin\controller;
use app\app\model\Comment as appCommentModel;
use think\Db;

class Comment extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Comment');
    }

    /**
     * 评价
     */
    public function index() {

        $get = input('param.');
        $get['type'] = !empty($get['type'])? $get['type'] : 1;
        $get['pj_dj'] = !empty($get['pj_dj'])? $get['pj_dj'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $where['b.create_time'] = array('between',[$timearr[0],$timearr[1]]);
        }
        if(!empty($get['type'])){
            $where['b.type'] = array('eq',$get['type']);
        }
        if(!empty($get['pj_dj'])){
            $where['b.comment_type'] = array('eq',$get['pj_dj']);
        }
        if(!empty($get['keywords'])){
            $where['b.content'] = array('like','%'.trim($get['keywords']).'%');
        }

        $where['b.status'] = array('gt',-1);
        $where['b.pid'] = array('eq',0);

        $data_list =  Db::name('comment')->alias('b')->join('usermember u','u.id = b.uid')->field('b.*,u.username,u.head_icon,u.wxheadimg')->where($where)->order('id desc')->paginate(20)->each(function($item,$key){
              return $this->foreach_list($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','评价列表');
        $this->assign('field',['name'=>'dates','id'=>'dates']);

        return $this->fetch();
    }

    //公共循环插入
    public function foreach_list($item){

        return $item;
    }


    /**
    *详情
     */
    public function edit($id){
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            $post = input('param.');

            //验证数据
            if(empty($post['check_status'])){
                $this->error('请选择审核结果');
            }

            $data['check_status'] = $post['check_status'];
          //  $data['reason']       = $post['reason'];
            $data['update_time']  = time();
            $res = db('comment')->where('id',$post['id'])->update($data);
            if($res == true){
                $this->success('提交成功',url('index',array('type'=>$post['type'])));
            }else{
                $this->error('提交失败');
            }

        } else {

            $this->assign('meta_title',$title);

            $comment = new appCommentModel();
            $res = $comment->order_comment(['comment_id'=>$id]);
            $this->assign('info',$res);

            $user = db('usermember')->find($res['uid']);
            $user['headimg'] = head_img_url($user['head_icon'],$user['wxheadimg']);
            $this->assign('user',$user);
            return $this->fetch();
        }
    }
	
	
	
	
	/**
     * 消息列表
     */
    public function newmsg() {

        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['b.content'] = array('like','%'.trim($get['keywords']).'%');
        }

        $where['user_type'] = array('eq',5);
        $where['uid']      = array('eq',0);
        $data_list =  Db::name('message')->alias('b')->where($where)->order('id desc')->paginate(20);

        $this->assign('list',$data_list);
        $this->assign('meta_title','消息列表');

        $whet['is_see']      = array('eq',0);
        db('message')->where($whet)->update(['is_see'=>1]);

        return $this->fetch();
    }


    /**
    *删除评论
     */
    public function delete_comment(){
        if(IS_POST){
            $post = input('param.');
            $where['id'] = array('eq',$post['id']);
            if(!empty($post['type']) && $post['type']==3){
                $res =  db('comment')->where($where)->update(['additional_comment'=>'','additional_time'=>0]);
            }else{
                $res =  db('comment')->where($where)->delete();
            }
            if($res){
                $this->ajaxReturn(['code'=>200,'msg'=>'删除成功']);
            }else{
                $this->ajaxReturn(['code'=>201,'msg'=>'删除失败']);
            }
           
        }
    }





}