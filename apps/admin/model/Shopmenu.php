<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class Shopmenu extends Base {

    protected  $table = 'qyc_shop_menu';

    //表单验证
    public function checkform($post){
        if(empty(trim($post['title']))){
            return array('code'=>201,'msg'=>'请填写名称！');
        }
        if(empty($post['type1'])){
            return array('code'=>201,'msg'=>'请选择分类！');
        }
        if(floatval($post['new_price'])<=0){
            return array('code'=>201,'msg'=>'请正确填写价格！');
        }
        if(!empty($post['id'])){
            $post['update_time'] = time();
        }else{
            $post['create_time'] = time();

        }

        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }


}
