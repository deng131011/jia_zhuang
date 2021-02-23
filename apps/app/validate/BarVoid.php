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

class BarVoid extends \think\Validate
{
    protected $rule = [
        //'mobile|手机号'  =>  'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'uid|会员id' =>  'require',
        'void|视频id' =>  'require',
        'muic_id|音乐id' =>  'require',
        'icon|图片id' =>  'require',
        'content|内容' =>  'require',
        'shop_id|商品id' =>  'require',
        'status|状态' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'create' =>['content','void','uid','icon'],
        'get'=>['category_id'],
    ];
    //错误提示
    protected $message = [
        'content.require'     => '请填写文案',
        'void.require'   => '请上传视频',
        'status.require'   => '请选择视频状态',
        'icon.require'   => '请上传视频封面图',
        'muic_id.require'   => '请上传音乐',
        'shop_id.require'   => '请选择关联商品',
        'uid.require'   => '缺少参数uid',
    ];


    //发帖
    public function createValidate(&$data){
        $validate=new BarVoid($this->rule,$this->message);
        $result = $validate->scene('create')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>$validate->getError(),'data'=>$data);
        }
        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }



}