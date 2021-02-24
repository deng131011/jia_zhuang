<?php

namespace app\app\controller;

use think\Cache;
use think\cache\driver\Redis;
use think\Db;
use think\Session;

class Appindex extends Home {



    /**
     * APP首页接口
     * */
    public function index(){
        $post = $this->post;
        $result = model('Appindex')->appIndex($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }



    /**
     * 热门搜索
     */
    public function hot_search(){
        $post = $this->post;
        $res = model('Appindex')->hot_search($post);
        $this->appReturn($res);
    }

    /**
     * App版本更新
     */
    public function check_version(){
        $post = $this->post;
        if(empty($post['platform'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试！'));
        }
        $where['platform'] = array('eq',$post['platform']);
        $where['status'] = array('eq',1);
        $vo = db('appip')->where($where)->order('id desc')->find();
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$vo));

    }


	



    /**
     * APP、小程序上架审核开关
     * status_a:1开，2关
     */
    public function close_open(){
        $post = $this->post;
		$post['screen_type'] = !empty($post['screen_type']) ? $post['screen_type'] : '';
		if($post['screen_type']==1){
			$this->appReturn(array('code'=>200,'msg'=>'成功','status_a'=>2));//苹果端
		}else{
			$this->appReturn(array('code'=>200,'msg'=>'成功','status_a'=>1));//小程序端
		}
        
    }



    public function test(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        //$redis->set('jia_zhuang_stringa','邓松');
        p($redis->get('jia_zhuang_stringa'));
    }






	

    
}