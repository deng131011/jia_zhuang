<?php

namespace app\common\model;

use app\common\model\Base;

class Codes extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['type2'])){
            return array('code'=>201,'msg'=>'请选择分类');
        }
		if(intval($post['nums'])){
            return array('code'=>201,'msg'=>'数量必须大于0');
        }
		
        
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }





}
