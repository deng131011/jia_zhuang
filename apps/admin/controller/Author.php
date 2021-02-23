<?php

namespace app\admin\controller;
use think\Db;
class Author extends Admin{
    

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Author');
    }

    /**
     *资料库作者列表
     */
    public function index() {
        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        $map['u.status'] = array('gt',-1);
        if(!empty($get['keywords'])){
            $map['u.names'] = array('like','%'.trim($get['keywords']).'%');
        }
        $data_list =  Db::name('music_author')->alias('u')->where($map)->order('u.id desc')->field('u.*')->paginate(20);

        $this->assign('list',$data_list);
        $this->assign('meta_title','作者列表');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        return $this->fetch();
    }






    /**
     * 编辑
     */
    public function edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        $model = $this->model;
        if (IS_POST) {
            $params = input('param.');
            $res = $model->checkform($params);
            if($res['code']==201){
                $this->error($res['msg']);
            }

            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->model->editData($res['data'])) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($model->getError());
            }

        } else {

            $info = ['type'=>1,'target'=>'_blank','rating'=>0,'sort'=>99];
            if ($id>0) {
                $info = $this->model->find($id);
            }

            $return = builder('Form')
                ->addFormItem('id', 'hidden', 'ID', 'ID')
                ->addFormItem('names', 'text', '作者姓名', '必须填写')
                ->addFormItem('bank_name', 'text', '银行名称', '')
                ->addFormItem('bank_number', 'text', '银行账号')
                ->addFormItem('alipay_code', 'text', '支付宝账号')
                ->addFormItem('sort', 'number', '排序', '数值越小越靠前')
                ->setFormData($info)
                ->addButton('submit')->addButton('back')    // 设置表单按钮
                ->fetch();

            return Iframe()
                ->setMetaTitle($title.'作者')  // 设置页面标题
                ->content($return);

        }
    }


}