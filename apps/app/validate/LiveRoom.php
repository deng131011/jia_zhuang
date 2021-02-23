<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/20
 * Time: 14:00
 */
namespace app\app\validate;
use app\api\controller\Publics;
use think\Cache;
use think\Db;

class LiveRoom extends \think\Validate
{
    protected $rule = [
        //'mobile|手机号'  =>  'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'title|帖子标题' =>  'require',
        'one_brief|个人介绍' =>  'require',
        'zb_brief|直播介绍' =>  'require',
        'uid|会员id' =>  'require',
        'live_id|直播id' =>  'require',
        'token|会员token' =>  'require',
        'type|类型' =>  'require',
        'url|路径' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'set' =>['title','one_brief','zb_brief','uid','live_id','token'],
        'live_data' =>['uid','type','token'],
        'live_rec' =>['uid','room_number','sw_token'],
        'live_rec_end' =>['uid','resourceId','sid','room_number'],
        'live_search' =>['type'],

    ];
    //错误提示
    protected $message = [
        'title.require'     => '请填写直播间标题',
        'one_brief.require'   => '请完善个人介绍',
        'zb_brief.require'   => '请完善直播介绍',
        'uid.require'   => '网络连接失败，请稍后再试',
        'live_id.require'   => '网络连接失败，请稍后再试',
        'token.require'   => '网络连接失败，请稍后再试',
        'type.require'   => '网络连接失败，请稍后再试',
        'url.require'   => '网络连接失败，请稍后再试',
    ];


    //设置直播数据
    public function setValidate(&$data){
        $validate=new LiveRoom($this->rule,$this->message);
        $result = $validate->scene('set')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>$validate->getError(),'data'=>$data);
        }

        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }

    //查看直播数据
    public function liveDataValidate(&$data){
        $validate=new LiveRoom($this->rule,$this->message);
        $result = $validate->scene('live_data')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>$validate->getError(),'data'=>$data);
        }

        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }

    //开启录制
    public function liveRecValidate(&$data){
        $validate=new LiveRoom($this->rule,$this->message);
        $result = $validate->scene('live_rec')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>$validate->getError(),'data'=>$data);
        }
        //手动验收房间是否存在
        $map['room_number']=$data['room_number'];
        $live=Db::table('qyc_live_room')->where($map)->find();
        if ($live){
            $data['live_id']=$live['id'];
        }else{
            return array('code'=>201,'msg'=>'当前房间不存在，或被管理员删除','data'=>$data);
        }

        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }

    //结束录制
    public function liveRecEndValidate(&$data){
        $validate=new LiveRoom($this->rule,$this->message);
        $result = $validate->scene('live_rec_end')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>$validate->getError(),'data'=>$data);
        }
        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }





}