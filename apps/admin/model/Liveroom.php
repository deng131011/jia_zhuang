<?php

namespace app\admin\model;

use app\common\model\Base;

class Liveroom extends Base {

    protected $table='qyc_live_room';

//表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写直播标题！');
        }
        if(empty($post['teacher'])){
            return array('code'=>201,'msg'=>'请填写讲师名称！');
        }

        if($post['free_status']==2 || $post['free_status']==3){
            if(floatval($post['new_price'])<=0){
                return array('code'=>201,'msg'=>'请填写价格！');
            }
        }
        if(empty($post['open_date'])){
            return array('code'=>201,'msg'=>'请选择直播日期！');
        }
        if(empty($post['start_time'])){
            return array('code'=>201,'msg'=>'请选择直播时间！');
        }else{
            $time = explode('-',$post['start_time']);
            $post['start_time'] = $time[0];
            $post['over_time']  = $time[1];
        }

        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }




}
