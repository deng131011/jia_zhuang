<?php

namespace app\admin\controller;
use think\Db;
use app\app\model\Address as appAddressModel;
class China extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('China');
    }

    /**
     * 订单列表
     */
    public function index() {
        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['pid']     = !empty($get['pid'])? $get['pid'] : 0;
        $this->assign('get',$get);

		if(!empty($get['keywords'])){
			$where['title'] = array('like','%'.$get['keywords'].'%');
		}
		
		
		
		$where['pid'] = array('eq',$get['pid']);
		$where['level'] = array('in','1,2,3');
		$where['status'] = array('gt',-1);
		

        $data_list =  Db::name('china')->where($where)->order('id asc')->paginate(20);
        $this->assign('list',$data_list);
        $this->assign('meta_title','地区列表');
        
        return $this->fetch();
    }


    


    /**
     *详情
     */
    public function edit($id=0){

        $title = $id>0 ? "编辑":"新增";
        $matchModel = $this->model;
        if(IS_POST){
            $post = input('param.');
			
			
            //验证数据
            
            if(empty($post['title'])){
				$this->error('标题不能为空');
			} 
			
			if(!empty($post['start_addr']) && empty($post['lng_lat'])){
				
				$this->error('有配送起点时必须获取经纬度');
			}
			
			if($post['pid']>0){
                $sj_zone =  db('china')->find($post['pid']);
                $post['level']      = $sj_zone['level']+1;
            }else{
                $post['level']      = 1;
            }
			
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            if ($matchModel->editData($post)) {
                $this->success($title.'成功', url('index',array('pid'=>$post['pid'])));
            } else {
                $this->error($this->model->getError());
            }

        }else{
            $get = input('param.');
            $info =db('china')->where('id',$id)->find();
			
			
			if(empty($id)){
				$get['pid'] = !empty($get['pid']) ? $get['pid'] : '';
			}else{
				$get['pid'] = $info['pid'];
			}
			
            $this->assign('info',$info);
            $this->assign('meta_title',$title);
            $this->assign('get',$get);
            return $this->fetch();
        }
    }



	/**
	*获取经纬度
	*/
	public function get_lng_lat(){
		$post = input('param.');
		$appAddressModel = new appAddressModel();
        $res = $appAddressModel->get_lng_lat($post);
        $this->ajaxReturn($res);
	}
	
	
	
}