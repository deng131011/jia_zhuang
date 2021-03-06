<?php
// 授权管理控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;

class Category extends Admin {


    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Category');
        $this->position_type = ['2'=>'关闭','1'=>'开启'];
    }

    /**
     * 后台菜单管理
     * @return [type] [description]
     */
    public function index(){
        $depend_flag = input('param.depend_flag','all');//管理类型
        //移动上级按钮属性
        $move_position_attr = [
            'title'   => '移动位置',
            'icon'    => 'fa fa-exchange',
            'class'   => 'btn btn-info btn-sm',
            'onclick' => 'move_menuposition()'
        ];

        $map['status'] = array('gt',-1);
        $menus = logic('Typetree')->getAdminMenu('category',$map);


        $this->assign('menus',$menus);
        return $this->fetch();

        //$extra_html = logic('Base')->moveMenuHtmlCom('category');//添加移动按钮html

    }


    /**
     * 菜单编辑
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function edit($id=0){
        $title = $id ? "编辑":"新增";
        
        if(IS_POST){
            // 提交数据
            $data = $this->request->param();

            //验证数据
            $checkres = $this->model->checkForm($data,'AuthRule.edit');

            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            if($data['pid']==$data['id']){
                $this->error('上级菜单不能选择本级');
            }

            $data['level'] = $this->model->level($data['pid']);

            if($data['level'] > 3){
                $this->error('商品类别最多三级');
            }

            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->model->editData($data)) {
                $this->success($title.'菜单成功', url('index',['pid'=>input('param.pid')]));
            } else {
                $this->error($this->Producttype->getError());
            }   

        } else{

            // 获取表单数据
            $info = array();
            $info =db('category')->where('id',$id)->find();

            //获取上级菜单
            $map['status'] = array('gt',-1);
          //  $map['fl_type']       = array('neq',1);
            $menus = logic('Typetree')->getAdminMenu('category',$map);
            $menus = !empty($menus) ? $menus : array();

            $menus = array_merge([0=>['id'=>0,'title_show'=>'顶级菜单']], $menus);

            $typelist = $menus;
            $this->assign('typelist',$typelist);
            $this->assign('meta_title',$title);

            $field = ['name'=>'icon','description'=>'','options'=>['path_type'=>'picture'],'value'=>$info['icon']];
            $this->assign('info',$info);
            $this->assign('field',$field);
            return $this->fetch();

            $tab_list = [
                ['title'=>'基本信息','href'=>''],
            ];

            $content = builder('form')
                    ->SetTabNav($tab_list,0)  // 设置Tab按钮列表
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('pid', 'multilayer_select', '上级菜单', '上级菜单',$menus)
                    ->addFormItem('title', 'text', '分类标题', '必须填写')
                    ->addFormItem('sort', 'number', '排序', '按照数值大小的升序进行排序，数值越小越靠前')

                    ->addFormItem('status', 'radio', '状态', '',[1=>'启用',0=>'禁用'])
                    ->addFormItem('icon', 'picture', '分类图标', '')
                    ->addFormItem('content', 'ueditor', '分类内容', '选填')
                    ->setFormData($info)
                    ->addButton('submit')->addButton('back')    // 设置表单按钮
                    ->fetch();

            return Iframe()
                    ->setMetaTitle($title.'商品分类')  // 设置页面标题
                    ->content($content);
        }   
        
    }


    /**
     * 对菜单进行排序
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function Sort($ids = null)
    {
        $builder = builder('Sort');
        $depend_flag = input('param.depend_flag','all');//是否存在父ID
        $map     = [];
        if ($depend_flag!='all' && $depend_flag) {
            $map['depend_flag']=$depend_flag;
        }
        
        if (IS_POST) {
            cache('admin_sidebar_menus_'.$this->currentUser['uid'],null);//清空后台菜单缓存
            $builder->doSort('category', $ids);
        } else {
            $map['status'] = 1;
            $list = $this->model->getList($map,'id,title,sort,pid','sort asc,id asc');
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }

            $content = $builder
                    ->setListData($list)
                    ->addButton('submit')->addButton('back')
                    ->fetch();

            return Iframe()
                    ->setMetaTitle('菜单排序')  // 设置页面标题
                    ->content($content);
        }
    }

    /**
     * 标记菜单
     * @return [type] [description]
     * @date   2017-08-27
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function markerMenu(){
        //是否标记菜单：0否，1是
        $model='AuthRule';
        if (IS_POST) {
            cache('admin_sidebar_menus_'.$this->currentUser['uid'],null);//清空后台菜单缓存

            $ids    = input('post.ids/a');
            $status = input('param.status');
            if (empty($ids)) {
                $this->error('请选择要操作的数据');
            }

            $map['id'] = ['in',$ids];
            switch ($status) {
                case 0 :  
                    $data = ['is_menu' => 0];
                    $this->editRow(
                        $model,
                        $data,
                        $map,
                        array('success'=>'标记成功','error'=>'标记失败')
                    );
                    break;
                case 1 :  
                    $data = ['is_menu' => 1];
                    $this->editRow(
                        $model,
                        $data,
                        $map,
                        ['success'=>'标记成功','error'=>'标记失败']
                    );
                    break;
                default :
                    $this->error('参数错误');
                    break;
            }
        }
    }
    /**
     * 移动菜单位置
     * @author zhoujianbo
     */
    public function moveMenusPosition() {
        if (IS_POST) {
            $ids    = input('param.ids');
            $to_pid = input('param.to_pid');
            if ($to_pid || $to_pid==0) {
                $result = logic('Base')->moveMenusPositionCom($ids,$to_pid,'Category');
                if ($result) {
                    $this->success('移动成功',url('index'));
                } else{
                    $this->error('移动成功',url('index'));
                }
            } else{
                $this->error('请选择目标菜单'.$to_pid);
            }
            
        }
    }

    /**
     * 收藏菜单
     * @param  integer $id 菜单ID
     * @return [type] [description]
     * @date   2018-02-15
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function toggleCollect()
    {
        try {
            if (!IS_GET) {
                throw new \Exception("非法请求", 0);
                
            }
            $param = $this->request->param();
            $collect_menus = config('admin_collect_menus');
            if (isset($collect_menus[$param['url']])) {
                unset($collect_menus[$param['url']]);
                $return = ['code'=>2,'msg'=>'取消收藏','data'=>[]];
            } else{
                $collect_menus[$param['url']] = ['title'=>$param['title']];
                $return = ['code'=>1,'msg'=>'收藏成功','data'=>[]];
            }
            model('Config')->where('name','admin_collect_menus')->setField('value',json_encode($collect_menus));
            cache('DB_CONFIG_DATA',null);
            return json($return);
        } catch (\Exception $e) {
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
        
    }
}