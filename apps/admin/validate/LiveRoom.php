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

class LiveRoom extends \think\Validate
{
    protected $rule = [
       // 'one_brief|个人介绍'  =>  'require|max:50',
     //   'zb_brief|直播介绍' =>  'require|gt:0',
      //  'title|直播标题' =>  'require',

    ];

    //场景验证
    protected $scene = [
        'edit' =>['title','one_brief','zb_brief'],
    ];
    //错误提示
    protected $message = [
       // 'title.require' => '请填写直播标题',
       // 'one_brief.require'     => '请填写个人介绍',
       // 'zb_brief.require'     => '请填写直播介绍',
    ];






}