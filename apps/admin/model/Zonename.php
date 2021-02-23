<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class Zonename extends Base {

    protected  $table = 'qyc_zonename';

    //表单验证
    public function checkform($post){
        if(empty(trim($post['title']))){
            return array('code'=>201,'msg'=>'请填写名称！');
        }

        if(empty(trim($post['address']))){
            return array('code'=>201,'msg'=>'请填写详细地址！');
        }
        if(empty($post['lng_lat'])){
            return array('code'=>201,'msg'=>'请获取经纬度！');
        }
        if(!empty($post['id'])){
            $post['update_time'] = time();
        }else{
            $post['create_time'] = time();

        }

        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }


}
