<?php

namespace app\app\model;
use think\Cache;
use think\Db;
use think\Model;
use app\common\model\Usermember;
use app\app\model\Wechat as WechatModel;
class Account extends Model {


    protected function initialize(){
        parent::initialize();
        $this->usermemberModel = new Usermember();
    }


    /**
     * 第一次登录注册
     */
    public function register($post){
        $yzm = cache(trim($post['mobile']));
        if($yzm!=$post['yzm']){
            //return array('code'=>201,'msg'=>'验证码错误');
        }
        $where['mobile']   = array('eq',trim($post['mobile']));
        $vo = $this->usermemberModel->where($where)->find();
        if(!empty($vo)){
            return array('code'=>201,'msg'=>'该手机号已注册');
        }

        if($post['password'] != $post['repassword']){
            return array('code'=>201,'msg'=>'两次密码不一致！');
        }

        $chk_res = $this->commlogin($post);
        if($chk_res['code']==201){
            return $chk_res;
        }

        Db::startTrans();
        try{
            $post['token'] = str_rand(32);
            $post['password'] = encrypt(trim($post['password']));
            $post['reg_time'] = mydate();
            $post['codeid']   = userid(); //用户编号
            $res = $this->usermemberModel->allowField(true)->save($post);
            $vo['uid'] = $this->usermemberModel->id;
            $vo['token'] = $post['token'];
            Db::commit();
            return array('code'=>200,'msg'=>'登录成功！','data'=>$vo);
        }catch (\Exception $e){
            Db::rollback();
            return array('code'=>201,'msg'=>'登录失败！');
        }
    }

    /**
     * 短信验证码登录
     */
    public function yzmLogin($post)
    {
        $yzm = cache(trim($post['mobile']));
        if($yzm!=$post['yzm']){
           // return array('code'=>201,'msg'=>'验证码错误');
        }

        $where['mobile']   = array('eq',trim($post['mobile']));
        $vo = $this->usermemberModel->where($where)->field('*,id as uid')->find();

        $chk_res = $this->commlogin($post,$vo);
        if($chk_res['code']==201){
            return $chk_res;
        }
        Db::startTrans();
        try {
            $data['token'] = str_rand(32);
            if (!empty($vo)) {
                if ($vo['status'] != 1) {
                    return array('code' => 201, 'msg' => '该账号已被禁用！');
                }
                if (!empty($post['unionid']) && empty($vo['unionid'])) {
                    $data['unionid'] = $post['unionid'];
                }
                if (!empty($post['nickname']) && empty($vo['nickname'])) {
                    $data['nickname'] = $post['nickname'];
                }
                if (!empty($post['wxheadimg']) && empty($vo['wxheadimg'])) {
                    $data['wxheadimg'] = $post['wxheadimg'];
                }
                if (empty($vo['codeid'])) {
                    $data['codeid'] = userid(); //用户编号
                }
                $data['update_time'] = mydate();
                $this->usermemberModel->where(array('id' => ['eq', $vo['id']]))->update($data);
                $vo['token'] = $data['token'];
            } else {
                $post['token'] = $data['token'];
                $post['codeid'] = userid(); //用户编号
                $res = $this->usermemberModel->allowField(true)->save($post);
                $vo['uid'] = $this->usermemberModel->id;
                $vo['token'] = $data['token'];
            }
            Db::commit();
            return array('code' => 200, 'msg' => '登录成功！', 'data' => $vo);
        }catch (\Exception $e){
            Db::rollback();
            return array('code' => 201, 'msg' => '登录失败！');
        }
    }



    public function commlogin($post,$vo=[]){
        if(!empty($post['unionid'])){
            if(!empty($vo)){
                $three['id'] = array('neq',$vo['id']);
            }
            $three['unionid'] = array('eq',$post['unionid']);
            $openid = db('usermember')->where($three)->find();
            if(!empty($openid)){
                return array('code'=>201,'msg'=>'该微信已被别人绑定！');
            }
        }

        return array('code'=>200,'msg'=>'成功！');
    }


    /**
     * 三方登录
     * three_type:1微信登录，2QQ登录
     */
    public function three_login($post)
    {
        if($post['three_type']==1){
            $where['unionid'] = array('eq',$post['unionid']);
        }

        $vo = db('usermember')->where($where)->field('*,id as uid')->find();
        if(!empty($vo)){
            if($vo['status']!=1){
                return array('code'=>201,'msg'=>'该账号已被禁用');
            }
            if(empty($vo['codeid'])){
                $data['codeid']   = userid(); //用户编号
            }
            $data['token']       = str_rand(32);
            $data['update_time'] = mydate();
            db('usermember')->where('id',$vo['id'])->update($data);

            $vo['token'] = $data['token'];
            return array('code'=>200,'msg'=>'登录成功','data'=>$vo);
        }else{
            return array('code'=>202,'msg'=>'还未绑定，请立即绑定！');
        }
    }


