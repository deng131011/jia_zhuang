<?php

namespace app\app\model;
use think\Cache;
use think\Db;
use think\Model;
use app\common\model\Usermember;
class Member extends Model {



    protected function initialize(){
        parent::initialize();
        $this->usermemberModel = new Usermember();
    }

    /**
     *用户信息
     */
    public function userInfo($post){
        $vo = $this->usermemberModel->find($post['uid']);
        $vo['head_img'] =  $vo['head_icon']>0 ? get_image($vo['head_icon']) : config('index_url').'/toux.png';
        $age = date('Y') - date('Y',strtotime($vo['birthday']));
        $vo['age'] =!empty($vo['birthday']) ? $age :'';
        $vo['ewm_url'] = !empty($vo['ewm_url']) ? config('index_url').$vo['ewm_url'] : '';
        //判断是否已完善信息
        $vo['wsinfo_status'] = complete_userinfo($vo);
        return $vo;
    }


    /**
     *设置用户信息
     */
    public function edit_user($post){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'登录失效，请重新登录');
        }
        $post['update_time'] = date('Y-m-d H:i:s');
        $res  = $this->usermemberModel->allowField(true)->save($post,['id'=>$post['uid']]);
        if($res){
            return array('msg'=>'提交成功','code'=>200);
        }else{
            return array('msg'=>'提交失败','code'=>201);
        }
    }

    /**
     *修改密码
     */
    public function update_pass($post){
        $yzm = Cache::get(trim($post['mobile']));
        if(empty(trim($post['yzm']))){
            return array('code'=>201,'msg'=>'请输入验证码');
        }
        if($yzm!=$post['yzm']){
           // return array('msg'=>'验证码错误','code'=>201);
        }
        if(empty(trim($post['password']))){
            return array('msg'=>'请输入密码','code'=>201);
        }
        if($post['password']!=$post['repassword']){
            return array('msg'=>'两次密码不一致','code'=>201);
        }
        $data['password']    = encrypt($post['password']);
        $data['update_time'] = date('Y-m-d H:i:s');
        $res  = $this->usermemberModel->allowField(true)->save($data,['id'=>$post['uid']]);
        if($res){
            return array('msg'=>'修改成功','code'=>200);
        }else{
            return array('msg'=>'修改失败','code'=>201);
        }

    }



    /**
     *修改手机号第二步
     */
    public function edit_mobile($post){

        if(empty($post['uid']) || empty($post['new_mobile']) || empty($post['yzm'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }

        $yzm = Cache::get($post['new_mobile']);
        if(trim($post['yzm'])!=$yzm){
            return array('code'=>201,'msg'=>'验证码错误');
        }
        $map['mobile'] = array('eq',trim($post['new_mobile']));
        $vo = db('usermember')->where($map)->find();
        if(!empty($vo)){
            return array('code'=>201,'msg'=>'该手机号已经存在');
        }
        $data['mobile'] = $post['new_mobile'];
        $res = db('usermember')->where('id',$post['uid'])->update($data);
        if($res){

            $yzm = Cache::set($post['new_mobile'],null);
            return array('code'=>200,'msg'=>'修改成功','data'=>$post);
        }else{
            return array('code'=>201,'msg'=>'修改失败');
        }
    }



    /**
     *解绑、绑定微信
     * type:1解绑，2绑定
     */
    public function three_bang($post){
        $data['update_time'] = date('Y-m-d H:i:s');
        if($post['type']==1){
            if($post['dx_type']==1){
                $data['wx_openid']          = '';
            }else if($post['dx_type']==2){
                $data['qq_openid']     = '';
            }

            $res  = $this->usermemberModel->allowField(true)->save($data,['id'=>$post['uid']]);
            if($res){
                return array('msg'=>'解绑成功','code'=>200);
            }else{
                return array('msg'=>'解绑失败','code'=>201);
            }
        }else if($post['type']==2){
            if($post['dx_type']==1){

                if(empty($post['wx_openid'])){
                    return array('msg'=>'获取openid失败','code'=>201);
                }
                $data['wx_openid']     = $post['wx_openid'];
                $wheres['wx_openid'] = array('eq',$post['wx_openid']);
                $wheres['id'] = array('neq',$post['uid']);
                $vo = db('usermember')->where($wheres)->find();
                if(!empty($vo)){
                    return array('msg'=>'该微信已绑定其他账号，不能重复绑定。','code'=>201);
                }
            }else if($post['dx_type']==2){
                if(empty($post['qq_openid'])){
                    return array('msg'=>'获取QQ信息失败','code'=>201);
                }
                $data['qq_openid']     = $post['qq_openid'];

                $wheres['qq_openid']   = array('eq',$post['qq_openid']);
                $wheres['id'] = array('neq',$post['uid']);
                $vo = db('usermember')->where($wheres)->find();
                if(!empty($vo)){
                    return array('msg'=>'该QQ已绑定其他账号，不能重复绑定。','code'=>201);
                }
            }

            $data['update_time'] = mydate();
            $res  = $this->usermemberModel->allowField(true)->save($data,['id'=>$post['uid']]);
            if($res){
                return array('msg'=>'绑定成功','code'=>200);
            }else{
                return array('msg'=>'绑定失败','code'=>201);
            }
        }


    }


    /**
     *消息未读数量
     */
    public function newMsgNum($post){
        $where['uid']    = array('eq',$post['uid']);
        $where['is_see'] = array('eq',0);
        $where['status'] = array('eq',1);
        //系统通知数量
        $where['type']   = array('eq',1);
        $result['count_a'] = db('message')->where($where)->count();
        $result['nosee_lista'] = db('message')->where($where)->order('id desc')->find();
        $result['nosee_lista']['create_time'] = mydate($result['nosee_lista']['create_time'],1);
        //订单通知数量
        $where['type']   = array('eq',2);
        $result['count_b'] = db('message')->where($where)->count();
        $result['nosee_listb'] = db('message')->where($where)->order('id desc')->find();
        $result['nosee_listb']['create_time'] = mydate($result['nosee_listb']['create_time'],1);
        return $result;
     }

    /**
     *消息列表
     */
    public function message($post){
        $where['uid']    = array('eq',$post['uid']);
        $where['type']   = array('eq',$post['type']);
        $where['status'] = array('eq',1);
		
		db('message')->where($where)->update(array('is_see'=>1)); //更改为已读

        $num = !empty($post['num']) ? $post['num'] : 20;
        $page = !empty($post['page']) && $post['page']>1 ? ($post['page']-1)*$num : 0;
        $list = db('message')->where($where)->order('id desc')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['create_time'] = mydate($ve['create_time']);
            $voret = [];
            if(!empty($ve['return_id'])){
                if($ve['return_type']==2){
                    $voret = db('course_study')->field('course_id,bar_id,uid as student_id')->find($ve['return_id']);//跳作品
                }else if($ve['return_type']==3){
                    $voret = db('course_order')->find($ve['return_id']); //跳课程
                }else if($ve['return_type']==4){
                    $voret['order_id'] = $ve['return_id']; //跳商品订单
                }else if($ve['return_type']==5){
                    $voret['student_id'] = $ve['return_id']; //班主任跳学生
                }
            }
            $list[$ke]['link_data'] = $voret;
        }
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }


    /**
     *意见反馈问题分类
     */
    public function fankui_type(){
        $where['pid']   = array('eq',5);
        $where['status'] = array('eq',1);
        $list = db('category')->where($where)->order('sort asc')->select();
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }


    /**
     *意见反馈提交
     */
    public function fankui_add($post){
        $post['create_time'] = time();
        $res  = db('fankui')->insert($post);
        if($res){
            return array('msg'=>'提交成功','code'=>200);
        }else{
            return array('msg'=>'提交失败','code'=>201);
        }
    }

    /**
     *意见反馈记录
     */
    public function fankui_list($post){
        $where['uid']   = array('eq',$post['uid']);
        $where['status'] = array('eq',1);
        $list = db('fankui')->where($where)->order('id desc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['create_time'] = mydate($ve['create_time'],2);
            $list[$ke]['type_title'] = modelField($ve['typeid'],'category','title');
            $list[$ke]['imgarr'] = imgArr($ve['imgarr']);
        }
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }




    /**
     *添加银行卡
     * see_type:1添加、编辑，2查看
     */
    public function add_bank($post){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试');
        }
        $where['uid'] = array('eq',$post['uid']);
        $bank = db('user_bank')->where($where)->find();
        if($post['see_type']==2){
            return array('code'=>200,'msg'=>'成功','data'=>$bank);
        }

        if(empty(trim($post['bank_number'])) || empty(trim($post['bank_username'])) || empty(trim($post['bank_name'])) || empty(trim($post['kh_bank']))){
            return array('code'=>201,'msg'=>'请完善银行卡信息');
        }
        unset($post['token']);

        if(!empty($bank)){
           $res = db('user_bank')->where('id',$bank['id'])->update($post);
        }else{
            $res = db('user_bank')->insertGetId($post);
        }
        if($res){
            return array('msg'=>'绑定成功','code'=>200);
        }else{
            return array('msg'=>'绑定失败','code'=>201);
        }
    }


    /**
     *大客户添加会员账户
     */
    public function bigkf_addvip($post){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试');
        }
        if(empty(trim($post['username'])) || empty(trim($post['company'])) || empty(trim($post['address']))){
            return array('code'=>201,'msg'=>'请完善信息');
        }
        if(Cache::get(trim($post['mobile']))!=$post['yzm']){
            return array('code'=>201,'msg'=>'验证码错误');
        }

        Db::startTrans();
        try{
            $where['mobile'] = array('eq',trim($post['mobile']));
            $user = db('usermember')->where($where)->find();

            $data['user_type']    = 2;
            $data['zhvisit_code'] = $post['zhvisit_code'];
            $data['update_time']  = mydate();

            $dtatb['pro_title'] =  provinceCityCounty($post,'zone_title');//省市区名称
            $dtatb['username']  = $post['username'];
            $dtatb['company']   = $post['company'];
            $dtatb['province_id'] = $post['province_id'];
            $dtatb['city_id']     = $post['city_id'];
            $dtatb['county_id']   = $post['county_id'];
            $dtatb['address']     = $post['address'];
            if(!empty($user)){
                if($user['user_type']!=1){
                    return array('code'=>201,'msg'=>'该手机号不是普通用户身份，禁止添加');exit;
                }


                $res = db('usermember')->where('id',$user['id'])->update($data);
                //副表
                $maps['uid'] = array('eq',$user['id']);
                $vo = db('userdata')->where($maps)->find();
                $dtatb['uid'] = $user['id'];
                if(!empty($vo)){
                    $dtatb['update_time'] = time();
                    db('userdata')->where('id',$vo['id'])->update($dtatb);
                }else{
                    db('userdata')->insert($dtatb);
                }

            }else{
                $data['codeid']    = userid(); //用户编号
                $data['mobile']    = trim($post['mobile']);
                $data['reg_time']  = mydate();
                $res = db('usermember')->insertGetId($data);

                $dtatb['uid'] = $res;
                db('userdata')->insert($dtatb);
            }
            Db::commit();
            return array('msg'=>'添加成功','code'=>200);
        }catch (\Exception $e){
            Db::rollback();
            return array('msg'=>'添加失败','code'=>201);
        }

    }



}
