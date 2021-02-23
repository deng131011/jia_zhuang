<?php

namespace app\admin\controller;

use think\Db;

class Product extends Admin{
    
    protected $linkModel;
    protected $linkType;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Product');
        $this->ggmodel = model('ProductGuige');
        $this->typelist = logic('Product')-> producttype();


        $this->assign('typelist',$this->typelist);
    }

    public function index() {
        $get = input('param.');
        $get['type1'] = !empty($get['type1']) ? $get['type1'] : '';
        $this->assign('get',$get);

        $map = array();
        if(!empty($get['keywords'])){
            $map['title'] = array('like','%'.trim($get['keywords']).'%');
        }
        if(!empty($get['type1'])){
            $map['type1'] = array('eq',$get['type1']);
        }
        if(!empty($get['status'])){
            if($get['status']=='sj'){
                $map['status'] = array('eq',1);
            }
            if($get['status']=='xj'){
                $map['status'] = array('eq',2);
            }
        }else{
            $map['status'] = array('gt',-1);
        }
        $data_list =  Db::name('product')->where($map)->order('id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','商品列表');
        return $this->fetch();
    }

    public function foreach_list($item){
        $item['imgurl'] = get_image($item['icon']);
        $item['gg_count'] = db('product_guige')->where('product_id',$item['id'])->count();
        //分类
        $item['typea_title'] = db('producttype')->where('id',$item['type1'])->value('title');
        $item['typeb_title'] = db('producttype')->where('id',$item['type2'])->value('title');

        return $item;
    }

    /**
 * 编辑链接
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

            $this->assign('meta_title',$title);
            $field = ['name'=>'imgarr','description'=>'','options'=>['path_type'=>'pictures'],'value'=>$info['imgarr']];
            $this->assign('field',$field);

            $fieldarrb = ['name'=>'content_img','description'=>'','options'=>['path_type'=>'pictures'],'value'=>$info['content_img']];
            $this->assign('fieldarrb',$fieldarrb);

            if(!empty($info)){
                $two_typelist = logic('Product')-> producttype($info['type1']);
                $this->assign('two_typelist',$two_typelist);
            }

            return $this->fetch();
        }
    }


    /**
    *ajax搜索产品返回
     */
    public function ajax_product(){
        $post = input('param.');
        $res = model('Product')->ajax_product($post);
        return $res;
    }


    /**
     *ajax获取下级分类
     */
    public function son_type(){
        $post = input('param.');
        $where['pid']   = array('eq', $post['parent_id']);
        $where['level'] = array('eq', $post['level']);
        $list = db('producttype')->where($where)->order('sort asc')->select();

        $str = '';
        foreach($list as $ke=>$ve){
            $str .= '<option value='.$ve['id'].'>'.$ve['title'].'</option>';
        }
        $this->ajaxReturn(array('code'=>200,'msg'=>$str));
    }

    //商品规格
    public function gui_ge(){
        $get = input('param.');
        $this->assign('get',$get);

        $map = array();
        if(!empty($get['keywords'])){
            $map['gg_title'] = array('like','%'.trim($get['keywords']).'%');
        }
        $map['product_id'] = array('eq',$get['product_id']);
        $data_list =  Db::name('product_guige')->where($map)->order('id desc')->paginate(20);

        $this->assign('list',$data_list);
        $this->assign('meta_title','商品规格');
        return $this->fetch();
    }


    /**
     * 编辑链接
     */
    public function gg_edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            $post = input('param.');
            //验证数据
            $checkres = $this->ggmodel->checkform($post);
            if($checkres['code']==201){
                $this->error($checkres['msg']);
            }
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($this->ggmodel->editData($checkres['data'])) {
                $this->success($title.'成功', url('gui_ge',array('product_id'=>$post['product_id'])));
            } else {
                $this->error($this->model->getError());
            }

        } else {
            $get = input('param.');
            $info =$this->ggmodel->where('id',$id)->find();
            $this->assign('info',$info);
            $this->assign('meta_title',$title);
            $product_id = !empty($info) ? $info['product_id'] : $get['product_id'];
            $this->assign('product_id',$product_id);
            $typelist = db('spec_type')->where('status',1)->order('type asc')->select();
            $this->assign('typelist',$typelist);

            return $this->fetch();
        }
    }


}