<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class Contact extends Base {


    protected $table = 'qyc_contact';

    //表单验证
    public function checkform($post){

        if(empty($post['type1'])){
            return array('code'=>201,'msg'=>'请选择分类！');
        }
        if(empty(trim($post['title']))){
            return array('code'=>201,'msg'=>'请填写单位名称！');
        }
        if(empty(trim($post['tel']))){
            return array('code'=>201,'msg'=>'请填写联系电话！');
        }
        if(empty(trim($post['oneline_time']))){
            return array('code'=>201,'msg'=>'请填写服务时间！');
        }
        if(!empty($post['id'])){
            $post['update_time'] = time();
        }else{
            $post['create_time'] = time();
        }
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }


}
