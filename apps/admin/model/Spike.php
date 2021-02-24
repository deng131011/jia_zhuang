<?php

namespace app\common\model;

use app\common\model\Base;

class Spike extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写秒杀标题！');
        }
        if(empty($post['start_time'])|| empty($post['end_time'])){
            return array('code'=>201,'msg'=>'请填写开始结束日期！');
        }
        $start_time = strtotime($post['start_time']);
        $end_time   = strtotime($post['end_time']);
        if($end_time<$start_time){
            return array('code'=>201,'msg'=>'结束日期不能小于开始日期！');
        }
        if($post['id']>0){
            $where['id']   = array('neq',$post['id']);
        }
        $where['end_time'] = array('gt',$start_time);
        $where['status']   = array('eq',1);
        $vo = db('spike')->where($where)->find();
        if(!empty($vo)){
            return array('code'=>201,'msg'=>'开始日期必须大于其他活动的结束日期！');
        }
        $post['start_time'] = $start_time;
        $post['end_time'] = $end_time;
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }

    //秒杀列表
    public function spike_list(){
        $where['status'] = array('eq',1);
        $where['end_time'] = array('gt',time());
        $list = db('spike')->where($where)->order('id desc')->select();
        return $list;
    }



    //添加秒杀商品
    public function addSpike($post){

        $where['spike_id'] = array('eq',$post['spike_id']);
        $list = db('spike_product')->where($where)->select();
        foreach ($list as $ke=>$ve){
            foreach ($post['product_id'] as $k=>$v){
                  if($ve['product_id']==$v){
                      return array('code'=>201,'msg'=>'产品：'.modelField($v,'product','title').'已存在');
                  }
            }
        }
        foreach ($post['product_id'] as $kk=>$vv){
            if(floatval($post['price_'.$vv])<=0 || intval($post['ms_stock_'.$vv])<=0){
                return array('code'=>201,'msg'=>'秒杀价格和数量必须大于0');exit;
            }
           $data[$kk]['spike_id']    = $post['spike_id'];
           $data[$kk]['product_id']  = $vv;
           $data[$kk]['price']       = $post['price_'.$vv];
           $data[$kk]['ms_stock']    = $post['ms_stock_'.$vv];
           $data[$kk]['mssy_stock']  = $post['ms_stock_'.$vv];
        }
        $res = db('spike_product')->insertAll($data);
        if($res){
            return array('code'=>200,'msg'=>'添加成功');
        }else{
            return array('code'=>201,'msg'=>'添加失败');
        }
    }




}
