<?php

namespace app\common\model;

use app\common\model\Base;

class AdPositon extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title']) || empty($post['size']) || empty($post['type'])){
            return array('code'=>201,'msg'=>'请完善信息！');
        }
    }

}
