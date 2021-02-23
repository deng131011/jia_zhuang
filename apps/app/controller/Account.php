<?php

namespace app\app\controller;
use app\common\model\User as UserModel;
use think\Cache;
use think\captcha\Captcha;

class Account extends Home {

    /**
     * 验证码登录设置密码
     */
    public function register()
    {
        $post = $this->post;
        if(empty($post['mobile']) || empty($post['password'])){
            $this->appReturn(array('code'=>201,'msg'=>'请输入手机号或密码'));
        }
        $result = model('Account')->register($post);
        $this->appReturn($result);
    }

    /**
     * 短信验证码登录
     */
    public function yzm_login()
    {
        $post = $this->post;
        if(empty($post['mobile']) || empty($post['yzm'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result = model('Account')->yzmLogin($post);

        $this->appReturn($result);
    }


    /**
     * 三方登录
     */
    public function three_login()
    {
        $post = $this->post;
        if(empty($post['three_type'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result = model('Account')->three_login($post);
        $this->appReturn($result);
    }


    /**
     * 账号密码登录
     */
    public function login()
    {
        $post = $this->post;
        if(empty($post['mobile']) || empty($post['password'])){
            $this->appReturn(array('code'=>201,'msg'=>'请输入手机号或密码'));
        }
        $result = model('Account')->login($post);
        $this->appReturn($result);
    }



    /**
     * 找回密码
     */
    public function forgetPass()
    {
        $post = $this->post;
        if(empty($post['mobile']) || empty($post['password']) || empty($post['yzm'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result = model('Account')->forgetPass($post);
        $this->appReturn($result);
    }



    /**
     * 退出登录
     * @return [type] [description]
     */
    public function logout(){
        session(null);
        cookie(null,config('cookie.prefix'));
        $this->redirect(url('home/login/login'));
    }


    //验证码
    public function yzm(){
        $post = $this->post;
        if(empty(trim($post['mobile']))){
            $this->appReturn(array('code'=>201,'msg'=>'请输入手机号'));
        }
        if(empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'网络错误，请稍后再试'));
        }
        $result = model('Account')->yzm($post);
        $this->appReturn($result);
    }

    /**
    *协议
     */
    public function auth_info(){
        $post = $this->post;
        if(empty($post['type'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少参数'));
        }
        $result = model('Account')->auth_info($post);
        $this->appReturn($result);
    }




    /**
     *微信登录获取
     */
    public function get_wx_info(){
        $post = $this->post;
        if(empty($post['code'])){
            $this->appReturn(array('code'=>201,'msg'=>'缺少Code'));
        }
        $config = config('wechatConfig');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$config['AppID'].'&secret='.$config['AppSecret'].'&js_code='.$post['code']. '&grant_type=authorization_code';

        $result = file_get_contents($url);
        $this->appReturn(['code'=>200,'msg'=>'成功','data'=>$result]);
    }


    /**
     * 小程序解密
     */
    public function small_jiemi()
    {
        $post = $this->post;
        $config = config('wechatConfig');

        Vendor('xcx_jiemi.wxBizDataCrypt');
        $appid = $config['AppID'];
        $sessionKey = $post['sessionKey'];
        $encryptedData=$post['encryptedData'];
        $iv = $post['iv'];

        $pc = new \WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData,$iv,$data);
        if ($errCode == 0) {
            $this->appReturn(array('code'=>200,'msg'=>'成功','data'=>$data));
        } else {
            $this->appReturn(array('code'=>201,'msg'=>$errCode));
        }
    }





}