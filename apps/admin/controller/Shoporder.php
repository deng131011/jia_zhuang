<?php

namespace app\admin\controller;
use think\Db;

class Shoporder extends Admin{

    function _initialize()
    {
        parent::_initialize();
            $this->model = model('Shoporder');
    }

    /**
     * 订单列表
     */
    public function index() {
        $get = input('param.');
        $get['hexiao_status'] = !empty($get['hexiao_status'])? $get['hexiao_status'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';


        $where = $this->model->search_form($get); //搜索条件
        $data_list =  Db::name('shop_order')->alias('b')->join('usermember u','u.id = b.uid')->where($where)->field('b.*,u.username,u.mobile')->order('b.id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });
		
        $this->assign('list',$data_list);
        $this->assign('meta_title','商家订单');
        $this->assign('field',['name'=>'dates','id'=>'dates']);

        if(!empty($get['excel'])){
            $excel_list =  Db::name('shop_order')->alias('b')->join('usermember u','u.id = b.uid')->where($where)->field('b.*,u.username,u.mobile')->order('b.id desc')->select();
            foreach ($excel_list as $ks=>$vs){
                $excel_list[$ks] = $this->foreach_list($vs);
            }
            $this->exportExcel($excel_list); //导出excel
        }


        $get['dates'] = return_dates($get);
        $this->assign('get',$get);
        return $this->fetch();
    }


    //公共循环插入
    public function foreach_list($item){
        $item['orderstatus']      = shopOrderStatus($item);
        $item['hotel_json']      = !empty($item['hotel_json']) ? json_decode($item['hotel_json'],true) : '';
        return $item;
    }


    /**
     *订单详情
     */
    public function details(){

        $post =  input('param.');
        if(empty($post['order_id'])){
            $this->error('非法操作！');
        }
        $where['b.id'] = array('eq',$post['order_id']);
        $vo =  Db::name('shop_order')->alias('b')->join('usermember u','u.id = b.uid')->where($where)->field('b.*,u.username,u.mobile')->order('b.id desc')->find();
        $vo = $this->foreach_list($vo);
        $this->assign('info',$vo);

        $this->assign('meta_title','商家订单详情');
        return $this->fetch();
    }





    /**
    *打印
     */
    public function prints(){
         return $this->details();
    }




    /**
     *删除订单
     */
    public function delOrder(){
        $post =  input('param.');
        if(empty($post['ids'])){
            $this->error('请选择数据！');
        }
        $res = $this->model->delOrder($post);
        if($res['code']==200){
            $this->success('删除成功！');
        }else{
            $this->error($res['msg']);
        }
    }






    /**
    *导出excel
    */
    public function exportExcel($list){

        $thead = [
            ['title'=>'核销码','ziduan'=>'hexiao_code'],
            ['title'=>'下单用户','ziduan'=>'username'],
            ['title'=>'用户电话','ziduan'=>'mobile'],
            ['title'=>'预约时间','ziduan'=>'order_date'],
            ['title'=>'实际支付','ziduan'=>'pay_price'],
            ['title'=>'下单时间','ziduan'=>'create_time'],
            ['title'=>'订单状态','ziduan'=>'orderstatus'],
        ];

        foreach ($list as $ke=>$ve){
            $list[$ke]['username']       = !empty($ve['order_user']) ? $ve['order_user']: $ve['username'];
            $list[$ke]['mobile']       = !empty($ve['order_mobile']) ? $ve['order_mobile'] : $ve['mobile'];
            $list[$ke]['create_time'] = mydate($ve['create_time'],2);
            $list[$ke]['orderstatus'] = $ve['orderstatus']['msg2'];
        }

        downExcelExport($thead,$list,'商家订单记录');
    }





}