<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Visit as VisitModel;
use app\app\model\OrderList as OrderListModel;
use app\app\model\ProductGuige as ProductGuigeModel;
/**
 *商品支付
 */
class Pay extends Model {

    /**
     *初始检测订单支付状态
     */
    public function start_check($ordernumber='',$uid=0,$model='order'){
        if($uid>0){
            $where['uid'] = array('eq',$uid);
        }
        $where['order_number'] = array('eq',$ordernumber);
        $where['pay_status']   = array('eq',0);
        $where['status']       = array('eq',1);
        $where['user_status']  = array('eq',1);
        $vo = db($model)->where($where)->find();
        return $vo;
    }


    /**------------------------------- 商品订单支付处理 -------------------------------------------*/


    /**
    *商品订单支付成功回调
     * $pay_type：1支付宝，2微信
    */
    public function pay_success($ordernumber,$pay_type=0,$log=''){
        $orderinfo = model('Pay')->start_check($ordernumber);
		
        if(empty($orderinfo)){
            return array('code'=>201,'msg'=>'订单不存在！');
        }
        $uid = $orderinfo['uid'];
        $userinfo   = db('usermember')->find($uid); //用户信息

        Db::startTrans();
        try{

            //修改订单支付状态
            $data1['pay_status']   = 1;
            $data1['pay_time']     = time();
            $data1['pay_type']     = $pay_type;
            $data1['trade_log']     = $log;

            if(!empty($log)){
                if($pay_type == 2){
                    $postObj = simplexml_load_string($log, 'SimpleXMLElement', LIBXML_NOCDATA );
                    $data1['trade_no']     = $postObj->transaction_id;
                }else if($pay_type == 1){
                    $logser =  json_decode($log,true);
                    $data1['trade_no']     = $logser['trade_no'];
                }
            }


            $res1 = db('order')->where('id',$orderinfo['id'])->update($data1);

            //更新用户表
            $data2['total_pay']   =  $userinfo['total_pay']+$orderinfo['pay_price'];//总消费
            $data2['update_time'] = time();
            $data2['id']          = $uid;
            $res2 = db('usermember')->update($data2);

            //添加平台流水
            $ii = 0;
            $water[$ii]  = addFlowater(0,2,1,1,'商城下单',$orderinfo['pay_price'],0,$orderinfo['id'],$orderinfo['uid']);
            $ii++;
            db('flowater')->insertAll($water);

            //更新销量和库存
            $res_sale = $this->save_sell_num($orderinfo);

            //添加系统消息
         /*   $it = 0;
            $msgarr[$it] = addMessageAll($uid,'订单支付成功','您的订单：'.$orderinfo['order_number'].'，您已支付成功，我们将尽快为你发货，请耐心等待，感谢您的支持！',2,1,$orderinfo['id']);
            $it++;
            db('message')->insertAll($msgarr);*/

            Db::commit();
            return array('code'=>200,'msg'=>'支付成功！');
        }catch (\Exception $e) {
            Db::rollback();
            return array('code'=>201,'msg'=>'支付失败！');
        }
    }


    /**
     *更新商品销量和库存
     */
    public function save_sell_num($orderinfo){
        $where['l.order_id'] = array('eq',$orderinfo['id']);
        $list = db('order_list l')->join('product p',' p.id = l.product_id')->where($where)->field('l.num,p.id as product_id,p.sale_num,p.freeze_stock')->select();

        foreach($list as $ke=>$ve){
            $datall[$ke]['id']              = $ve['product_id'];
            $datall[$ke]['sale_num']        = $ve['sale_num']+$ve['num'];
            $datall[$ke]['freeze_stock']    = $ve['freeze_stock']-$ve['num'];
        }

        if(!empty($datall)){
            $product = new Product();
            $res = $product->saveAll($datall);
        }

    }


    /**----------------------------------------------------- 酒店支付 --------------------------------------------------------------*/


    /**
     *酒店订单支付成功回调
     * $pay_type：1支付宝，2微信
     */
    public function hotel_pay_success($ordernumber,$pay_type=0,$log=''){
        $orderinfo = model('Pay')->start_check($ordernumber,'','hotel_order');
        if(empty($orderinfo)){
            return array('code'=>201,'msg'=>'订单不存在！');
        }
        $uid = $orderinfo['uid'];
        $userinfo   = db('usermember')->find($uid); //用户信息

        Db::startTrans();
        try{

            //修改订单支付状态
            $data1['pay_status']   = 1;
            $data1['pay_time']     = time();
            $data1['pay_type']     = $pay_type;
            $data1['trade_log']     = $log;

            if(!empty($log)){
                if($pay_type == 2){
                    $postObj = simplexml_load_string($log, 'SimpleXMLElement', LIBXML_NOCDATA );
                    $data1['trade_no']     = $postObj->transaction_id;
                }else if($pay_type == 1){
                    $logser =  json_decode($log,true);
                    $data1['trade_no']     = $logser['trade_no'];
                }
            }
            $res1 = db('hotel_order')->where('id',$orderinfo['id'])->update($data1);

            //更新用户表
            $data2['total_pay']   =  $userinfo['total_pay']+$orderinfo['pay_price'];//总消费
            $data2['update_time'] = time();
            $data2['id']          = $uid;
            $res2 = db('usermember')->update($data2);

            //添加平台流水
            $ii = 0;
            $water[$ii]  = addFlowater(0,2,1,2,'酒店下单',$orderinfo['pay_price'],0,$orderinfo['id'],$orderinfo['uid']);
            $ii++;
            db('flowater')->insertAll($water);

            //更新销量和库存
            db('hotel')->where('id',$orderinfo['hotel_id'])->setInc('sale_num');

            Db::commit();
            return array('code'=>200,'msg'=>'支付成功！');
        }catch (\Exception $e) {
            Db::rollback();
            return array('code'=>201,'msg'=>'支付失败！');
        }
    }