    /**
     * 账号密码登录
     */
    public function login($post)
    {
        $where['mobile']   = array('eq',$post['mobile']);
        $vo = $this->usermemberModel->where($where)->field('*,id as uid')->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'该手机号未注册！');
        }

        if(encrypt($post['password'])!=$vo['password']){
            return array('code'=>201,'msg'=>'密码错误！');
        }
        if($vo['status']!=1){
            return array('code'=>201,'msg'=>'该账号已被禁用！');
        }
        if (!empty($post['unionid']) && empty($vo['unionid'])) {
            $data['unionid'] = $post['unionid'];
        }
        if (!empty($post['nickname']) && empty($vo['nickname'])) {
            $data['nickname'] = $post['nickname'];
        }
        if (!empty($post['wxheadimg']) && empty($vo['wxheadimg'])) {
            $data['wxheadimg'] = $post['wxheadimg'];
        }
        $data['token'] = str_rand(32);//生成验证token
        $this->usermemberModel->where(array('id'=>['eq',$vo['uid']]))->update($data);
        $vo['token'] = $data['token'];
        return array('code'=>200,'msg'=>'登录成功！','data'=>$vo);
    }



    /**
     * 找回密码
     */
    public function forgetPass($post)
    {

        if(strlen($post['password'])<6){
            return array('code'=>201,'msg'=>'密码不能少于6位！');
        }
        $yzm = Cache::get($post['mobile']);
        if($yzm!=$post['yzm']){
            return array('code'=>201,'msg'=>'验证码错误！');
        }
        $where['mobile'] = array('eq',$post['mobile']);
        $vo = db('usermember')->where($where)->find();
        if(empty($vo)){
           return array('code'=>201,'msg'=>'手机号未注册！');
        }
        if($vo['status']!=1){
            return array('code'=>201,'msg'=>'该账号已被禁用！');
        }
        $data['password']    = encrypt(trim($post['password']));
        $data['update_time'] = time();
        $data['id']          = $vo['id'];
        $save = db('usermember')->update($data);
        if($save){
            return array('code'=>200,'msg'=>'修改成功！');
        }else{
            return array('code'=>201,'msg'=>'修改失败！');
        }
    }



    /**
     * 验证码
     **/
    public function yzm($post){
        $phone = trim($post['mobile']);
        $where['mobile'] = array('eq',$phone);
        if($post['type']==1){
            //验证码登录

        }else if($post['type']==2){
            //修改手机号旧手机号获取验证码
            $vo = db('usermember')->where($where)->find();
            if(empty($vo)){
                 return array('msg'=>'旧手机号不存在！','code'=>201); exit;
            }
        }else if($post['type']==3){
            //修改密码
            $where['status'] = array('eq',1);
            $vo = db('usermember')->where($where)->find();
            if(empty($vo)){
                return array('code'=>201,'msg'=>'手机号未注册！');
            }

        }else if($post['type']==4){
            //修改手机号新手机号获取验证码
            $vo = db('usermember')->where($where)->find();
            if(!empty($vo)){
                return array('msg'=>'新手机号已经存在！','code'=>201); exit;
            }
        }else{
			 return array('msg'=>'非法操作','code'=>201); exit;
		}



        //$rand = rand(100000,999999);
        $rand = '123456';
        //$ret = AliyunSendMsg($phone,'SMS_210062243',$rand);
        $ret['Code']='OK';
        if($ret['Code']=='OK'){
            Cache::set($phone,$rand,300);
            //cookie($phone,$rand,300); //缓存5分钟
            return array('msg'=>'验证码已发送','code'=>200);
        }else{
            return array('msg'=>'验证码发送失败','code'=>201);
        }
    }



    /**
    *注册协议
    * type:1服务协议,2隐私政策
    */
    public function auth_info($post){
        $where['pid'] = array('eq',13);
        if($post['type']==1){
            $where['id'] = array('eq',14);
        }else if($post['type']==2){
            $where['id'] = array('eq',15);
        }else{
            return array('msg'=>'非法操作','code'=>201);
        }
        $vo =   db('category')->where($where)->field('id,content')->find();
        $vo['content'] = preg_replace('/(<img.+?src=")(.*?)/', '$1'.config('index_url').'$2', $vo['content']);
        //$vo['url'] = config('index_url').'home/authinfo/index?id='.$vo['id'];
        return array('msg'=>'成功','code'=>200,'data'=>$vo);
    }







}
