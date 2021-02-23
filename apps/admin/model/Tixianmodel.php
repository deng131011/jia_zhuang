<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/18
 * Time: 17:06
 */

namespace app\admin\model;
use app\common\model\Base;
use think\Db;

/**
 * Class TopicMake
 * @package app\api\model
 * 做题模型
 */
class Tixianmodel extends Base{
    public function sendMoney($post=[]){
        $secrect_key = 'sa4s0A4fS2za4FSCXsZ4d11A2S4A52Zd';//API密码
        $mchid       = '1602265876';//商户号
        $mch_appid   = 'wxb61dad6f07a8b40c';//商户账号appid

        $total_amount = (100) * $post['total_amount'];
        $data=array(
            'mch_appid'         => $mch_appid,//商户账号appid
            'mchid'             => $mchid,//商户号
            'nonce_str'         => $this->createNoncestr(),//随机字符串
            'partner_trade_no'  => $post['partner_trade_no'],//商户订单号
            'openid'            => $post['openid'],//用户openid
            'check_name'        => 'NO_CHECK',//校验用户姓名选项,
            're_user_name'      => $post['check_name'],//收款用户姓名
            'amount'            => $total_amount,//金额
            'desc'              => $post['desc'],//企业付款描述信息
            'spbill_create_ip'  => '',//Ip地址
        );

        $data = array_filter($data);
        ksort($data);
        $str ='';
        foreach($data as $k=>$v) {
           $str.=$k.'='.$v.'&';
        }
        $str.='key='.$secrect_key;

        $data['sign'] = md5($str);
   
        $xml = $this->arraytoxml($data);
       
        
        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //调用接口
        $res = $this->wx_curl($xml,$url);
        $return = $this->xmltoarray($res);
        
        $responseObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);

        if($responseObj->result_code=='SUCCESS'){
            return ['code'=>200,'msg'=>'提现成功'];
        }else{
            if($responseObj->err_code=='SENDNUM_LIMIT'){
                return ['code'=>201,'msg'=>'每天最多只能提现两次'];
            }else{
                return ['code'=>201,'msg'=>'提现失败：'.$responseObj->err_code_des[0]];
            }
        }

    }
     

    public function createNoncestr($length =32){
       /* $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  */
		$str = str_rand(32);
        return $str;
    }
     
    public function arraytoxml($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }
     
    public function xmltoarray($xml) { 
        //禁止引用外部xml实体 
        libxml_disable_entity_loader(true); 
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $val = json_decode(json_encode($xmlstring),true); 
        return $val;
    } 
     
    public function wx_curl($vars,$url,$second = 30, $aHeader = array()) {

        $isdir = ROOT_PATH."public_html/wxpay/lib/cacert/";//证书位置

        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置执行最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//证书类型
        curl_setopt($ch, CURLOPT_SSLCERT, $isdir . 'apiclient_cert.pem');//证书位置
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');//CURLOPT_SSLKEY中规定的私钥的加密类型
        curl_setopt($ch, CURLOPT_SSLKEY, $isdir . 'apiclient_key.pem');//证书位置
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);//设置头部
        }
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);//全部数据使用HTTP协议中的"POST"操作来发送
     
        $data = curl_exec($ch);//执行回话
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }


}