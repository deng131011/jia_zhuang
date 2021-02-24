<?php 
use think\Route;
use app\common\builder\Builder;
use app\common\layout\Iframe;
use app\admin\logic\Module as ModuleLogic;
use app\admin\logic\Plugin as PluginLogic;

/**
 * 获取对象
 */
function get_sington_object($object_name = '', $class = null)
{

    $request = \think\Request::instance();
    
    $request->__isset($object_name) ?: $request->bind($object_name, new $class());
    
    return $request->__get($object_name);

}

/**
 * 获取iframe布局实例
 * @param  string $type 类型（list|form）
 * @return [type] [description]
 */
function Iframe()
{
    $builder = new Iframe();
    return $builder;
}

/**
 * 获取构建器实例
 * @param  string $type 类型（list|form）
 * @return [type] [description]
 */
function builder($type='')
{
    $builder = Builder::run($type);
    return $builder;
}

/**
 * 获取逻辑层实例
 * @param  string $name [description]
 * @return [type] [description]
 */
function logic($name='')
{
    return model($name,'logic');
}

/**
 * 获取服务层实例
 * @param  string $name [description]
 * @return [type] [description]
 */
function service($name='')
{
    return model($name,'service');
}

/**
 * 检测是否安装某个模块
 * @param  string $name 模块标识
 * @return [type] [description]
 */
function check_install_module($name='')
{
    return ModuleLogic::checkInstall($name);
}

/**
 * 检测是否安装某个插件
 * @param  string $name 插件标识
 * @return [type] [description]
 */
function check_install_plugin($name='')
{
    return PluginLogic::checkInstall($name);
}

/**
 * 处理插件钩子
 * @param  [type] $hook 钩子
 * @param  array $params 参数
 * @param  boolean $is_return 是否返回（true:返回值，false:直接输入）
 * @return [type] [description]
 */
function hook($hook, $params = null, $is_return =false)
{
    if ($is_return==true) {
        return \think\Hook::listen($hook, $params);exit;
    }
    \think\Hook::listen($hook, $params);
}

/**
 * 返回某个插件类的类名
 * @param  [type] $name 插件标识
 * @return [type] [description]
 */
