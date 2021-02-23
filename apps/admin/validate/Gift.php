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

class Gift extends \think\Validate
{
    protected $rule = [
        'title|礼物名称'  =>  'require|max:50',
        'le_bi|礼物乐币' =>  'require|gt:0',
        'icon|礼物图片' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'add' =>['title','le_bi','icon'],
    ];
    //错误提示
    protected $message = [
        'title.require' => '请输入礼物名称',
        'le_bi.require'     => '请填写乐币',
        'icon.require'     => '请上传礼物图片',
    ];






}