    /**----------------------------------------------------- 组装支付 --------------------------------------------------------------*/

    /**
    *组装（微信）支付参数
     */
    public function wx_pay($data){
        require_once "wxpay/lib/WxPay.Api.php";
        require_once "wxpay/example/WxPay.NativePay.php";

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        $input->SetOut_trade_no($data['ordernumber']); //订单号
        //$input->SetAttach($data['data_type']); //附加数据,1立即下单，2待支付支付

        $price = wx_pay_money($data['pay_price']); //支付金额
       
        $input->SetTotal_fee($price);//支付金额
        $input->SetNotify_url($data['notify_url']);



        $wechatConfig = config('wechatConfig');
        if(!empty($data['screen_type']) && $data['screen_type']==3){
            //小程序配置
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($data['openid']);
            $appid     = $wechatConfig['AppID'];
            $AppSecret = $wechatConfig['AppSecret'];
        }else{
            //APP配置
            $input->SetTrade_type("APP");
            $appid     = $wechatConfig['AppAppID'];
            $AppSecret = $wechatConfig['AppAppSecret'];
        }

        $input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);

        $config = new \WxPayConfig($appid,$AppSecret);

        $order = \WxPayApi::unifiedOrder($config, $input);

        if($order['result_code']=="SUCCESS"){
            $time = time();
            if(!empty($data['screen_type']) && $data['screen_type']==3){
                //小程序的
                $string = 'appId='.$order['appid'].'&nonceStr='.$order['nonce_str'].'&package=prepay_id='.$order['prepay_id'].'&signType=MD5&timeStamp='.$time.'&key='.$config->GetKey();
            }else{
                //APP的
                $string = "appid=".$order['appid']."&noncestr=".$order['nonce_str']."&package="."Sign=WXPay"."&partnerid=".$order['mch_id']."&prepayid=".$order['prepay_id']."&timestamp=".$time."&key=".$config->GetKey();
            }

            $string = md5($string);
            $order['sign']      = strtoupper($string);
            $order['timestamp'] = $time;
            return array('msg'=>'请求成功','code'=>200,'data'=>$order);
        }else{
             return array('msg'=>'请求失败','code'=>201,'error_msg'=>$order['return_msg']);
        }
    }

    /**
     *组装（微信）退款参数
     */
    public function wx_refund($data){
        require_once "wxpay/lib/WxPay.Api.php";
        require_once "wxpay/example/WxPay.NativePay.php";

        $input = new \WxPayRefund();
        $input->SetOut_trade_no($data['out_trade_no']);  //订单号
        //$input->SetTotal_fee($data['total_fee']*100);   //订单金额
       // $input->SetRefund_fee($data['refund_fee']*100);  //退款金额
		$input->SetTotal_fee(1);   //订单金额
        $input->SetRefund_fee(1);  //退款金额

        $config = new \WxPayConfig();
        $input->SetOut_refund_no($data['out_refund_no']);   //退款单号
        $input->SetOp_user_id($config->GetMerchantId());
       
        $result = \WxPayApi::refund($config, $input);
		
		return $result;

    }


    /**
    *支付宝
     */
    /**
     *组装（支付宝）支付参数
     */
    public function ali_pay($data){
        require_once 'alipay/config.php';
        require_once 'alipay/pagepay/service/AlipayTradeService.php';
        require_once 'alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
        $ordernumber = $data['ordernumber'];
        $price = alipay_money($data['pay_price']);

        $subject = $data['subject'];
        $body = $data['body'];
        $passback_params = $data['data_type'];

        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['merchant_private_key'];
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = $config['alipay_public_key'];
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();

        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"$body\","
            . "\"subject\": \"$subject\","
            . "\"out_trade_no\": \"$ordernumber\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"$price\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\","
            . "\"passback_params\":\"$passback_params\""
            . "}";
        $request->setNotifyUrl($data['notify_url']);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        //htmlspecialchars 是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        //就是orderString 可以直接给客户端请求，无需再做处理。
        return array('msg'=>'请求成功','code'=>200,'data'=>$response);
    }


    /**
     *组装（支付宝）退款参数
     */
    public function alipay_refund($data){
        require_once 'alipay/config.php';
        require_once 'alipay/pagepay/service/AlipayTradeService.php';
        require_once 'alipay/pagepay/buildermodel/AlipayTradeRefundContentBuilder.php';

        $data['refund_amount'] = 0.01;
        //构造参数
        $RequestBuilder=new \AlipayTradeRefundContentBuilder();
        $RequestBuilder->setOutTradeNo($data['out_trade_no']);  //订单号
        $RequestBuilder->setTradeNo($data['trade_no']);    //支付宝交易号
        $RequestBuilder->setRefundAmount($data['refund_amount']);  //退款金额
        $RequestBuilder->setOutRequestNo($data['out_request_no']);   //退款单号
        $RequestBuilder->setRefundReason($data['refund_reason']);  //退款原因
        $aop = new \AlipayTradeService($config);
        $response = $aop->Refund($RequestBuilder);
        return $response;
    }

}
