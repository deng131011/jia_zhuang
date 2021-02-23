<?php

namespace app\admin\controller;
use app\admin\model\Fastdi as FastdiModel;
class Fastdi extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Fastdi');
    }

    /**
     * 友情链接管理
     * @return [type] [description]
     */
    public function index() {

        list($data_list,$total) = $this->model->search('title,url')->getListByPage([],true,'create_time desc');

        $content = builder('List')
            ->addTopButton('addnew')    // 添加新增按钮
            ->addTopButton('resume')  // 添加启用按钮
            ->addTopButton('forbid')  // 添加禁用按钮
            ->setSearch('请输入关键字')
            ->keyListItem('title', '快递公司名称')
            ->keyListItem('code', '编码')
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
            ->setMetaTitle('快递公司')  // 设置页面标题
            ->content($content);
    }

    /**
     * 编辑链接
     */
    public function edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            // 提交数据
            $data = $this->request->param();
            //验证数据
            $checkres = $this->model->checkform($data);
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }

            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->model->editData($checkres['data'])) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->model->getError());
            }

        } else {
            $info = ['type'=>1,'target'=>'_blank','rating'=>0,'sort'=>99];
            if ($id>0) {
                $info = FastdiModel::get($id);
            }

            $return = builder('Form')
                ->addFormItem('id', 'hidden', 'ID', 'ID')
                ->addFormItem('title', 'text', '快递公司名称', '请输入名称')
                ->addFormItem('code', 'text', '快递公司编码', '必须填写')
                ->addFormItem('sort', 'number', '排序', '数值越小越靠前')
                ->setFormData($info)
                ->addButton('submit')->addButton('back')    // 设置表单按钮
                ->fetch();

            return Iframe()
                ->setMetaTitle($title.'快递')  // 设置页面标题
                ->content($return);
        }
    }


}