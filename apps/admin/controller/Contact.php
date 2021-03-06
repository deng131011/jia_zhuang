<?php

namespace app\admin\controller;
use think\Db;
use think\Request;
use app\app\model\Product as appProductModel;
/**
 * 客服热线
 */
class Contact extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Contact');

        $appProductModel = new appProductModel();
        $category = $appProductModel->type_list(['type'=>3]);
        $this->assign('category',$category['data']);
    }

    /**
     * 客服热线
     */
    public function kefu() {
        $get = input('param.');

        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['type1']     = !empty($get['type1'])? $get['type1'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['b.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        if(!empty($get['type1'])){
            $where['b.type1'] = array('eq',$get['type1']);
        }
        $where['b.status'] = array('gt',-1);
        $data_list =  db('contact b')
            ->where($where)
            ->order('b.id desc')
            ->paginate(20)->each(function($item,$key){
                return $this->foreach_list($item);
            });;
        $this->assign('list',$data_list);
        $this->assign('meta_title','客服热线');
        return $this->fetch();
    }

    public function foreach_list($item){
        $item['category_title'] = modelField($item['type1'],'category','title');
        return $item;
    }

    /**
     * 编辑
     */
    public function edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            $post = input('param.');
            //验证数据
            $checkres = $this->model->checkform($post);
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->model->editData($checkres['data'])) {
                $this->success($title.'成功', url('kefu'));
            } else {
                $this->error($this->model->getError());
            }

        } else {

            $info = Db::table('qyc_contact')->where('id',$id)->find();
            $this->assign('info',$info);
            $this->assign('meta_title',$title);//标题
            return $this->fetch();
        }
    }









}