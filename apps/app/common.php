<?php


/*
 * 获取header信息
 *
 * */
function getAllHeader(){
    foreach ($_SERVER as $name => $value)
    {
        if (substr($name, 0, 5) == 'HTTP_')
        {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

//验证TOKEN
function check_token($token){
    $arr['token'] = array('eq',$token);
    $value = db('token_value')->where($arr)->find();
    if(!$value){
        $data['code'] = 507;
        $data['msg'] = 'token验证失败';
        exit(json_encode($data));
    }
    if($value['invalid_time'] < time()){
        $data['code'] = 501;
        $data['msg'] = 'token已过期';
        exit(json_encode($data));
    }
    return true;
}

//头部信息
function headerInfo(){
    $header = apache_request_headers();
    $authorization = explode('_',$header['Password']);
    return $authorization;
}





