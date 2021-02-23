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
use app\common\model\Links as LinksModel;

class Appip extends Admin{
    
    protected $linkModel;
    protected $linkType;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Appip');
        $this->appipType  = [
                    1 => '安卓',
                    2 => '苹果'
                ];
    }

    /**
     * 版本管理
     */
    public function index() {
        // 获取所有链接
        list($data_list,$total) = $this->model->getListByPage([],true,'id desc');

        foreach ($data_list as $ke=>$ve){
            //$data_list[$ke]['create_time'] = $ve->create_time;
        }


        $content = builder('List')
                ->addTopButton('addnew')    // 添加新增按钮
                ->setSearch('请输入关键字')
                ->keyListItem('version_name', '版本名称')
                ->keyListItem('vsersion_code', '版本号')
                ->keyListItem('file_size', '附件大小')
                ->keyListItem('platform', '对应终端', 'array',$this->appipType)
                ->keyListItem('right_button', '操作', 'btn')
                ->setListData($data_list)     // 数据列表
                ->setListPage($total)  // 数据列表分页
                ->addRightButton('edit')     // 添加编辑按钮
                ->addRightButton('delete')  // 添加删除按钮
                ->fetch();

        return Iframe()
                    ->setMetaTitle('版本管理')  // 设置页面标题
                    ->content($content);
    }

    /**
     * 编辑链接
     */
    public function edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            $params = input('param.');
            $params['create_time'] = time();
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->model->editData($params)) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->model->getError());
            }

        } else {
            $info = array();
            if ($id>0) {
                $info = db('appip')->find($id);
            }

            $return = builder('Form')
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->addFormItem('version_name', 'text', '版本名称', '例：1.8.1')
                    ->addFormItem('vsersion_code', 'text', '版本号', '请填写数字')
                    ->addFormItem('file_size', 'text', '附件大小', '例：2M')
                    ->addFormItem('platform', 'select', '所属终端', '',$this->appipType)
                    ->addFormItem('is_gengxin', 'radio', '强制更新', '',['1'=>'需要','0'=>'不需要'])
                    ->addFormItem('content', 'textarea', '描述', '')
                    ->addFormItem('file', 'file', '附件')
                    ->setFormData($info)
                    ->addButton('submit')->addButton('back')    // 设置表单按钮
                    ->fetch();

            return Iframe()
                    ->setMetaTitle($title.'链接')  // 设置页面标题
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