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

class Follow extends \think\Validate
{
    protected $rule = [
        //'mobile|手机号'  =>  'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'uid|会员id' =>  'require',
        'type|类型' =>  'require',
        'target_id|目标id' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'set' =>['uid','type','target_id'],
        'get' =>['uid','type'],
    ];
    //错误提示
    protected $message = [
        'uid.require'     => '缺少参数uid',
        'type.require'   => '缺少参数type',
        'target_id.require'   => '缺少参数target_id',
    ];


    //关注，点赞
    public function setValidate(&$data){
        $validate=new Follow($this->rule,$this->message);
        $result = $validate->scene('set')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>'网络连接失败，请稍后再试','system_msg'=>$validate->getError(),'data'=>$data);
        }
        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }

    //关注，点赞
    public function getValidate(&$data){
        $validate=new Follow($this->rule,$this->message);
        $result = $validate->scene('get')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>'网络连接失败，请稍后再试','system_msg'=>$validate->getError(),'data'=>$data);
        }
        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }





}