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

class Invitation extends \think\Validate
{
    protected $rule = [
        //'mobile|手机号'  =>  'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'title|帖子标题' =>  'require',
        'category_id|帖子导航' =>  'require',
        'status|帖子导航' =>  'require',
    ];

    //场景验证
    protected $scene = [
        'create' =>['title','category_id','status'],
        'get'=>['category_id'],
    ];
    //错误提示
    protected $message = [
        'title.require'         => '请填写标题',
        'content.require'       => '请填写内容',
        'category_id.require'   => '请选择分类',
        'invitation_type.require'   => '请选择帖子发布类型',
    ];


    //发帖
    public function createValidate(&$data){
        $validate=new Invitation($this->rule,$this->message);
        $result = $validate->scene('create')->check($data);
        if(!$result){
            return array('code'=>201,'msg'=>$validate->getError(),'data'=>$data);
        }

        return array('code'=>200,'msg'=>'验证通过','data'=>$data);
    }




}