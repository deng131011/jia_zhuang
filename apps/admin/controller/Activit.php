<?php

namespace app\admin\controller;
use think\Db;
use app\app\model\Course as AppCourseModel;
use app\app\model\Hotel as appHotelModel;
class Activit extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Activit');

    }

    /**
     *活动列表
     */
    public function index() {

        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['b.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status'] = array('gt',-1);
        $data_list =  Db::name('activit')->alias('b')->where($where)->order('b.id desc')->paginate();
        $this->assign('list',$data_list);
        $this->assign('meta_title','活动列表');

        return $this->fetch();
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
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->model->getError());
            }

        } else {
            $info =$this->model->where('id',$id)->find();
            $this->assign('info',$info);
            $field = ['name'=>'icon','description'=>'','options'=>['path_type'=>'picture'],'value'=>$info['icon']];
            $this->assign('field',$field);
            $this->assign('meta_title',$title);

            return $this->fetch();
        }
    }




}