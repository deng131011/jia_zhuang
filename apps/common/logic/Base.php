<?php
// 系统逻辑模型基类
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
namespace app\common\logic;
use think\Model;
use eacoo\EacooAccredit;
use eacoo\Tree;

class Base extends Model {
    protected $url;
    protected $request;
    protected $module;
    protected $controller;
    protected $action;

    protected function initialize() {
        parent::initialize();
        $this->request = request();
        defined('ACCREDIT_TOKEN') or define('ACCREDIT_TOKEN',EacooAccredit::getAccreditToken());//获取本地授权token
        //获取request信息
        $this->requestInfo();
        //halt(config());
    }

    /**
     * request信息
     * @return [type] [description]
     */
    protected function requestInfo() {
        
        defined('MODULE_NAME') or define('MODULE_NAME', $this->request->module());
        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME') or define('ACTION_NAME', $this->request->action());
        defined('IS_POST') or define('IS_POST', $this->request->isPost());
        defined('IS_AJAX') or define('IS_AJAX', $this->request->isAjax());
        defined('IS_PJAX') or define('IS_PJAX', $this->request->isPjax());
        defined('IS_GET') or define('IS_GET', $this->request->isGet());

        //$this->param = $this->request->param();
        $this->urlRule = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
        $this->ip = $this->request->ip();
        $this->url = $this->request->url(true);//完整url
    }

    //移动位置
    public function moveMenuHtmlCom($model){

        //移动菜单位置
        $menus = db($model)->select();
        $tree_obj = new Tree;
        $menus = $tree_obj->toFormatTree($menus,'title');
        $menu_options = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
        $menu_options_str='';
        foreach ($menu_options as $key => $option) {
            if(is_array($option)){
                $menu_options_str.='<option value="'.$option['id'].'">'.$option['title_show'].'</option>';
            }else{
                $menu_options_str.='<option value="'.$option['id'].'">'.$option.'</option>';
            }
        }
        $move_position_url = url('moveMenusPosition');
        return <<<EOF
        <div class="modal fade mt100" id="movemenuPositionModal">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                        <p class="modal-title">移动至</p>
                    </div>
                    <div class="modal-body">
                        <form action="{$move_position_url}" method="post" class="form-movemenu">
                            <div class="form-group">
                                <select name="to_pid" class="form-control">{$menu_options_str}</select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="ids">
                                <button class="btn btn-primary btn-block submit ajax-post" type="submit" target-form="form-movemenu">确 定</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            function move_menuposition(){
                var ids = '';
                $('input[name="ids[]"]:checked').each(function(){
                   ids += ',' + $(this).val();
                });
                if(ids != ''){
                    ids = ids.substr(1);
                    $('input[name="ids"]').val(ids);
                    $('.modal-title').html('移动选中的菜单至：');
                    $('#movemenuPositionModal').modal('show', 'fit')
                }else{
                    updateAlert('请选择需要移动的菜单', 'warning');
                }
            }
        </script>
EOF;
    }

    /**
     * 移动菜单位置
     * @author zhoujianbo
     */
    public function moveMenusPositionCom($ids,$to_pid,$model) {
        $currentUser = session('admin_login_auth');
        cache('admin_sidebar_menus_'.$currentUser['uid'],null);
        $map['id'] = ['in',$ids];
        $data = array('pid' => $to_pid);
        $result = model($model)->editRow($data, $map);
        return $result;
    }


    //选择秒杀活动
    public function add_spike_product($model){

        return <<<EOF
        <script type="text/javascript">
            function add_spike_product(){
                var ids = '';
                $('input[name="ids[]"]:checked').each(function(){
                   ids += ',' + $(this).val();
                });
                if(ids != ''){
                    ids = ids.substr(1);
                    window.location.href="/Admin/Spike/addSpike?ids="+ids;
                }else{
                    updateAlert('请选择需要秒杀的商品', 'warning');
                }
            }
        </script>
EOF;
    }

}