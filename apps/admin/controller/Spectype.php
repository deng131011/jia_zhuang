<?php

namespace app\admin\controller;
use app\common\layout\Iframe;

class Spectype extends Admin{
    

    function _initialize()
    {
        parent::_initialize();
        $this->Spectype = model('Spectype'); //广告位模型
    }


    public function index(){
        $get = $_GET;

        $map = array();
        if(!empty($get['keyword'])){
            $map['title'] = array('like','%'.trim($get['keyword']).'%');
        }
        $map['status'] = array('gt',-1);

        // 获取所有链接
        list($data_list,$total) = $this->Spectype->getListByPage($map,true,'sort,create_time desc');
        foreach ($data_list as $ke=>$ve){
            $data_list[$ke]['type'] = spec_type_list($ve["type"]);
            $data_list[$ke]['have_picture1'] = $ve['have_picture']==1?'有':'无';
        }


        $content = builder('List')
            ->addTopButton('addnew')    // 添加新增按钮
            ->addTopButton('resume',['model'=>'Spectype'])  // 添加启用按钮
            ->addTopButton('forbid',['model'=>'Spectype'])  // 添加禁用按钮
            ->setSearch('请输入关键字')
            ->keyListItem('title', '名称')
            ->keyListItem('type', '规格类别')
            ->keyListItem('have_picture1', '有无图片')
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
            ->setMetaTitle('商品规格分类')  // 设置页面标题
            ->search([
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
            $checkres = $this->Spectype->checkform($post);

            //wangkun start 检测是否已有规格影响价格
            if($post['type']==1){
                $where_t['type']=array('eq',1);
                if(!empty($post['id'])){
                    $where_t['id']=array('neq',$post['id']);
                }
                $have_sp=db('spec_type')->where($where_t)->find();
                if($have_sp){
                    $this->error('已有规格影响价格，请先将其设置为常规规格');
                }
            }
            if($post['have_picture']==1){
                $where_h['have_picture']=array('eq',1);
                if(!empty($post['id'])){
                    $where_h['id']=array('neq',$post['id']);
                }
                $have_tp=db('spec_type')->where($where_h)->find();
                if($have_tp){
                    $this->error('已有规格有图片，请先将其设置为无图片');
                }
            }
            //end
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            if(intval($post['id'])>0){
                $post['update_time'] = date('Y-m-d H:i:s');
            }else{
                $post['create_time'] = date('Y-m-d H:i:s');
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->Spectype->editData($post)) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->Spectype->getError());
            }

        } else {
            $info =$this->Spectype->where('id',$id)->find();
            $this->assign('meta_title',$title);
            $this->assign('info',$info);
            return $this->fetch();

        }
    }


}