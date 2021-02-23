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

class Invitation extends \think\Validate
{
    protected $rule = [
        'category_star_id|导航id'  =>  'require|max:50',
        'title|帖子标题' =>  'require|gt:0',
        'status|状态' =>  'require',
        'recommend|推荐' =>  'require',
        'top|推荐' =>  'require',
        'browse|浏览量' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'edit' =>['title','category_star_id'],
    ];
    //错误提示
    protected $message = [
        'title.require' => '请输入帖子名称',
        'category_star_id.require'     => '请选择导航',
    ];






}