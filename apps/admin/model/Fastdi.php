<?php

namespace app\admin\model;

use app\common\model\Base;

class Fastdi extends Base {

    protected $table='qyc_logistics_company';

    //表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写快递公司名称！');
        }
        if(empty($post['code'])){
            return array('code'=>201,'msg'=>'请填写快递公司编码！');
        }
        if($post['id']>0){
           $post['update_time'] = mydate();
        }else{
            $post['create_time'] = mydate();
        }
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);
    }




}
