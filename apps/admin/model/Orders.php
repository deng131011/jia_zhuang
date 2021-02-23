<?php

namespace app\admin\model;

use app\common\model\Base;
use app\app\model\Myorder as MyorderModel;
use think\Db;
use app\app\model\Wechat as WechatModel;
class Orders extends Base {

    protected  $table = 'qyc_order';


    //搜索条件
    public function search_form($get=array()){
        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
        }

        if($get['pay_status']=='dzf'){
            $where['b.pay_status'] = array('eq',0);
        }else if($get['pay_status']=='yzf'){
            $where['b.pay_status'] = array('eq',1);
        }

        if($get['order_status']=='wxd'){
            $where['b.pay_status'] = array('eq',0);
            $where['b.order_status'] = array('eq',0);
        }
        if($get['order_status']=='dfh'){
            $where['b.pay_status'] = array('eq',1);
            $where['b.order_status'] = array('eq',0);
        }
        if(intval($get['order_status'])>0){
            $where['b.pay_status'] = array('eq',1);
            $where['b.order_status'] = array('eq',intval($get['order_status']));
        }
        if(!empty($get['keywords'])){
            $where['b.order_number|b.order_user|b.order_mobile'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status'] = array('gt',-1);
        return $where;
    }


    /**
     *删除订单
     */
    public function delOrder($post){
        if(is_array($post['ids'])){
            $where['id'] = array('in',implode(',',$post['ids']));
        }else{
            $where['id'] = array('eq',$post['ids']);
        }
        $list = db('order')->where($where)->select();
        foreach ($list as $ke=>$ve){
            if($ve['pay_status']>0){
                return array('code'=>201,'msg'=>'只能删除待支付订单');exit;
            }
        }

        $res = db('order')->where($where)->delete();
        if($res){
            return array('code'=>200,'msg'=>'删除成功');exit;
        }else{
            return array('code'=>201,'msg'=>'删除失败');exit;
        }
    }

    /**
     *更改订单状态
     */
    public function changStatus($post){
        if($post['order_status']=='dfh'){
            $data['order_status'] = 0;
        }else if($post['order_status']=='yfh'){

			if(empty($post['wuliu_code'])){
				return array('code'=>201,'msg'=>'请选择快递');
			}
			if(empty($post['wuliu_number'])){
				return array('code'=>201,'msg'=>'请填写快递单号');
			}
            $data['order_status'] = 1;
            $data['wuliu_code']   = $post['wuliu_code'];
            $data['wuliu_number'] = $post['wuliu_number'];
            $content = '商家已发货，物流单号：'.$post['wuliu_number'].'，快递公司：'.$post['wuliu_code'];
        }else if($post['order_status']=='qrsh'){
            $data['order_status'] = 2;
            $content = '已收货';
        }else if($post['order_status']=='ypj'){
            $data['order_status'] = 3;
            $content = '已评价';
        }

        Db::startTrans();
        try{
            $where['id'] = array('in',$post['order_id']);
            $res = db('order')->where($where)->update($data);

            $orderinfo = db('order')->find($post['order_id']);

            //确认收货判断是否需要返利
            if($post['order_status']=='qrsh'){
                $MyorderModel = new MyorderModel();
                //$MyorderModel->visit_return_price($post['uid'],$post['order_id'],1);
            }

            //添加操作记录
            order_use_recode($post['order_id'],$post['uid'],2,1,$content);

            //消息提示
            if($post['order_status']=='yfh'){
                addMessage($post['uid'],'订单提示','您的订单：'.$orderinfo['order_number'].' 已发货，请注意查收。',2,1,$post['order_id']);
            }

            Db::commit();
            return array('code'=>200,'msg'=>'提交成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return array('code'=>201,'msg'=>'提交失败');
        }
    }
	
	
	
	
	
	



}
