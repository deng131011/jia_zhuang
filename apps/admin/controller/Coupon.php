<?php

namespace app\admin\controller;
use app\app\model\Course as addCourseModel;
use think\Db;

class Coupon extends Admin {


    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Coupon');
		$this->assign('type',$this->model->type(1)); //优惠券类型
		$this->assign('get_type',$this->model->coupon_gettype()); //优惠券获取途径
        $this->position_type = ['2'=>'关闭','1'=>'开启'];

        $area_type = [
            '1'=>'商城优惠券',
            '2'=>'课程优惠券',
            '3'=>'直播优惠券',
            '4'=>'题库优惠券',
        ];
        $this->assign('area_type',$area_type);
        $this->area_type = $area_type;
    }

    /**
     * 后台菜单管理
     * @return [type] [description]
     */
    public function index(){
        $get = input('param.');
        $get['area_type'] = !empty($get['area_type']) ? $get['area_type'] : '';
        $get['type'] = !empty($get['type']) ? $get['type'] : '';
        $get['keywords'] = !empty($get['keywords']) ? $get['keywords'] : '';
        $this->assign('get',$get);
        $map = array();
        if(!empty($get['keywords'])){
            $map['title'] = array('like','%'.trim($get['keywords']).'%');
        }
        if(!empty($get['type'])){
            $map['type'] = array('eq',$get['type']);
        }
        if(!empty($get['area_type'])){
            $map['area_type'] = array('eq',$get['area_type']);
        }
        $map['status'] = array('gt',-1);
        $data_list =  Db::name('coupon')->alias('b')->order('b.id desc')->where($map)->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });;

        $this->assign('list',$data_list);
        $this->assign('meta_title','优惠券列表');
        return $this->fetch();




    }

   public function foreach_list($item){



       return $item;
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

        } else{
            // 获取表单数据

            $info =$this->model->where('id',$id)->find();

            $this->assign('info',$info);
            $this->assign('meta_title',$title);
            return $this->fetch();
        }

    }






}