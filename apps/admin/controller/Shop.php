<?php

namespace app\admin\controller;
use think\Db;
use think\Request;
use app\app\model\Product as appProductModel;
use app\admin\model\ShopTjlist as ShopTjlistModel;
/**
 * Class Feedback
 * @package app\admin\controller
 * 商家管理
 */
class Shop extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Shop');
        error_reporting(E_ERROR | E_PARSE );//关闭tp5严格模式

        $appProductModel = new appProductModel();
        $category = $appProductModel->type_list(['type'=>2]);
        $this->assign('category',$category['data']);
    }

    /**
     * 商家管理
     */
    public function index() {
        $get = input('param.');

        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['category_id']     = !empty($get['category_id'])? $get['category_id'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['b.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        if(!empty($get['category_id'])){
            $where['b.category_id'] = array('eq',$get['category_id']);
        }
        $where['b.status'] = array('gt',-1);
        $data_list =  db('shop b')->join('usermember u','b.uid = u.id','LEFT')->where($where)->order('b.id desc')->field('b.*,u.username,u.mobile as u_mobile')->paginate(20)->each(function($item,$key){
              return $this->foreach_list($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','商家列表');
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
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->model->getError());
            }

        } else {

            $info = Db::table('qyc_shop')->where('id',$id)->find();
            $this->assign('info',$info);
            $this->assign('meta_title',$title);//标题

            $field = ['name'=>'imgarr','description'=>'','options'=>['path_type'=>'pictures'],'value'=>$info['imgarr']];
            $this->assign('field',$field);



            //申请成功的用户
            $maps['u.user_type']    = array('eq',2);
            $maps['s.check_status'] = array('eq',1);
            $userlist = db('usermember u')->join('shop_apply s','s.uid = u.id')->where($maps)->field('s.names')->select();
            $this->assign('userlist',$userlist);

            return $this->fetch();
        }
    }



    /**
     * 商家推荐信息列表
     */
    public function tj_list() {
        $get = input('param.');



        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['category_id']     = !empty($get['category_id'])? $get['category_id'] : '';
        $this->assign('get',$get);
        if(!empty($get['keywords'])){
            $where['b.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status'] = array('gt',-1);
        $where['b.shop_id'] = array('eq',$get['shop_id']);
        $data_list =  db('shop_tjlist b')->where($where)->order('b.id desc')->paginate(20);
        $this->assign('list',$data_list);
        $this->assign('meta_title','推荐列表');
        return $this->fetch();
    }

    /**
     * 推荐列表编辑
     */
    public function tj_edit($id=0) {
        $title = $id>0 ? "编辑":"新增";
        if (IS_POST) {
            $post = input('param.');
            //验证数据
            if(empty($post['title'])){
                $this->error('请填写标题');
            }
            if(empty($post['icon'])){
                $this->error('请上传图片');
            }
            $post['create_time'] = time();
            $ShopTjlistModel = new ShopTjlistModel();
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($ShopTjlistModel->editData($post)) {
                $this->success($title.'成功', url('tj_list',array('shop_id'=>$post['shop_id'])));
            } else {
                $this->error($this->model->getError());
            }

        } else {
            $get = input('param.');
            $info = Db::table('qyc_shop_tjlist')->where('id',$id)->find();
            $this->assign('info',$info);
            $this->assign('get',$get);
            $this->assign('meta_title',$title);//标题
            $shop_id = !empty($info['shop_id']) ? $info['shop_id'] : $get['shop_id'];
            $this->assign('shop_id',$shop_id);
            return $this->fetch();
        }
    }








}