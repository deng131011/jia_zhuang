<?php

namespace app\common\model;

use app\common\model\Base;

class AppLoginad extends Base {

    public function checkform($post){
        if(empty($post['title']) || empty($post['linktype']) || empty($post['url'])|| empty($post['icon'])){
            return array('code'=>201,'msg'=>'请完善信息！');
         }
    }

}
