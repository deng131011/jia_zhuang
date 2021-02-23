<?php

namespace app\admin\controller;
use think\Db;

class Shopapply extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Shopapply');
    }

    /**
     *商家申请列表
     */
    public function index() {
        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);
         
        if(!empty($get['dates'])){
            $timearr = explode('—',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['f.create_time'] = $time;
            }
        }
		if(!empty($get['keywords'])){
			$where['f.names|f.tel'] = array('like','%'.$get['keywords'].'%');
		}
        $where['f.status'] = array('gt',-1);
        $data_list =  Db::name('shop_apply')->alias('f')->join('usermember u','f.uid = u.id')->where($where)->order('f.id desc')->field('f.*')->paginate(20)->each(function($item,$key){
            return $this->list_foreach($item);
        });
        $this->assign('list',$data_list);
        $this->assign('meta_title','商家申请');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        return $this->fetch();
    }


    //公共循环插入
    public function list_foreach($item){

        $item['code_a'] = get_image($item['code_a']) ;
        $item['code_b'] = get_image($item['code_b']) ;
        $item['yyzz_img'] = get_image($item['yyzz_img']) ;
        $item['check'] = check_status($item['check_status']) ;
        return $item;
    }


    /**
    *详情
     */
    public function details(){
        if(IS_POST){
            // 提交数据
            $post = $this->request->param();
            $res = $this->model->add_check_result($post);
            if($res['code']==200){
                $this->success($res['msg'],url('index'));
            }else{
                $this->error($res['msg']);
            }

        }else{
            $get = input('param.');
            $where['f.id'] = array('eq',$get['id']);
            $vo =  Db::name('shop_apply')->alias('f')->join('usermember u','f.uid = u.id')->where($where)->field('f.*')->find();
            $vo = $this->list_foreach($vo);

            $this->assign('info',$vo);
            $this->assign('meta_title','商家申请');

            if(!empty($get['is_bang']) && $get['is_bang']==1){
                $whes['uid']    = array('eq',0);
                $whes['status'] = array('eq',1);
                $shop_list = db('shop')->where($whes)->select();
                $this->assign('shop_list',$shop_list);
                return $this->fetch('details_b');
            }else{
                return $this->fetch();
            }

        }
    }


    /**
    *绑定商家
     */
    public function bang_shop(){
        $get = input('param.');
        if(empty($get['shop_id'])){
            $this->ajaxReturn(['code'=>201,'msg'=>'请选择商家']);
        }
        if(empty($get['uid'])){
            $this->ajaxReturn(['code'=>201,'msg'=>'缺少用户参数']);
        }

        db('shop_apply')->where('id',$get['id'])->update(['shop_id'=>$get['shop_id']]);

        $data['uid']         = $get['uid'];
        $data['update_time'] = time();
        $res = db('shop')->where('id',$get['shop_id'])->update($data);
        if($res){
            $this->ajaxReturn(['code'=>200,'msg'=>'绑定成功','url'=>url('index')]);
        }else{
            $this->ajaxReturn(['code'=>201,'msg'=>'绑定失败']);
        }

    }


}