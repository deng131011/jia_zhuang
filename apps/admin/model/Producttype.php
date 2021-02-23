<?php

namespace app\common\model;

use app\common\model\Base;

class Producttype extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写标题！');
        }
        if(empty($post['is_tjposition'])){
           // return array('code'=>201,'msg'=>'请选择推荐位状态！');
        }
    }

    //获取类别层级
    public function level($pid){

        if($pid == 0){
            return 1;
        }

        $product = model('Producttype')->where('id='.$pid)->field('level')->find();

        return $product['level']+1;

    }



}
