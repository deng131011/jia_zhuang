<?php

namespace app\common\model;

use app\common\model\Base;

class Activit extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写标题！');
        }
        if(empty($post['address'])){
            return array('code'=>201,'msg'=>'请填写地址！');
        }
        if(empty($post['dates'])){
            return array('code'=>201,'msg'=>'请选择日期！');
        }
        if(empty($post['times'])){
            return array('code'=>201,'msg'=>'请填写时间段！');
        }


        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);
    }




}
