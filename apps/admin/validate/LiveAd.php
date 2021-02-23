<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/20
 * Time: 14:00
 */
namespace app\admin\validate;
use app\api\controller\Publics;
use think\Cache;

class LiveAd extends \think\Validate
{
    protected $rule = [
       // 'content|内容'  =>  'require|max:50',
        'type|类型'  =>  'require',
        'target_id|内容'  =>  'require',
        'icon|图片'  =>  'require',
        'title|直播标题' =>  'require',

    ];

    //场景验证
    protected $scene = [
        'edit' =>['title','type','target_id','icon'],
    ];
    //错误提示
    protected $message = [
        'title.require' => '请填写标题',
        'type.require'     => '请选择类型',
        'target_id.require'     => '请选择内容',
        'icon.require'     => '请上传图片',

    ];






}