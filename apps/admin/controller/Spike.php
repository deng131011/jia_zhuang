<?php

namespace app\admin\controller;
use app\common\model\Links as LinksModel;
use think\Db;

class Spike extends Admin{
    
    protected $linkModel;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Spike');
    }

    /**
     * 限时秒杀
     */
    public function index() {



        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['b.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status'] = array('gt',-1);

        $data_list =  Db::name('spike')->alias('b')->where($where)->order('id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','限时秒杀');
        $this->assign('field',['name'=>'dates','id'=>'dates']);

        return $this->fetch();
    }

    //公共循环插入
    public function foreach_list($item){
        $item['start_time'] = mydate($item['start_time'],2);
        $item['end_time']   = mydate($item['end_time'],2);
        $item['end_status'] = spike_status($item);
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
            if(!empty($info)){
                $info['start_time'] = mydate($info['start_time'],2);
                $info['end_time']  = mydate($info['end_time'],2);
            }
            $this->assign('info',$info);
            $map['status'] = array('gt',-1);

            $this->assign('meta_title',$title);
            return $this->fetch();
        }
    }

    //添加限时秒杀产品
    public function addSpike($id=0){
        if(IS_POST){
            $post = input('param.');
            if(empty($post['spike_id'])){
                $this->error('请选择所属秒杀活动！');
            }
            $res = $this->model->addSpike($post);
            if ($res['code']==200) {
                $this->success($res['msg'], url('index'));
            } else {
                $this->error($res['msg']);
            }
        }else{
            $post = input('param.');
            if(empty($post['ids'])){
                $this->error('请选择数据！');
            }
            $where['id'] = array('in',$post['ids']);
            $data_list = model('Product')->where($where)->select();
            foreach ($data_list as $ke=>$ve){
                $data_list[$ke]['ms_stock'] = 0;
            }
            $info['list'] = $data_list;
            $title = $id ? "编辑":"新增";
            $this->assign('meta_title',$title);
            $this->assign('info',$info);
            $this->assign('spike_list',$this->model->spike_list());
            return $this->fetch();
        }
    }


    //秒杀活动对应的商品
    public function spike_product(){
        $get = $_GET;
        $map = array();
        if(!empty($get['keywords'])){
            $map['title'] = array('like','%'.trim($get['keywords']).'%');
        }

        $map['status'] = array('gt',-1);
        // 获取所有链接
        list($data_list,$total) = $this->model->getListByPage($map,true,'sort,create_time desc');
        foreach ($data_list as $ke=>$ve){
            $data_list[$ke]['start_time'] = mydate($ve['start_time']);
            $data_list[$ke]['end_time']   = mydate($ve['end_time']);
            $data_list[$ke]['end_status'] = spike_status($ve);
        }

        // return $this->fetch();
        $content = builder('List')
            ->addTopButton('addnew')    // 添加新增按钮
            ->addTopButton('resume')  // 添加启用按钮
            ->addTopButton('forbid')  // 添加禁用按钮
            ->setSearch('请输入关键字')
            ->keyListItem('title', '产品名称')
            ->keyListItem('start_time', '开始日期')
            ->keyListItem('end_time', '结束日期')
            ->keyListItem('end_status', '结束状态')
            ->keyListItem('status', '禁用状态', 'status')
            ->keyListItem('right_button', '操作', 'btn')
            ->setListData($data_list)     // 数据列表
            ->setListPage($total)  // 数据列表分页
            ->addRightButton('edit')     // 添加编辑按钮
            ->addRightButton('delete')  // 添加删除按钮
            ->addRightButton('forbid',['title'=>'下架']) // 添加禁用按钮
            ->fetch();

        return Iframe()
            ->setMetaTitle('友情链接')  // 设置页面标题
            ->search([
                ['name'=>'keywords','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],
            ])
            ->content($content);
    }


    /**
    *秒杀商品列表
     */
    public function lists(){
        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['p.title'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status'] = array('gt',-1);
        $where['b.spike_id'] = array('eq',$get['spike_id']);

        $data_list =  Db::name('spike_product')->alias('b')->join('product p','p.id=b.product_id')->where($where)->order('b.id desc')->field('b.*,p.title')->paginate(20);

        $this->assign('list',$data_list);
        $this->assign('meta_title','限时秒杀');
        $this->assign('field',['name'=>'dates','id'=>'dates']);

        return $this->fetch();
    }


    //编辑秒杀商品
    public function edit_pro($id=0) {
        if(IS_POST){
            $post = input('param.');
            if(empty($post['id'])){
                $this->error('缺少参数！');
            }
            $post['update_time'] = time();
            $res =db('spike_product')->update($post);

            if ($res) {
                $this->success('提交成功', url('lists',array('spike_id'=>$post['spike_id'])));
            } else {
                $this->error('提交失败');
            }
        }else{
            $post = input('param.');

            $where['s.id'] = array('eq',$post['id']);
            $info = db('spike_product s')->join('product p','p.id = s.product_id')->where($where)->field('s.*,p.title')->find();

            $title = $id ? "编辑":"新增";
            $this->assign('meta_title',$title);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }



}