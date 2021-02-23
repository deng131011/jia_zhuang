<?php

namespace app\common\model;

use app\common\model\Base;

class Coupon extends Base {

    /*
    * 优惠券类型
    * open：1开启选项，2关闭选项
    * 如果不需要现在的选项，请关闭选项，但不能删除该选项。然后自己往下增加选项。
    * */
    public function type($type=1){
        $arr[1] = array(
            array('id'=>1,'title'=>'现金优惠券','open'=>1),
            array('id'=>2,'title'=>'满折优惠券','open'=>2),
            array('id'=>3,'title'=>'满减优惠券','open'=>2),
        );
        $arr[2] = ['1'=>'现金优惠券','2'=>'满折优惠券','3'=>'满减优惠券'];
        if($type==1){
            foreach ($arr[1] as $k=>$v){
                if($v['open']==2){
                    unset($arr[1][$k]);
                }
            }
        }

        return $arr[$type];
    }

    /*
     * 优惠券获得途径
     * open：1开启选项，2关闭选项
     * 如果不需要现在的选项，请关闭选项，但不能删除该选项。然后自己往下增加选项。
     * */
    public function coupon_gettype(){

        $arr = array(
               array('id'=>1,'title'=>'无条件赠送','open'=>1),
               array('id'=>2,'title'=>'分享赠送','open'=>1),
               array('id'=>3,'title'=>'普通邀请人赠送','open'=>1),
        );
        return $arr;
    }



    /**
     * 表单验证
     * */
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写优惠券名称！');
        }
        if(floatval($post['val'])<=0){
            return array('code'=>201,'msg'=>'请填写优惠券面额！');
        }
        if(empty($post['area_type'])){
            //return array('code'=>201,'msg'=>'请选择使用场景！');
        }

        if($post['get_type']==1){
            if(empty($post['end_time'])){
                return array('code'=>201,'msg'=>'请选择过期日期！');
            }
            $post['end_time'] = strtotime($post['end_time']);
        }else{
            if(intval($post['days'])<=0){
                return array('code'=>201,'msg'=>'请填写有效天数！');
            }
            $post['days'] = intval($post['days']);
        }

        if(!empty($post['id'])){
            $post['update_time'] = time();
        }else{
            $post['create_time'] = time();
        }
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);
    }



}
