<?php

namespace app\admin\controller;
use app\common\model\Links as LinksModel;
use think\Db;

class Article extends Admin{
    
    protected $linkModel;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Article');

        //文章分类
        $map['status'] = array('eq',1);

        $article_type = logic('Typetree')->getAdminMenu('category',$map);

        $this->assign('article_type',$article_type);
    }

    /**
     *
     */
    public function index() {

        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['typeid']        = !empty($get['typeid'])? $get['typeid'] : '';
        $get['type']        = !empty($get['type'])? $get['type'] : '';
        $this->assign('get',$get);

        if(!empty($get['typeid'])){
            $where['b.typeid'] = array('eq',$get['typeid']);
        }
        if(!empty($get['keywords'])){
            $where['b.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        if(!empty($get['type'])){
            $where['type'] = array('eq',$get['type']);
        }else{
            $where['type'] = array('eq',0);
        }

        $where['b.status'] = array('gt',-1);
        $data_list =  Db::name('article')->alias('b')->where($where)->order('id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });
        $this->assign('list',$data_list);
        $this->assign('meta_title','文章列表');

        if($get['type']==1){

            return $this->fetch('days_list'); //服务日程
        }else{
            return $this->fetch();
        }

    }

    //公共循环插入
    public function foreach_list($item){
        if(in_array(1,explode(',',$item['flag']))){
            $item['flag_title'] = '<font style="color: red;">首页推荐</font>';
        }
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
                if(!empty($checkres['data']['typeid'])){
                    $this->success($title.'成功', url('index',array('typeid'=>$checkres['data']['typeid'])));
                }else{
                    if(!empty($checkres['data']['type'])){
                        $this->success($title.'成功', url('index',array('type'=>$checkres['data']['type'])));
                    }
                }

            } else {
                $this->error($this->model->getError());
            }

        } else {
            $post = input('param.');
            $post['typeid']  = !empty($post['typeid']) ?  $post['typeid'] : '';
            $post['type']    = !empty($post['type']) ?  $post['type'] : '';
            $info =$this->model->where('id',$id)->find();
            $this->assign('info',$info);
            $typeid = !empty($info['typeid']) ? $info['typeid'] : $post['typeid'] ;
            $type = !empty($info['type']) && $info['type']>0 ? $info['type'] : $post['type'] ;
            $this->assign('typeid',$typeid);
            $this->assign('type',$type);

            $this->assign('meta_title',$title);
            $this->assign('post',$post);
            if(!empty($type) && $type==1){
                return $this->fetch('days_edit'); //服务日程
            }else{
                return $this->fetch();
            }

        }
    }


    /**
     *商家列表
     */
    public function shop_list(){
        if(IS_POST){
            $post= input('param.');
            $res = $this->model->shop_list($post);
            $this->ajaxReturn($res);
        }else{
            $get= input('param.');
            $this->assign('get',$get);

            $whet['status']   = array('eq',1);
            $list = db('shop')->where($whet)->order('id desc')->select();
            $str = '';
            foreach ($list as $ke=>$ve){
                $str .= '<tr><td class="bs-checkbox"><input type="checkbox" name="ids[]" value="'.$ve['id'].'"></td><td>'.$ve['id'].'</td><td class="course_title">'.$ve['title'].'</td><td><img src="'.get_image($ve['icon']).'" style="width:50px;" /></td></tr>';
            }
            $this->assign('str',$str);
            return $this->fetch();
        }
    }





}