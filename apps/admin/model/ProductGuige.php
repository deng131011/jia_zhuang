<?php

namespace app\admin\model;

use app\common\model\Base;

class ProductGuige extends Base {

    protected $table = 'qyc_product_guige';

    //表单验证
    public function checkform($post){
        if(empty($post['typeid'])){
            return array('code'=>201,'msg'=>'请填写规格类别！');
        }
        if(empty($post['gg_title'])){
            return array('code'=>201,'msg'=>'请填写规格名称！');
        }

        if($post['is_price']==1){
            if(floatval($post['cben_price'])<=0 || floatval($post['new_price'])<=0 || floatval($post['bigdl_price'])<=0 || floatval($post['dls_price'])<=0){
                return array('code'=>201,'msg'=>'请完善价格！');
            }
        }else{
            $post['cben_price'] = $post['old_price'] = $post['new_price'] = $post['bigdl_price'] = $post['dls_price'] = 0;
        }



        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }



}
