<?php

namespace app\app\model;
use think\Db;
use think\Model;
class Logistics extends Model {

    /**
     *物流信息API
     */
    public function logisticsApi($code='',$number='',$phone='',$from='',$to=''){
        //参数设置
        $key      = config('WEB_WULIU_KEY');						//客户授权key
        $customer = config('WEB_WULIU_COMPANYID');			//查询公司编号
      
        $param = array (
            'com' => $code,			//快递公司编码
            'num' => $number,	    //快递单号
            'phone' => $phone,			//手机号
            'from' => $from,			//出发地城市
            'to' => $to,				//目的地城市
            'resultv2' => '1'		//开启行政区域解析
        );

        //请求参数
        $post_data = array();
        $post_data["customer"] = $customer;
        $post_data["param"] = json_encode($param);
        $sign = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($sign);
        $url = 'http://poll.kuaidi100.com/poll/query.do';	//实时查询请求地址
        $params = "";
        foreach ($post_data as $k=>$v) {
            $params .= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $post_data = substr($params, 0, -1);

        //发送post请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = str_replace("\"", '"', $result );
        $data = json_decode($data);
        if($data->message=='ok'){
            return $data->data;
        }else{
            return '';
        }
    }


}
