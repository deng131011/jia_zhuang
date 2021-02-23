<?php

namespace app\admin\controller;


class Ad extends Admin{
    

    function _initialize()
    {
        parent::_initialize();
        $this->Ad = model('Ad'); //广告位模型
        $this->linkType  = [
            1 => '无需链接',
            2 => '商品链接',
            3 => '商家链接',
            4 => '文章链接',
        ];
        $this->assign('linktype',$this->linkType);
        $this->position = $this->Ad->ad_position(); //广告位

    }


public function index(){
    $get = $_GET;

    $map = array();
    if(!empty($get['keyword'])){
        $map['title'] = array('like','%'.trim($get['keyword']).'%');
    }
    if(!empty($get['position_id'])){
        $map['position_id'] = array('eq',$get['position_id']);
    }
    $map['status'] = array('gt',-1);

    // 获取所有链接
    list($data_list,$total) = $this->Ad->getListByPage($map,true,'create_time desc');
    foreach ($data_list as $ke=>$ve){
        $data_list[$ke]['position'] =modelField($ve['position_id'],'ad_positon','title');
    }


    $content = builder('List')
        ->addTopButton('addnew')    // 添加新增按钮
        ->addTopButton('resume',['model'=>'Ad'])  // 添加启用按钮
        ->addTopButton('forbid',['model'=>'Ad'])  // 添加禁用按钮
        ->setSearch('请输入关键字')
        ->keyListItem('title', '广告名称')
        ->keyListItem('position', '广告位置')
        ->keyListItem('icon', '图像', 'picture',['style'=>'width:100px;height:50px;'])
        ->keyListItem('sort', '排序')
        ->keyListItem('create_time', '添加时间')
        ->keyListItem('status', '状态', 'status')
        ->keyListItem('right_button', '操作', 'btn')
        ->setListData($data_list)     // 数据列表
        ->setListPage($total)  // 数据列表分页
        ->addRightButton('edit')     // 添加编辑按钮
        ->addRightButton('delete')  // 添加删除按钮
        ->addRightButton('forbid') // 添加禁用按钮
        ->fetch();

    return Iframe()
        ->setMetaTitle('广告位置')  // 设置页面标题
        ->search([
            ['name'=>'position_id','type'=>'select3','multilayer_select'=>'广告位','options'=>$this->position],
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
            //p($post);
            //验证数据
            $checkres = $this->Ad->checkform($post);
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            if(intval($post['id'])>0){
                $post['update_time'] = date('Y-m-d H:i:s');
            }else{
                $post['create_time'] = date('Y-m-d H:i:s');
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->Ad->editData($post)) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->Ad->getError());
            }

        } else {
            $info =db('ad')->where('id',$id)->find();
            $this->assign('meta_title',$title);
            $this->assign('info',$info);
            $this->assign('position',$this->position);
            return $this->fetch();

        }
    }





}