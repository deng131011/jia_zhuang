<?php

namespace app\admin\controller;
use think\Db;
use think\Request;
use app\app\model\Shop as appShoptModel;
use app\admin\model\ShopTjlist as ShopTjlistModel;
/**
 * Class Feedback
 * @package app\admin\controller
 * 商家菜单
 */
class Shopmenu extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Shopmenu');

    }

    /**
     * 商家菜单列表
     */
    public function index() {
        $get = input('param.');

        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['type1']     = !empty($get['type1'])? $get['type1'] : '';
        $get['shop_id']     = !empty($get['shop_id'])? $get['shop_id'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['b.title|s.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        if(!empty($get['type1'])){
            $where['b.type1'] = array('eq',$get['type1']);
        }
        if(!empty($get['shop_id'])){
            $where['b.shop_id'] = array('eq',$get['shop_id']);
        }
        $where['b.status'] = array('gt',-1);
        $data_list =  db('shop_menu b')
            ->join('shop s','b.shop_id = s.id')
            ->where($where)
            ->field('b.*,s.title as shop_title')
            ->order('b.id desc')
            ->paginate(20)->each(function($item,$key){
                return $this->foreach_list($item);
            });
        $this->assign('list',$data_list);
        $this->assign('meta_title','商家菜单列表');

        if(!empty($get['shop_id'])){
            $appShoptModel = new appShoptModel();
            $menu_type = $appShoptModel->menu_type(['shop_id'=>$get['shop_id']]);
            $this->assign('menu_type',$menu_type);

        }

        return $this->fetch();
    }

    public function foreach_list($item){
        $item['category_title'] = modelField($item['type1'],'shop_menu_type','title');
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
                $this->success($title.'成功', url('index',array('shop_id'=>$post['shop_id'])));
            } else {
                $this->error($this->model->getError());
            }

        } else {
            $get = input('param.');
            $info = Db::table('qyc_shop_menu')->where('id',$id)->find();
            $this->assign('info',$info);
            $this->assign('meta_title',$title);//标题

            //菜品分类
            $shop_id = !empty($info['shop_id']) ? $info['shop_id'] : $get['shop_id'];
            $appShoptModel = new appShoptModel();
            $menu_type = $appShoptModel->menu_type(['shop_id'=>$shop_id]);
            $this->assign('menu_type',$menu_type);
            $this->assign('shop_id',$shop_id);
            return $this->fetch();
        }
    }



    /**
     * 菜单分类
     */
    public function menu_type() {

        if(IS_POST){
            $post = input('param.');

            //编辑
            if(!empty($post['id'])){
                if($post['type']==1){
                    if(empty(trim($post['val']))){
                        $this->ajaxReturn(['code'=>201,'msg'=>'标题不能为空']);
                    }
                    $data['title'] = trim($post['val']);
                }else if($post['type']==2){
                    $data['sort'] = intval($post['val']);
                }
                $data['update_time'] = time();
                $res = db('shop_menu_type')->where('id',$post['id'])->update($data);
                if($res){
                    $this->ajaxReturn(['code'=>200,'msg'=>'编辑成功']);
                }else{
                    $this->ajaxReturn(['code'=>201,'msg'=>'编辑失败']);
                }
            }

            //新增
            if(empty(trim($post['title']))){
                $this->ajaxReturn(['code'=>201,'msg'=>'请输入分类标题']);
            }
            $where['title'] = array('eq',$post['title']);
            $where['shop_id'] = array('eq',$post['shop_id']);
            $vo = db('shop_menu_type')->where($where)->find();
            if(!empty($vo)){
                $this->ajaxReturn(['code'=>201,'msg'=>'分类不能重复']);
            }
            $post['create_time'] = time();
            $res = db('shop_menu_type')->insertGetId($post);
            if($res){
                $this->ajaxReturn(['code'=>200,'msg'=>'添加成功']);
            }else{
                $this->ajaxReturn(['code'=>201,'msg'=>'添加失败']);
            }

        }else{
            $get = input('param.');
            if(empty($get['shop_id'])){
                $this->error('缺少商家id');
            }
            $this->assign('get',$get);
            $where['b.status'] = array('eq',1);
            $where['b.shop_id'] = array('eq',$get['shop_id']);
            $data_list =  db('shop_menu_type b')->where($where)->order('b.id desc')->select();
            $this->assign('list',$data_list);
            $this->assign('meta_title','菜单分类');
            return $this->fetch();
        }
    }


    //删除分类
    public function delete_type(){
        $get = input('param.');
        $where['type1'] = array('eq',$get['ids']);
        $where['shop_id'] = array('eq',$get['shop_id']);
        $vo =  db('shop_menu')->where($where)->find();
        if(!empty($vo)){
            $this->ajaxReturn(['code'=>201,'msg'=>'该分类下已关联商品，先接触关联再删除']);
        }
        $res =  db('shop_menu_type')->delete($get['ids']);
        if($res){
            $this->ajaxReturn(['code'=>200,'msg'=>'删除成功']);
        }else{
            $this->ajaxReturn(['code'=>201,'msg'=>'删除失败']);
        }
    }








}