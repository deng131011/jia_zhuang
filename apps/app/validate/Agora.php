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

class Agora extends \think\Validate
{
    protected $rule = [
        //'mobile|手机号'  =>  'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'icon|直播封面图' =>  'require',
        'title|直播标题' =>  'require',
        'post_title|主播职位' => 'require',
        'room_id|房间id' => 'require',
        //'password|新密码' => 'require|confirm|regex:/^(?=.*[0-9])(?=.*[a-zA-Z]).{8,32}$/',
        //'old_password|原密码' => 'require',
        'is_charge|是否收费' => 'require',
        'price|收费价格' => 'require',

        'recordId|录制id' => 'require',
        'roomId|房间id' => 'require',
        'senderId|会员的uid' => 'require',
        'channelName|房间名称编码' => 'require',
        's_token|声望会员token' => 'require',
    ];

    //场景验证
    protected $scene = [
        'open' =>['icon','title','post_title','is_charge'],
        'edit_roomId' =>['room_number','room_id'],
        'record' =>['recordId','roomId','senderId','channelName','s_token'],

    ];
    //错误提示
    protected $message = [
        'icon.require' => '请上传直播封面图',
        'title.require'     => '请填写直播标题',
        'post_title.require'   => '请填写主播职位',
        'room_id.require'   => '确少参数room_id',
        'room_number.require'   => '确少参数room_number',
        'is_charge.require'   => '请选择是否收费',
        'price.require'  => '请填写收费金额',


        'recordId.require'  => '缺少recordId',
        'roomId.require'  => '缺少roomId',
        'senderId.require'  => '缺少senderId',
        'channelName.require'  => '缺少channelName',
        's_token.require'  => '缺少s_token',


    ];


    //开直播
    public function openLiveValidate(&$data){
        $validate=new Agora($this->rule,$this->message);
        $result = $validate->scene('open')->check($data);
        if(!$result){
            Publics::ApiJson(201,$validate->getError(),$validate->getError(),$data);
        }
        //手动验证,金额
        if ($data['is_charge'] !=2){
            if (!isset($data['price']) || empty($data['price'])){
                Publics::ApiJson(201, '请输入收费金额', '请输入收费金额', $data);
            }

        }

    }

    //修改room_id
    public function editRoomIdValidate(&$data){
        $validate=new Agora($this->rule,$this->message);
        $result = $validate->scene('edit_roomId')->check($data);
        if(!$result){
            Publics::ApiJson(201,$validate->getError(),$validate->getError(),$data);
        }
    }


    //修改密码
    public function recordValidate(&$data){
        $validate=new Agora($this->rule,$this->message);
        $result = $validate->scene('record')->check($data);
        if(!$result){
            Publics::ApiJson(201,'网络连接失败，请稍后再试',$validate->getError(),$data);
        }
    }


}