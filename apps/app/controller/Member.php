<?php

namespace app\app\controller;

use think\Cache;
use app\app\model\Member as MemberModel;
class Member extends Home {

    /**
     *用户信息
     * screen_type：1苹果，2安卓，3小程序
     */
    public function index(){
        $post = $this->post;

        if(empty($post['uid']) || empty($post['token'])){
            $this->appReturn(array('code'=>201,'msg'=>'登录失效，请重新登录'));
        }
        $user  = $this->userinfo;
		$head_img              = getAdImages(4,'',1);
		$user['head_img']      = $head_img['imgurl'];
		$result['userinfo']    =   $user;
		$result['kefu_mobile'] =   config('KEFU_MOBILE');

        $result['order_count'] = model('Myorder')->order_count($post);
        $result['yuyue_count'] = model('Shoporder')->order_list(['uid'=>$post['uid'],'type'=>1,'saoma_type'=>2],'count');

        //消息数量
        /*$vots = model('Member')->newMsgNum(['uid'=>$post['uid']]);
        $result['msg_num'] = intval($vots['count_a']) + intval($vots['count_b']);*/

        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }



    /**
     *设置用户信息
     */
    public function edit_user(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['token'])){
             $this->appReturn(array('code'=>201,'msg'=>'登录失效，请重新登录'));
        }
        $result  = model('Member')->edit_user($post);
        $this->appReturn($result);
    }

    /**
     *修改手机号第一步
     */
    public function editone_mobile(){
        $post = $this->post;
        if(empty($post['old_mobile']) || empty($post['yzm'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $yzm = Cache::get($post['old_mobile']);
        if(trim($post['yzm'])!=$yzm){
            $this->appReturn(array('code'=>201,'msg'=>'验证码错误'));
        }
        if(!empty($post['uid'])){
            $map['id'] = array('eq',$post['uid']);
        }
        $map['mobile'] = array('eq',trim($post['old_mobile']));
        $vo = db('usermember')->where($map)->find();
        if(empty($post['uid'])){
            $yzm = Cache::set($post['old_mobile'],null);
            $this->appReturn(array('code'=>200,'msg'=>'成功'));
        }else{
            if(empty($vo)){
                $this->appReturn(array('code'=>201,'msg'=>'手机号和你的账号不匹配'));
            }else{
                $yzm = Cache::set($post['old_mobile'],null);
                $this->appReturn(array('code'=>200,'msg'=>'成功'));
            }
        }
    }


    /**
     *修改密码
     */
    public function update_pass(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['token'])){
            $this->appReturn(array('code'=>201,'msg'=>'登录失效，请重新登录'));
        }

        $result  = model('Member')->update_pass($post);
        $this->appReturn($result);
    }

    /**
     *绑定、解绑微信、QQ
     */
    public function three_bang(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['token'])){
            $this->appReturn(array('code'=>201,'msg'=>'登录失效，请重新登录'));
        }
        $result = model('Member')->three_bang($post);
        $this->appReturn($result);
    }



    /**
     *消息分类对应的信息
     */
    public function message_type(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result  = model('Member')->newMsgNum($post);
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$result));
    }

    /**
     *消息列表
     */
    public function message(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result  = model('Member')->message($post);
        $this->appReturn($result);
    }
	
	
	  /**
     *意见反馈分类
     */
    public function fankui_type(){
        $post = $this->post;
        $where['status'] = array('eq',1);
        $where['pid'] = array('eq',5);
        $list = db('category')->where($where)->field('id,title')->order('sort asc')->select();
        $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$list));
    }



    /**
     *意见反馈提交
     */
    public function fankui_add(){
        $post = $this->post;
        if(empty($post['uid']) || empty($post['content'])){
            $this->appReturn(array('code'=>201,'msg'=>'请填写反馈内容'));
        }
        $post['create_time'] = time();
        $result  = model('Member')->fankui_add($post);
        $this->appReturn($result);
    }

    /**
     *意见反馈记录
     */
    public function fankui_list(){
        $post = $this->post;
        if(empty($post['uid'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result  = model('Member')->fankui_list($post);
        $this->appReturn($result);
    }


    /**
     *添加银行卡
     */
    public function add_bank(){
        $post = $this->post;
        $post['update_time'] = time();
        $result  = model('Member')->add_bank($post);
        $this->appReturn($result);
    }


    /**
     *大客户添加会员账户
     */
    public function bigkf_addvip(){
        $post = $this->post;
        $result  = model('Member')->bigkf_addvip($post);
        $this->appReturn($result);
    }


}