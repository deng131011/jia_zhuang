<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class Shop extends Base {

    protected  $table = 'qyc_shop';

    //表单验证
    public function checkform($post){
        if(empty(trim($post['title']))){
            return array('code'=>201,'msg'=>'请填写商家名称！');
        }
        if(empty($post['type1'])){
            return array('code'=>201,'msg'=>'请选择商家分类！');
        }
        if(empty(trim($post['mobile']))){
            return array('code'=>201,'msg'=>'请填写联系电话！');
        }
        if(empty(trim($post['address']))){
            return array('code'=>201,'msg'=>'请填写详细地址！');
        }
        if(empty($post['lng_lat'])){
            return array('code'=>201,'msg'=>'请获取经纬度！');
        }
        if(empty($post['yingye_time'])){
            return array('code'=>201,'msg'=>'请正确填写营业时间！');
        }
        if(floatval($post['zhe_kou'])<=0){
            $post['zhe_kou'] = 0;
        }
        if($post['is_food']==2 && empty($post['hotel_url'])){
            return array('code'=>201,'msg'=>'酒店商家请填写酒店链接');
        }
        if(!empty($post['flag'])){
            $post['flag'] = implode(',',$post['flag']);
        }else{
            $post['flag'] = '';
        }
        if(!empty($post['id'])){
            $post['update_time'] = time();
        }else{
            $post['create_time'] = time();

        }

        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }


}
