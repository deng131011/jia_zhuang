<?php
// 链接控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
class Hotsearch extends Admin{
    
    protected $linkModel;
    protected $linkType;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Hotsearch');
    }

    /**
     * 友情链接管理
     * @return [type] [description]
     * @date   2018-02-05
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function index() {
        $get = input('param.');
        // 获取所有链接
        if(!empty($get['keywords'])){
            $map['hot_name'] = array('like','%'.trim($get['keywords']).'%');
        }
        $map['status'] = array('gt',-1);
        list($data_list,$total) =  $this->model->getListByPage($map,true,'sort,create_time desc');

        $content = builder('List')
                ->addTopButton('addnew')    // 添加新增按钮
                ->addTopButton('resume')  // 添加启用按钮
                ->addTopButton('forbid')  // 添加禁用按钮
                ->setSearch('请输入关键字')
                ->keyListItem('hot_name', '关键词')
                ->keyListItem('type', '所属分类','array',['1'=>'帖子热搜','2'=>'商家热搜','3'=>'备注关键词'])
                ->keyListItem('sort', '排序')
                ->keyListItem('status', '状态', 'status')
                ->keyListItem('right_button', '操作', 'btn')
                ->setListData($data_list)     // 数据列表
                ->setListPage($total)  // 数据列表分页
            ->addRightButton('forbid') // 添加禁用按钮
                ->addRightButton('edit')     // 添加编辑按钮
                ->addRightButton('delete')  // 添加删除按钮
                ->fetch();

        return Iframe()
                    ->setMetaTitle('热搜列表')  // 设置页面标题
            ->search([
                ['name'=>'keywords','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],
            ])
                    ->content($content);
    }

    /**
     * 编辑链接
     */
    public function edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            $params = input('param.');
            //验证数据

            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->model->editData($params)) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->linkModel->getError());
            }

        } else {
            $info = ['type'=>0,'target'=>'_blank','rating'=>0,'sort'=>0];
            if ($id>0) {
                $info = $this->model->find($id);
            }

            $return = builder('Form')
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('hot_name', 'text', '关键词', '必须填写')
                    ->addFormItem('type', 'select', '关键词','', ['1'=>'帖子热搜','2'=>'商家热搜','3'=>'备注关键词'])
                    ->addFormItem('sort', 'number', '排序', '数值越小越靠前')
                    ->setFormData($info)
                    ->addButton('submit')->addButton('back')    // 设置表单按钮
                    ->fetch();

            return Iframe()
                    ->setMetaTitle($title.'关键字')  // 设置页面标题
                    ->content($return);
        }
    }
    
    /**
     * 对链接进行排序
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function sort($ids = null)
    {
        $builder = builder('Sort');
        if (IS_POST) {
            return $builder->doSort('links', $ids);
        } else {
            $map['status'] = ['egt', 0];
            $list = $this->linkModel->getList($map,'id,title,sort', 'sort asc');
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $content = $builder
                    ->setListData($list)
                    ->addButton('submit')->addButton('back')
                    ->fetch();

            return Iframe()
                    ->setMetaTitle('配置排序')
                    ->content($content);
        }
    }

}