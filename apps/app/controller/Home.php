<?php

namespace app\app\controller;
use app\common\controller\Base;
use think\Loader;
use think\Request;
class Home extends Base {

    function _initialize() {
        parent::_initialize();
        // 系统开关
        if (!config('toggle_web_site')) {
           $this->error('站点已经关闭，请稍后访问~');
        }
        $list = file_get_contents('php://input');
        $post = json_decode($list, true);

        $this->post = $post;
        //验证Token
        if(!empty($post['token']) && !empty($post['uid'])){
            $where['id']    = array('eq',$post['uid']);
            $user = db('usermember')->where($where)->field('*,id as uid')->find();
            if(empty($user)){
                $this->appReturn(array('code'=>501,'msg'=>'您的账号不存在或已被平台注销'));exit;
            }
            if($user['token']!=$post['token']){
                $this->appReturn(array('code'=>501,'msg'=>'您的账号在其他地方登陆'));exit;
            }
            if($user['status']!=1){
                $this->appReturn(array('code'=>201,'msg'=>'该账号已被禁用'));exit;
            }
            $this->userinfo = $user; //用户信息
        }else{
            $this->userinfo = []; //用户信息
        }
       
    }

    /**
     * app返回参数
     */
    protected function appReturn($value) {
        header("Content-Type:application/json; charset=utf-8");
        exit(json_encode($value));
    }


    /**
     * 验证数据
     * @param  string $validate 验证器名或者验证规则数组
     * @param  array  $data          [description]
     * @return [type]                [description]
     */
    public function validateData($data,$validate)
    {
        if (!$validate || empty($data)) return false;
        $result = $this->validate($data,$validate);
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);exit;
        } 
        return true;
        
    }

    /**
     * 页面配置信息
     * @param  string $title  标题
     * @param  string $main_mark [description]
     * @param  string $mark   [description]
     * @return [type]         [description]
     */
    public function pageInfo($title='',$mark='',$extend=[])
    {
        $page_config = [
            'title'  => $title,
            'mark'   => $mark
        ];
        if(!empty($extend) && is_array($extend)) $page_config = array_merge($page_config,$extend);

        //添加面包屑导航数据
        $page_config['breadcrumbs'] = $this->breadCrumbs($page_config);
        $this->assign('page_config',$page_config);
    }


    /**
     * 模版输出
     * @param  string $templateFile 模板文件名
     * @param  array  $vars         模板输出变量
     * @param  array  $replace      模板替换
     * @param  array  $config       模板参数
     * @param  array  $render       是否渲染内容
     * @return [type]               [description]
     */
    public function fetch($template='', $vars = [], $replace = [], $config = [] ,$render=false) {

        $ACTION_NAME = Request::instance()->action(true);   //处理驼峰写法模板兼容，一定要给true，不然方法名会自动变小写；

        if (!is_file($template)) {
            
            if (!$template) {
                $template_name = $this->request->controller().'/'.self::toUnderScore($this->request->action());
            } else{
                $template_name = $this->request->controller().'/'.$template;
            }

            // 当前模版文件
            $template = config('template.view_path').strtolower($template_name).'.'.config('template.view_suffix'); //当前主题模版是否存在
            if (!is_file($template)) {
                $template = APP_PATH.MODULE_NAME. '/view/'. strtolower($template_name) . '.' .config('template.view_suffix');
                if (!is_file($template)) {
                    throw new \Exception('模板不存在：'.$template, 5001);
                }
            }
            
        }

        return $this->view->fetch($template, $vars, $replace, $config, $render);
    }


    /*
     *  驼峰写法 转换大写为下划线加小写
     *  @param  string $str 需要转换的字符串
     *  @time: 2018-10-12
     *  @author: yyyvy <76836785@qq.com>
     * */
    public static function toUnderScore($str){
        $str = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $str);
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $str), "_"));
    }
}
