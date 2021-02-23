<?php

namespace app\admin\controller;


class Adpositon extends Admin{
    

    function _initialize()
    {
        parent::_initialize();
        $this->AdPositon = model('AdPositon'); //广告位模型
        $this->type=['1'=>'单图','2'=>'多图'];
    }


public function index(){
    $get = $_GET;

    $map = array();
    if(!empty($get['keyword'])){
        $map['title'] = array('like','%'.trim($get['keyword']).'%');
    }
    if(!empty($get['type'])){
        $map['type'] = array('eq',$get['type']);
    }

    // 获取所有链接
    list($data_list,$total) = $this->AdPositon->getListByPage($map,true,'sort,create_time desc');

    $content = builder('List')
        ->addTopButton('addnew')    // 添加新增按钮
        ->addTopButton('resume',['model'=>'AdPositon'])  // 添加启用按钮
        ->addTopButton('forbid',['model'=>'AdPositon'])  // 添加禁用按钮
        ->setSearch('请输入关键字')
        ->keyListItem('id', 'ID')
        ->keyListItem('title', '广告位')
        ->keyListItem('size', '尺寸')
        ->keyListItem('type', '类型','array',$this->type)
        ->keyListItem('sort', '排序')
        ->keyListItem('create_time', '添加时间')
        ->keyListItem('status', '状态', 'status')
        ->keyListItem('right_button', '操作', 'btn')
        ->setListData($data_list)     // 数据列表
        ->setListPage($total)  // 数据列表分页
        ->addRightButton('edit')     // 添加编辑按钮
        ->addRightButton('delete')  // 添加删除按钮
        ->fetch();

    return Iframe()
        ->setMetaTitle('广告位置')  // 设置页面标题
        ->search([
            ['name'=>'type','type'=>'select','title'=>'类型','options'=>$this->type],
            ['name'=>'keyword','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],

        ])

        ->content($content);
}


    /**
     * 编辑
     */
    public function edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        $info = array();
        if (IS_POST) {
            $post = input('param.');
            //验证数据
            $checkres = $this->AdPositon->checkform($post);
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            if($post['id']>0){
                $post['update_time'] = date('Y-m-d H:i:s');
            }else{
                $post['create_time'] = date('Y-m-d H:i:s');
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->AdPositon->editData($post)) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->AdPositon->getError());
            }

        } else {

            $info =db('ad_positon')->where('id',$id)->find();

            $return = builder('Form')
                ->addFormItem('id', 'hidden', 'ID', 'ID')
                ->addFormItem('title', 'text', '广告位名称', '必须填写','')
                ->addFormItem('size', 'text', '广告图尺寸', '广告位的图片尺寸，例：2000*1250')
                ->addFormItem('sort', 'number', '排序', '按照数值大小的升序进行排序，数值越小越靠前')
                ->addFormItem('type', 'radio', '类型','',['1'=>'单图','2'=>'多图'])
                ->setFormData($info)
                ->addButton('submit')->addButton('back')    // 设置表单按钮
                ->fetch();

            return Iframe()
                ->setMetaTitle($title.'广告位')  // 设置页面标题
                ->content($return);
        }
    }





}