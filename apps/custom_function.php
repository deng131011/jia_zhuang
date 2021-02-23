<?php
//自定义函数方法（二次开发全局的函数写在该文件，防止程序更新覆盖）


//微信支付金额
function wx_pay_money($price){
    return 1;
    return $price*100;
}
//支付宝支付金额
function alipay_money($price){
    return 0.01;
    return $price;
}

/**
*生成用户编号（注册时生成）
 */
function userid(){
    $rand = str_rand(8);
    $where['codeid'] = array('eq',$rand);
    $res = db('usermember')->where($where)->find();
    if(!empty($res)){
        userid();
    }else{
        return $rand;
    }
}

/**
 *生成随机数
 */
function str_rand($length = 16, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    if(!is_int($length) || $length < 0) {
        return false;
    }
    $string = '';
    for($i = $length; $i > 0; $i--) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
    }
    return $string;
}

/**
 *添加订单操作记录
 */
function order_use_recode($order_id,$uid=0,$pt_type=1,$type='',$content='',$order_listid=0,$dx_type=1){
    $data['order_id']      = $order_id;
    $data['order_listid']  = $order_listid;
    $data['uid']           = $uid;
    $data['dx_type']       = $dx_type;
    $data['type']          = $type;
    $data['pt_type']       = $pt_type;
    $data['content']       = $content;
    $data['create_time']   = time();
    $res = db('order_recode')->insertGetId($data);
    return $res;
}

/**
 *添加系统消息
 */
function addMessage($uid=0,$title='',$content='',$type=1,$return_type=1,$return_id=0){
    $data['uid']            = $uid;
    $data['title']          = $title;
    $data['content']        = $content;
    $data['type']           = $type;
    $data['return_type']    = $return_type;
    $data['return_id']      = $return_id;
    $data['create_time']    = time();
    $res = db('message')->insertGetId($data);
    return $res;
}


/**
 *批量添加系统消息
 */
function addMessageAll($uid=0,$title='',$content='',$type=1,$return_type=1,$return_id=0){
    $data['uid']      = $uid;
    $data['title']    = $title;
    $data['content']  = $content;
    $data['type']     = $type;
    $data['return_type']    = $return_type;
    $data['return_id']      = $return_id;
    $data['create_time']     = time();
    return $data;
}

/**
 *添加流水
 */
function addFlowater($uid=0,$pt_type=1,$type=1,$zf_type=0,$remark='',$pay_price=0,$balance=0,$return_id=0,$return_uid=0){
    $data['uid']            = $uid;
    $data['pt_type']        = $pt_type;
    $data['type']           = $type;
    $data['zf_type']        = $zf_type;
    $data['pay_price']      = $pay_price;
    $data['balance']        = $balance;
    $data['remark']         = $remark;
    $data['return_id']      = $return_id;
    $data['return_uid']      = $return_uid;
    $data['create_time']    = time();
    return $data;
}



/**
 * 获取指定年月的开始和结束时间戳
 */
