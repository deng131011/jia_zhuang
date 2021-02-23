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

class LiveNotice extends \think\Validate
{
    protected $rule = [
       // 'content|内容'  =>  'require|max:50',
        'content|内容'  =>  'require',
        'title|直播标题' =>  'require',

    ];

    //场景验证
    protected $scene = [
        'edit' =>['title','content'],
    ];
    //错误提示
    protected $message = [
        'title.require' => '请填写标题',
        'content.require'     => '请填写内容',

    ];






}