<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
namespace app\app\controller;
use think\Cache;
use think\Controller;
class Wechat extends Controller {
    /*************************************微信推送********************************************************/
    public function responseMsg($param)
    {
        $wxconfig = config('wechatConfig');

        $encodingAesKey = "FEY3FFrN0IHdTvMRorRJpQH0EUegT1wttfOP2jupJVW";
        $token = "shzizhangmenjyjg56678310";
        $appId = "wx1c72cee684e12c6b";

        //get post data, May be due to the different environments
        $postStr = file_get_contents('php://input');

        // 第三方发送消息给公众平台
        $timeStamp =$param["timestamp"];
        $nonce = $param["nonce"];
        $msg_sign = $param["msg_signature"];
        Vendor('wxdecryptMsg.wxBizMsgCrypt');
        $pc = new \WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $postStr, $msg);

        // 获取参数

        //extract post data
        if (!empty($msg)){
                $postObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);

                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $type = $postObj->MsgType;
                $customevent = $postObj->Event;

            
                if($type=="event" && $customevent=="subscribe"){
                    $message=db('category')->where(['pid'=>26,'status'=>1])->order('sort asc,create_time desc')->select();
                    if($message){
                        foreach($message as $k=>$v){
                            
                            $contentStr=$v['push_msg'];
                            $this->kefu($param['openid'],$contentStr);
          
                        }
                        
                    }

                    //$this->guanzhu_message_push($fromUsername,$toUsername,time());

                }
        }else {
            echo "";
            exit;
        }
    }

    public function kefu($userid,$content){

        $access_token=$this->get_access_token();

        $url ="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$access_token";
        $postArr = '{
            "touser":"'.$userid.'",
            "msgtype":"text",
            "text":
            {
                 "content":"'.$content.'"
            }
        }';
        return $this->http_curl($url,'post','json',$postArr);

    }
    //关注推送消息
    public function guanzhu_message_push($fromUsername,$toUsername,$time,$msgType="text"){
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>
                    ";    
        $message=db('category')->where(['pid'=>26,'status'=>1])->order('sort asc,create_time desc')->select();
        if($message){
            foreach($message as $k=>$v){
                
                $contentStr=$v['push_msg'];
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;            
            }

        }

    } 

  
    /*************************************微信推送（end）********************************************************/



    public function gzh_openid(){

        $get = $_REQUEST;

        $data = json_encode($get);
        $data = json_decode($data,true);

        $whe['gzh_openid'] = array('eq',trim($data['openid']));
        $vo = db('usermember')->where($whe)->find();

        if(empty($vo)){
            $mpp['gzh_openid'] = array('eq',trim($data['openid']));
            $wxres = db('wxinfo')->where($mpp)->find();
            if(!empty($wxres)){
                $arr['gzh_openid'] = $wxres['gzh_openid'];
                $arr['update_time'] = time();
                $wherew['unionid'] = array('eq',trim($wxres['unionid']));
                db('usermember')->where($wherew)->update($arr);
            }else{
                $access_token=$this->get_access_token();

                $url ='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$data['openid'].'&lang=zh_CN';
                $info_res = file_get_contents($url);
                $wxinfo = json_decode($info_res,true);

                if(!empty($wxinfo['unionid'])){
                    $where['unionid'] = array('eq',trim($wxinfo['unionid']));
                    $user = db('usermember')->where($where)->find();
                    if(!empty($user)){
                        $arr['gzh_openid'] = $wxinfo['openid'];
                        $arr['update_time'] = time();
                        db('usermember')->where('id',$user['id'])->update($arr);

                    }else{

                        $dataer['unionid']    = trim($wxinfo['unionid']);
                        $dataer['gzh_openid'] = trim($wxinfo['openid']);
                        $dataer['nickname']   = $wxinfo['nickname'];
                        $dataer['headimgurl']   = $wxinfo['headimgurl'];
                        $dataer['update_time']   = time();

                        $whereert['unionid'] = array('eq',trim($wxinfo['unionid']));
                        $vob = db('wxinfo')->where($whereert)->find();

                        if(empty($vob)){

                            $rest = db('wxinfo')->insertGetId($dataer);
                        }else{

                            $maps['id'] = array('eq',$vob['id']);
                            $maps['unionid'] = array('eq',trim($wxinfo['unionid']));
                            $rest = db('wxinfo')->where($maps)->update($dataer);
                        }

                    }
                }
            }
        }else{
            //file_put_contents('wxxx.txt',2454);
        }

        $this->responseMsg($get);


exit();

        //用于微信后台验证代码
        file_put_contents('wxxx.txt',json_encode($get));
        $signature = $get["signature"];
        $timestamp = $get["timestamp"];
        $nonce = $get["nonce"];
        $token = 'shzizhangmenjyjg56678310';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            file_put_contents('wxxx1.txt',1);
            return $get['echostr'];
        }else{
            file_put_contents('wxxx1.txt',2);
            return false;
        }

    }

    public function get_access_token(){
        $wxconfig = config('wechatConfig');
        $appid = $wxconfig['GzhAppID'];
        $appsecret = $wxconfig['GzhAppSecret'];
        $acctoken = Cache::get('gzh_access_token');
        if(empty($acctoken)){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
            $res = file_get_contents($url);
            $acce = json_decode($res,true);
            Cache::set('gzh_access_token',$acce['access_token'],3600);
            $access_token = $acce['access_token'];
        }else{
            $access_token = $acctoken;
        }
        return $access_token;
    }


   


    //$url  接口url string
    //$type 请求类型string
    //$res  返回类型string
    //$arr= 请求参数string
    public function http_curl($url,$type='get',$res='json',$arr=''){
        try{
            //1.初始化curl
            $ch  =curl_init();
            //2.设置curl的参数
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

            if($type == 'post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
            }
            //3.采集
            $output =curl_exec($ch);

            //4.关闭
            curl_close($ch);
            $return =json_decode($output,true);
     
            if($res=='json'){

                if($return['errcode']!=0){
                    //请求失败，返回错误信息
                    return curl_error($ch);
                }else{
                    //请求成功，返回错误信息

                    return json_decode($output,true);
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}