function monthStartEnd($y='',$m=''){
    $y = $y ? $y : date('Y');
    $m = $m ? $m : date('m');
    $d = date('t', strtotime($y.'-'.$m));
    return array("firsttime"=>strtotime($y.'-'.$m),"lasttime"=>mktime(23,59,59,$m,$d,$y));
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


/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）\
 */
function substr_cuttitle($user_name){
    if(empty($user_name)){
        return '';
    }
    $strlen     = mb_strlen($user_name, 'utf-8');
    $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}


/**
 *创建二维码
 * $type:1邀请二维码
 */
function create_ewm($type=1,$id=0){
    if($type==1){
        $path    = 'uploads/qrcode/'.date('Ymd').'/';
        $url     = config('index_url').'/home/share/index?visit_id='.$id;
    }
    $imgname = date('YmdHis').'_'.$id;
    qrcode($url,$path,$imgname);
    //存入二维码路径
    $ewm_url = '/'.$path.$imgname.'.png';
    return $ewm_url;
}

/**
 *生成二维码
 */
function qrcode($data,$path,$imgname,$level=3,$size=4){
    Vendor('Phpqrcode.phpqrcode');
    $level = 'L';
    // 点的大小：1到10,用于手机端4就可以了
    $size = 10;
    if(!file_exists($path))
    {
        mkdir($path, 0700);
    }
    //  生成的文件名
    $fileName = $path.$imgname.'.png';
    ob_end_clean();//清空缓冲区
    $object = new \QRcode();
    $object->png($data,$fileName,$level, $size);
}

/**
 *省市区标题
 */
function provinceCityCounty($vo,$field=''){
    $vo['province'] = !empty($vo['province_id']) ? modelField($vo['province_id'],'china','title') : '';
    $vo['city']     = !empty($vo['city_id']) ? modelField($vo['city_id'],'china','title') : '';
    $vo['county']   = !empty($vo['county_id']) ? modelField($vo['county_id'],'china','title') :'';
    $vo['zone_title'] = $vo['province'].' '.$vo['city'].' '.$vo['county'];
    return !empty($field) ? $vo[$field] : $vo;
}




/**
 *用户头像
 */
function head_img_url($head_icon,$wxhead_img){


    if($head_icon>0){
        $head_img = get_image($head_icon);
    }else{
        if(!empty($wxhead_img)){
            $head_img = $wxhead_img;
        }else{
            $head_img = config('index_url').'/toux.png';
        }
    }
    return $head_img;
}



//支付方式
function payTypeTitle($pay_type){
    if($pay_type==1){
        return '支付宝';
    }else if($pay_type==2){
        return '微信';
    }else if($pay_type==3){
        return '余额';
    }else{
        return '--';
    }
}

/**
*审核状态
 */
function check_status($check_status='')
{
    if ($check_status == 0) {
        return ['check_status'=>0,'msg'=>'未认证','msgb'=>'待审核','color'=>'red'];
    } else if ($check_status == 1) {
        return ['check_status'=>1,'msg'=>'认证成功','msgb'=>'审核成功','color'=>'#008d4c'];
    } else if ($check_status == 2) {
        return ['check_status'=>2,'msg'=>'认证失败','msgb'=>'审核失败','color'=>'gray'];
    }
}

/**
*售后状态
 */
function buyafter_status($after_status=0){
    if($after_status==1 || $after_status==2 || $after_status==4){
        $after = ['after_status'=>1,'msg2'=>'售后中'];
    }else if($after_status==3){
        $after = ['after_status'=>2,'msg2'=>'拒绝退款'];
    }else if($after_status==5){
        $after = ['after_status'=>3,'msg2'=>'退款成功'];
    }else{
        $after = ['after_status'=>0,'msg2'=>''];
    }
    return $after;
}


//售后状态
function afterStatus($vo){
    if(empty($vo)){
        return ['status'=>0,'msg'=>'','msg2'=>''];
    }
    if($vo['status']==-2){
        return ['status'=>-2,'msg'=>'<font style="color:red;">已取消退款</font>','msg2'=>'已取消退款','color'=>'red'];
    }else{
        if($vo['chuli_status']==1){
            return ['status'=>1,'msg'=>'<font style="color:red;">退款审核中</font>','msg2'=>'退款审核中','color'=>'red'];
        }else if($vo['chuli_status']==2){
            if($vo['apply_type']==1){
                return ['status'=>2,'msg'=>'<font style="color: #8715ec;">待打款</font>','msg2'=>'同意退款','color'=>'#8715ec'];
            }else{
                return ['status'=>2,'msg'=>'<font style="color: #8715ec;">快递待寄出</font>','msg2'=>'快递待寄出','color'=>'#8715ec'];
            }
        }else if($vo['chuli_status']==3){
            return ['status'=>3,'msg'=>'<font style="color: #bf3cce;">拒绝退款</font>','msg2'=>'拒绝退款','color'=>'#bf3cce'];
        }else if($vo['chuli_status']==4){
            return ['status'=>4,'msg'=>'<font style="color: #00a65a;">快递已寄出</font>','msg2'=>'快递已寄出'];
        }else if($vo['chuli_status']==5){
            return ['status'=>5,'msg'=>'<font style="">已退款</font>','msg2'=>'已退款','color'=>''];
        }else{
            return ['status'=>0,'msg'=>'','msg2'=>''];
        }
    }
}


/**
 *本周日期
 */
function this_week_time($i=1){
    $time = time();
    //组合数据
    $date = date('Y-m-d' ,strtotime( '+' . $i-7 .' days', $time));
    return $date;
}
/**
 *指定年月的开始和结束时间戳
 */
function year_month_time($y=0,$m=0){
    $y = $y ? $y : date('Y');
    $m = $m ? $m : date('m');
    $d = date('t', strtotime($y.'-'.$m));
    return array("begin"=>strtotime($y.'-'.$m),"end"=>mktime(23,59,59,$m,$d,$y));
}




/**
*递归查询子集
 */
function getSubs($categorys, $catId = 0, $level = 1)
{
    $subs = array();
    foreach ($categorys as $item) {
        if ($item['pid'] == $catId) {
            $item['level'] = $level;
            $subs[] = $item;
            $subs   = array_merge($subs, $this->getSubs($categorys, $item['id'], $level + 1));
        }
    }
    return $subs;
}


/**
 * 商品订单状态
 */
function orderStatus($order){
    $out_time = config('order_pay_out_time');

    $cha_time = time()-$out_time;
    if($order['user_status']==2){
        return ['status'=>-1,'msg'=>'已取消','msg2'=>'交易关闭'];
    }else{
        if($order['pay_status']==1){
            if($order['order_status']==0){
                return ['status'=>2,'msg'=>'<font style="color:red;">待发货</font>','msg2'=>'待发货'];
            }else if($order['order_status']==1){
                return ['status'=>3,'msg'=>'<font style="color:#00c0ef;">待收货</font>','msg2'=>'待收货'];
            }else if($order['order_status']==2){
                return ['status'=>4,'msg'=>'<font style="color:#00a65a;">待评价</font>','msg2'=>'待评价'];
            }else if($order['order_status']==3){
                return ['status'=>5,'msg'=>'<font style="color: #3c8dbc">已完成</font>','msg2'=>'交易成功'];
            }else if($order['order_status']==4){
                return ['status'=>6,'msg'=>'<font>已退款</font>','msg2'=>'已退款'];
            }
        }else{
            if($order['create_time']<=$cha_time){
                return ['status'=>-1,'msg'=>'<font style="color:red;">交易关闭</font>','msg2'=>'交易关闭'];
            }else{
                return  ['status'=>1,'msg'=>'待支付','msg2'=>'待支付'];
            }
        }
    }
}

/**
 *审核状态
 */
function checkStatus($check_status,$field){

    if($check_status==0){
        $msg = ['status'=>1,'msg'=>'<font style="color:red;">未审核</font>'];
    }else if($check_status==1){
        $msg = ['status'=>2,'msg'=>'<font style="color: #00a65a;">审核成功</font>'];
    }else if($check_status==2){
        $msg = ['status'=>3,'msg'=>'<font style="color: #bf3cce;">审核失败</font>'];
    }
    return $msg[$field];
}

/**
 *保留两位小数
 */
function two_xiaoshu($number){
    $number = floatval($number);
    return sprintf("%.2f",$number);
}

/**
 *手机号星星代替
 **/
function mobile_four_star($mobile){
    if(empty($mobile)){
        return '';
    }
    return substr_replace($mobile, '****',3, 4);
}

/**
*评论平均分数
 */
function comment_avg($sum_star=0,$counts=0){
    $score = 0;
    if(intval($sum_star)>0 && intval($counts)>0){
        $score = round(($sum_star/$counts),1);
    }
    return $score;
}

/**
*日期
 */
function return_dates($get){
    $dates = '';
    if(!empty($get['dates'])) {
        $timearr = explode('/', $get['dates']);
        $dates = $timearr[0].' / '.$timearr[1];
    }
    return $dates;
}
/**
*余额流水
 */
function flowater($arr){
    $flow['uid'] = $arr['uid'];
    $flow['pt_type'] = $arr['pt_type'];
    $flow['type'] = $arr['type'];
    $flow['zf_type'] = $arr['zf_type'];
    $flow['pay_price'] = $arr['pay_price'];
    $flow['balance'] = $arr['balance'];
    $flow['remark'] = $arr['remark'];
    $flow['return_id'] = $arr['return_id'];
    $flow['return_uid'] = !empty($arr['return_uid']) ? $arr['return_uid'] : 0;
    $flow['create_time'] = time();
    return $flow;
}

function spec_type_list($type){
    if($type==1){
        return '影响价格';
    }else if($type==2){
        return '常规规格';
    }else if($type==3){
        return '子商品规格';
    }
}
//商品规格信息
function guige_info($ve){
    $maps['product_id'] = array('eq',$ve['product_id']);
    $maps['id']         = array('in',$ve['spec_id']);
    $gg_info = db('product_guige')->where($maps)->field('id,gg_title')->select();
    return $gg_info;
}

//商品价格
function product_price($ve,$userinfo){
    if($userinfo['user_type']==2){
        $new_price = $ve['bigdl_price']; //大客户商品总价
    }else if($userinfo['user_type']==3){
        $new_price = $ve['dls_price']; //代理商商品总价
    }else{
        $new_price = $ve['new_price']; //普通用户单个商品总价
    }
    return $new_price;
}
