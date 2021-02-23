<?php

namespace app\admin\controller;
use think\Db;

class Fankui extends Admin{

    function _initialize()
    {
        parent::_initialize();
    }

    /**
     *意见反馈列表
     */
    public function index() {
        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);
         
        if(!empty($get['dates'])){
			 
            $timearr = explode('—',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['f.create_time'] = $time;
            }
        }
		
		if(!empty($get['keywords'])){
			$where['u.username|u.mobile|f.username|f.content'] = array('like','%'.$get['keywords'].'%');
		}

        $where['f.status'] = array('gt',-1);
        $data_list =  Db::name('fankui')->alias('f')->join('usermember u','f.uid = u.id','LEFT')->where($where)->order('f.id desc')->field('f.*,u.username as usernameb,u.mobile as mobileb')->paginate(20)->each(function($item,$key){
            return $this->list_foreach($item);
        });
        $this->assign('list',$data_list);
        $this->assign('meta_title','意见反馈');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        return $this->fetch();
    }


    //公共循环插入
    public function list_foreach($item){
        $item['imgarr'] = imgArr($item['imgarr']) ;
        return $item;
    }


    /**
    *详情
     */
    public function details(){
        if(IS_POST){
            $post = input('param.');
            if(empty($post['check_reason'])){
                $this->error('平台回复内容不能为空');
            }
            $res = Db::name('fankui')->where('id',$post['id'])->update(['check_status'=>1,'check_reason'=>$post['check_reason'],'check_time'=>time()]);
            if($res){
                $this->success('提交成功',url('index'));
            }else{
                $this->error('提交失败');
            }

        }else{
            $get = input('param.');
            $where['f.id'] = array('eq',$get['id']);
            $vo =  Db::name('fankui')->alias('f')->join('usermember u','f.uid = u.id','LEFT')->where($where)->field('f.*,u.username as usernameb,u.mobile as mobileb')->find();
            if(!empty($vo['imgarr'])){
                $vo['imgarr'] = imgArr($vo['imgarr']);
            }
            $this->assign('info',$vo);
            $this->assign('meta_title','意见反馈');
            return $this->fetch();
        }
    }




}