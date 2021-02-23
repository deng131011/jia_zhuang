<?php

namespace app\app\model;
use think\Cache;
use think\Db;
use think\Model;
class Wechat extends Model {

    /**
     *创建小程序二维码
     * $type:1用户注册二维码
     */
    public function small_ewm($data,$type=1){

        header('content-type:text/html;charset=utf-8');
        //配置APPID、APPSECRET
        $config = config('wechatConfig');
        $APPID =$config['AppID'];
        $APPSECRET =$config['AppSecret'];
        //获取access_token
        $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
        //缓存access_token
        $cache_token = Cache::get('access_token');
        if(empty($cache_token))
        {
            $json = $this->httpRequest2( $access_token );
            $json = json_decode($json,true);

            //如果超出token每日2000次限制
            if(!empty($json['errcode']) && $json['errcode']==45009){
                return ['code'=>201,'msg'=>'今日获取token超出限制'];
            }
            Cache::set('access_token',$json['access_token'],6500);
            $ACCESS_TOKEN = $json["access_token"];
        }else{
            $ACCESS_TOKEN =  $cache_token;
        }

        //构建请求二维码参数
        //path是扫描二维码跳转的小程序路径，可以带参数?id=xxx
        //width是二维码宽度
        $qcode ="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$ACCESS_TOKEN";
        if($type==1){
            $param = json_encode(array("path"=>"pages/logincode/logincode?visit_code=".$data['codeid'],"width"=>80));
        }
        //POST参数
        $result = $this->httpRequest2( $qcode, $param,"POST");

        $rst = json_decode($result,true);

        if(!empty($rst['errcode']) && $rst['errcode']==40001){
            $json = $this->httpRequest2( $access_token );
            $json = json_decode($json,true);
            Cache::set('access_token',$json['access_token'],6500);
            $ACCESS_TOKEN = $json["access_token"];
            $this->code($data,$type);
        }else{
            $png = time().'_'.$data['id'].'_'.$type.".png";
            //生成二维码
            $base_path = './uploads/qrcode/'.date('Y-m-d');
            if(!is_dir($base_path)){
                mkdir(iconv("UTF-8", "GBK", $base_path),0777,true);
            }
            $a=file_put_contents($base_path.'/'.$png, $result);
            if(!$a){
                return '';
            }
            $base64_image ="data:image/jpeg;base64,".base64_encode( $result );
            $path= substr($base_path.'/'.$png,1);
            return $path;
        }
    }


    //把请求发送到微信服务器换取二维码
    function httpRequest2($url, $data='', $method='GET'){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        if($method=='POST')
        {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data != '')
            {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }



}
