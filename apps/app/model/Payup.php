<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Pay as PayModel;
class Payup extends Model {


    /**
     *提交充值
     */
    public function add_payup($post){
        if(empty($post['pay_type']) || empty($post['uid']) || empty($post['price'])){
            return array('code'=>201,'msg'=>'缺少参数');
        }

        $data['price']     = floatval($post['price']);
        if($data['price']<=0 || empty($data['price'])){
            return array('msg'=>'充值金额必须大于0','code'=>201);
        }
        $data['order_number'] = order_number('payup_order');
        $data['uid']          = $post['uid'];
        $data['remark']       = '余额充值';
        $data['create_time']  = time();

        $map['uid'] = array('eq',$post['uid']);
        $map['pay_status'] = array('eq',0);
        $vo = db('payup_order')->where($map)->find();
        if($vo){
            $res = db('payup_order')->where('id',$vo['id'])->update($data);
        }else{
            $res = db('payup_order')->insert($data);
        }
        if($res){
            return array('msg'=>'提交成功','code'=>200,'data'=>$data);
        }else{
            return array('msg'=>'提交失败','code'=>201);
        }
    }


    /**
     *微信支付宝发起支付(充值)
     * pay_type:1支付宝，2微信
     **/
    public function weixin_alipay_payup($post){

        $where['order_number'] = array('eq',$post['order_number']);
        $where['pay_status']   = array('eq',0);
        $where['status']       = array('eq',1);
        $where['uid']          = array('eq',$post['uid']);
        $vo = db('payup_order')->where($where)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'充值订单不存在！');
        }
        $PayModel = new PayModel();

        if($post['pay_type']==1){
            $arr['ordernumber'] = $vo['order_number'];
            $arr['data_type']    = 1;
            $arr['title']        = '家居余额-充值';
            $arr['pay_price']    = $vo['price'];
            $arr['subject']      = '家居余额-充值';
            $arr['body']         = '会员充值支付';
            $arr['notify_url']   = config('index_url').'/App/Notify/payup_alipay_notify'; //回调地址
            $res = $PayModel->ali_pay($arr); //调取三方sdk
            return $res;
        }else if($post['pay_type']==2){
            $data['pay_price']    = $vo['price'];
            $data['data_type']    = 1;
            $data['ordernumber']  = $vo['order_number'];
            $data['body']         = '家居余额-充值';
            $data['notify_url']   = config('index_url').'/App/Notify/payup_wxpay_notify'; //微信充值回调地址
            $res = $PayModel->wx_pay($data);//调取三方sdk
            return $res;
        }
    }


    /**
     *余额充值成功回调
     * $pay_type:支付方式
     */
    public function payup_success($uid,$orderinfo,$pay_type=0,$log=[]){

        Db::startTrans(); //开启事务
        try{
            $userinfo = db('usermember')->lock(true)->find($uid);

            //改变余额
            $data1['balance']      = floatval($userinfo['balance'])+floatval($orderinfo['price']);
            $data1['update_time']  = mydate();
            $data1['id']           = $uid;
            $res1 = Db::table('qyc_usermember')->update($data1);
            //改变订单状态
            $res2 = Db::table('qyc_payup_order')->where('id',$orderinfo['id'])->update(array('pay_status'=>1,'pay_time'=>time(),'pay_type'=>$pay_type,'trade_log'=>$log));

            //添加流水表
            $data2 = addFlowater($uid,1,1,4,'余额充值',$orderinfo['price'],$data1['balance'],$orderinfo['id']);
            $res3 = Db::table('qyc_flowater')->insert($data2);
            // 提交事务
            Db::commit();
            return array('code'=>200,'msg'=>'支付成功！');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return array('code'=>201,'msg'=>'支付失败！');
        }
    }






}