function get_plugin_class($name) {
    $class = "\\plugins\\" . $name . "\\Index";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_plugin_config($name)
{
    if ($name!='') {
        $class = get_plugin_class($name);
        if (class_exists($class)) {
            $plugin = new $class();
            return $plugin->getConfig();
        } else {
            return [];
        }
    }
    
}

if (!function_exists('plugin_url')) {
    /**
     * 获取插件地址
     * @param  [type] $url   格式三段式，如：插件标识/控制器名称/操作名
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    function plugin_url($url, $param=[])
    {
        $params = [];
        // 拆分URL
        $url  = explode('/', $url);

        if (!isset($url[1]) && !isset($url[2])) {
            $params['_plugin']     = input('param._plugin');
            $params['_controller'] = input('param._controller');
            $params['_action']     = $url[0];
        } elseif (!isset($url[2])) {
            $params['_plugin']     = input('param._plugin');
            $params['_controller'] = $url[0];
            $params['_action']     = $url[1];
        } else {
            $params['_plugin']     = $url[0];
            $params['_controller'] = $url[1];
            $params['_action']     = $url[2];
        }

        // 合并参数
        $params = array_merge($params, $param);

        return url("home/plugin/execute", $params);
        
    }
}

/**
 *  url地址转换
 * @param  [type] $url [description]
 * @param  array $param [description]
 * @param  string $type 类型。0完整url，1模块地址，2插件地址，3主题
 * @return [type] [description]
 */
function eacoo_url($url, $param=[],$type=1)
{
    if ($type==2) {//插件
        $url_params = [];
        $query      = parse_url($url);
        $url        = $query['path'];
        if (!empty($query['query'])) {
            parse_str($query['query'],$url_params);
            $url_params = array_merge($url_params, $param);
        }
        if (strtolower($url)!='admin/plugins/config') {
            return plugin_url($url,$url_params);
        } else{
            return url($url,$url_params);
        }
    } else{
        if($url=='' || !$url || strpos($url, 'http://')!==false || strpos($url, 'https://')!==false){
            return $url;
        } 
        return url($url,$param);
    }
}

/**
 * 行为日志记录
 * @param  integer $uid 用户ID
 * @param  array $data 数据
 * @param  string $remark 备注
 * @return [type] [description]
 */
function action_log($action_id = 0, $uid = 0, $data = [], $remark = '',$is_admin = 0)
{
    if ($uid >0 ) {
        $action_log_model = new ActionLogic;
        if (is_array($data)) {
            $data = json_encode($data);
        }
        // 保存日志
        return $res = $action_log_model->recordLog($action_id ,$uid,$data,$remark,$is_admin);
    }
}

/**
 * 设置日志记录
 * @param  string $content 日志内容
 * @param  string $level 内容类型：如：info,error,debug
 * @param  string $action_name 操作名
 * @param  string $scene_name 场景名，默认控制器名
 * @param  string $module_name 模块名
 */
function setAppLog($content='',$level='info', $action_name='', $scene_name='', $module_name='' )
{
    if (empty($content)) {
        return false;
    }
    if (is_array($content)) {
        $content = var_export($content,true);
    }

    if (!$action_name) {
        $action_name = defined('ACTION_NAME') ? ACTION_NAME : 'unknown';
    }

    if (!$scene_name) {
        $scene_name = defined('CONTROLLER_NAME') ? CONTROLLER_NAME : 'unknown';
    }

    if (!$module_name) {
        $module_name = defined('MODULE_NAME') ? MODULE_NAME : 'unknown';
    }

    $now = date('Y-m-d H:i:s');
    $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $method  = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';
    $uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

    $data = [
            'type'      => 'custom',
            'content'   => $content,
            'remote'    => $remote,
            'method'    => $method,
            'uri'       => $uri
        ];
    $log_content = "[".date('Y-m-d H:i:s')."] ".strtoupper($level).": ".json_encode($data)."\n";
    $file = RUNTIME_PATH."applog".DS.$module_name.DS.$scene_name.DS.$action_name.'_'.date('Ymd',time()).".log";
    $path = dirname($file);
    !is_dir($path) && mkdir($path, 0755, true);
    file_put_contents($file,$log_content,FILE_APPEND|LOCK_EX);
    return true;
}
//格式化数组
function p($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';exit;
}

//获取图集
function imgArr($imgarr) {
    if (!empty($imgarr)) {
        $imgarrse = array_filter(explode(',', $imgarr));
        foreach ($imgarrse as $ks => $vs) {
            $img[$ks]['imgurl'] = get_image($vs);
            $img[$ks]['img_id'] = $vs;
        }
        return $img;
    } else {
        return array();
    }
}


/*
 *获取图片方法
 * $arrstatus:1查询一条，2查询多条
 **/
function getAdImages($position_id,$icon=0,$arrstatus=2){
    if($icon>0){
        $where['icon'] = array('eq',$icon);
    }
    $where['position_id'] = array('eq',$position_id);
    $where['status'] = array('eq',1);
    if($arrstatus==1){
        $vo = db('ad')->where($where)->find();
        $vo['imgurl'] = get_image($vo['icon']);
        return $vo;
    }else{
        $list = db('ad')->where($where)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl'] = get_image($ve['icon']);
        }
        return $list;
    }
}


//返回指定字段
function modelField($id, $model, $field) {
    $vo = db($model)->find($id);
    return $vo[$field];
}

//日期格式
function mydate($time='',$type=''){

    $time = !empty($time) ? $time : time();
    if($type==1){
        return date('Y-m-d',$time);
    }if($type==2){
        return date('Y-m-d H:i',$time);
    }if($type==3){
        return date('m-d',$time);
    }else if($type==4){
        return date('Y',$time);
    }else if($type==5){
        return date('m月d日',$time);
    }else if($type==6){
        return date('Y年m月d日',$time);
    }else{
        return date('Y-m-d H:i:s',$time);
    }
}


//双倍加密
function mymd5($string){
    return md5(md5($string));
}


//订单号
function order_number($model){
    if($model=='order'){
        $rand = date('YmdHis').rand(1000,9999);
    }else if($model=='payup_order'){
        $rand = 'CZ'.rand(100000,999999).date('YmdHi');
    }else if($model=='withdrawal'){
        $rand = 'TX'.date('YmdHi').rand(10000,99999);
    }else if($model=='buyafter'){
        $rand = 'TK'.date('YmdHi').rand(1000,9999);
    }
    $where['order_number'] = array('eq',$rand);
    $vo = db($model)->where($where)->find();
    if(!empty($vo)){
        order_number($model);
    }else{
        return $rand;
    }
}


//核销码
function hexiao_code($model,$shop_id=0){
    $rand = rand(100000,999999);
    $where['hexiao_code'] = array('eq',$rand);
    $where['shop_id']     = array('eq',$shop_id);
    $vo = db($model)->where($where)->find();
    if(!empty($vo)){
        hexiao_code($model);
    }else{
        return $rand;
    }
}


//时间条件
function timeCondition($start_time, $end_time) {
    if ($start_time != '' && $end_time == '') {
        $where = array('egt', strtotime($start_time . ' 00:00:00'));
    }
    if ($start_time == '' && $end_time != '') {
        $where = array('elt', strtotime($end_time . ' 23:59:59'));
    }
    if ($start_time != '' && $end_time != '') {
        $where = array(array('egt', strtotime($start_time . ' 00:00:00')), array('elt', strtotime($end_time . ' 23:59:59')));
    }
    if ($start_time == '' && $end_time == '') {
        $where = 0;
    }
    return $where;
}

//本月开始和结束时间
function this_month_time(){
    $arr['begin']=mktime(0,0,0,date('m'),1,date('Y'));
    $arr['end']=mktime(23,59,59,date('m'),date('t'),date('Y'));
    return $arr;
}

//现金流水支付类型
function price_flowater_zftype($zftype){
    $msg = [];
    if($zftype==1){
        $msg = ['zf_type'=>1,'title'=>'购买商品','title2'=>'购买商品'];
    }else if($zftype==2){
        $msg = ['zf_type'=>2,'title'=>'余额退回','title2'=>'余额退回'];
    }else if($zftype==3){
        $msg = ['zf_type'=>3,'title'=>'退款退回','title2'=>'退款退回'];
    }else if($zftype==4){
        $msg = ['zf_type'=>4,'title'=>'余额充值','title2'=>'余额充值'];
    }else if($zftype==5){
        $msg = ['zf_type'=>5,'title'=>'提现','title2'=>'提现'];
    }
    return $msg;
}



//阿里云短信
function AliyunSendMsg($mobile,$smscode,$codes){
    Vendor('Sendmsg.api_demo.Sendsns');
    $sms = new Sendsns();
    $result = $sms->ddsd($mobile,$smscode,$codes);
    $res =  json_decode(json_encode( $result),true);

    return $res;
}
