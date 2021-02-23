<?php

namespace app\admin\model;

use app\common\model\Base;
use app\app\model\Pay as PayModel;
use app\app\model\Order as OrderModel;
use think\Db;

class Buyafter extends Base {

    /**
     *判断是否要退运费
     */
    public function check_send_price($buyafter){
        $msdfd['uid'] = array('eq',$buyafter['uid']);
        $msdfd['id']  = array('eq',$buyafter['order_id']);
        $order = db('order')->where($msdfd)->find();

        if(empty($order)){
            return ['code'=>201,'msg'=>'订单不存在'];
        }

        $where['l.order_id']       = array('eq',$buyafter['order_id']);
        $where['l.id']             = array('neq',$buyafter['order_listid']);
        $where['l.after_status']   = array('neq',5);
        $vo = db('order o')->join('order_list l','o.id = l.order_id')->where($where)->field('o.*')->find();
        $end_status = !empty($vo) ? 1 : 2;//1未完成，2已完成
        if($order['order_status']==0){

            if(!empty($vo) || floatval($order['send_price'])<=0){
                return ['code'=>200,'msg'=>'成功','order'=>$order,'end_status'=>$end_status];
            }else{
                //该订单已经全部退款
                return ['code'=>202,'msg'=>'本次退款会增加'.$order['send_price'].'元运费','send_price'=>$order['send_price'],'order'=>$order,'end_status'=>$end_status];
            }
        }else{
            return ['code'=>200,'msg'=>'成功','order'=>$order,'end_status'=>$end_status];
        }
    }


    /**
     *退款
     */
    public function return_price($post){

        $mapt['id'] = array('eq',$post['buyafter_id']);
        $mapt['order_number'] = array('eq',$post['tkorder_number']);
        $buyafter = db('buyafter')->where($mapt)->find();

        if($buyafter['refund_status']==1){
            return array('code'=>201,'msg'=>'不能重复退款！');exit;
        }

        if($buyafter['apply_type']==1){
            if($buyafter['chuli_status']!=2){
                return array('code'=>201,'msg'=>'非法操作！');exit;
            }
        }
        if($buyafter['apply_type']==2){
            if($buyafter['chuli_status']!=4){
                return array('code'=>201,'msg'=>'非法操作！');exit;
            }
        }


        Db::startTrans();
        try{
            $send_price = 0;
            $end_status = 1;
            if($buyafter['type']==1){
                $rest = $this->check_send_price($buyafter);
                if($rest['code']==201){
                    Db::rollback();
                    return $rest;
                }else if($rest['code']==202){
                    $send_price = $rest['send_price'];
                }
                $order = $rest['order'];
                $end_status = $rest['end_status'];
            }


            $online_return_price = 0;
            if($buyafter['type']==1){
                //更新退货申请表
                $ture_price = floatval($send_price) + floatval($buyafter['apply_price'])+floatval($buyafter['yue_price']); //实际退回金额
                $online_return_price = $ture_price-floatval($buyafter['yue_price']);
                $res1 = db('buyafter')->where('id',$buyafter['id'])->update(array('send_price'=>$send_price,'refund_price'=>$ture_price,'refund_status'=>1,'chuli_status'=>5,'refund_time'=>time()));

                //商品退款
                $res1 = db('order_list')->where('id',$buyafter['order_listid'])->update(array('after_status'=>5));

                if($end_status==2){
                      //将订单修改成全部退款
                    db('order')->where('id',$order['id'])->update(array('order_status'=>4,'update_time'=>time(),'close_time'=>time()));
                }

                //添加操作记录
                order_use_recode($buyafter['order_id'],0,2,9,'平台已退款',$buyafter['order_listid'],1);
                //添加系统消息
                addMessage($buyafter['uid'],'退款申请通知','您的退款申请，平台已退款',3,2,$buyafter['id']);
               $zf_type = 3;
               $remark  = '商品退款';
            }

            //余额
            $ii = 0;
            if(floatval($buyafter['yue_price'])>0){
                $userinfo =  db('usermember')->lock(true)->find($buyafter['uid']);

                $yue_data['balance']     = $userinfo['balance'] + floatval($buyafter['yue_price']);
                $yue_data['update_time'] = mydate();
                db('usermember')->where('id',$buyafter['uid'])->update($yue_data);

                //余额流水
                $water[$ii]  = addFlowater($buyafter['uid'],1,1,$zf_type,$remark,floatval($buyafter['yue_price']),$yue_data['balance'],$buyafter['id'],$buyafter['uid']);
                $ii++;
            }

            //添加平台流水
            $water[$ii]  = addFlowater(0,2,2,$zf_type,$remark,round(floatval($ture_price),2),0,$buyafter['id'],$buyafter['uid']);
            $ii++;
            db('flowater')->insertAll($water);


            if($order['pay_type']==2 && $online_return_price>0){
                //微信退款
                /*$PayModel = new PayModel();
                $arr['out_trade_no']  = $order['order_number'];//商户订单号
                $arr['out_refund_no'] = $buyafter['order_number'];//退款单号
                $arr['screen_type']   = $order['screen_type'];
                $arr['total_fee']     = round(floatval($order['pay_price']),2);
                $arr['refund_fee']    = round(floatval($online_return_price),2);

                $result = $PayModel->wx_refund($arr);//调起支付
                $wxrefund_result = json_encode($result);
                file_put_contents('ddd.txt',$wxrefund_result);
                if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS'){
                    //存入微信退款结果
                    db('buyafter')->where('id',$buyafter['id'])->update(array('wxrefund_result'=>$wxrefund_result));
                }else{
                    Db::rollback();
                    return array('code'=>201,'msg'=>'退款失败：'.$result['err_code_des']);
                }*/

            }else if($order['pay_type']==1 && $online_return_price>0){
                //支付宝退款
                /*$PayModel = new PayModel();
                $arr['out_trade_no']   = $order['order_number'];//订单号
                $arr['trade_no']       = $order['trade_no'];//支付宝交易号
                $arr['refund_amount']  = round(floatval($online_return_price),2);//退款金额
                $arr['out_request_no'] = $buyafter['order_number'];//退款单号
                $arr['refund_reason']  = "售后申请退款";//退款原因
                $result = $PayModel->alipay_refund($arr);//调起支付
                $alipay_result = json_encode($result);
                file_put_contents('dddd.txt',$alipay_result);
                if($result['code']=='10000' && $result['msg']=='Success'){
                    //存入退款结果
                    db('buyafter')->where('id',$buyafter['id'])->update(array('wxrefund_result'=>$alipay_result));
                }else{
                    Db::rollback();
                    return array('code'=>201,'msg'=>'退款失败：'.$result['msg']);
                }*/
            }
            Db::commit();
            return array('code'=>200,'msg'=>'退款成功！');
        }catch (\Exception $e){
            Db::rollback();
            return array('code'=>201,'msg'=>'退款失败！');
        }
    }




}
