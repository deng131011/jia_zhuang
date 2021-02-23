<?php

namespace app\common\model;

use app\common\model\Base;

class Ad extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title']) || empty($post['position_id'])){
            return array('code'=>201,'msg'=>'请完善信息！');
        }

        if(empty($post['icon'])){
            return array('code'=>201,'msg'=>'请上传图片！');
        }

    }

    //广告位列表
    public function ad_position(){
        $list = db('ad_positon')->where(['status'=>1])->order('sort asc')->select();
        return $list;
    }



}
