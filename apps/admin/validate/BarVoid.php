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

class BarVoid extends \think\Validate
{
    protected $rule = [
        'void|视频'  =>  'require|max:50',
        'icon|封面图' =>  'require|gt:0',
        'content|视频文案' =>  'require',
        'recommend|推荐' =>  'require',
        'browse_number|浏览量' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'edit' =>['content','void','icon'],
    ];
    //错误提示
    protected $message = [
        'content.require' => '请填写视频文案',
        'void.require'     => '请上传视频',
        'icon.require'     => '请上传视频封面图',
    ];






}