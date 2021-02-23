<?php

namespace app\admin\controller;
use think\Db;

class Payorder extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('PayOrder');
    }

    /**
     * 充值订单列表
     */
    public function index() {
        $get = input('param.');
        $get['pay_status'] = !empty($get['pay_status'])? $get['pay_status'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';


        $where = $this->model->search_form($get); //搜索条件
        $data_list =  Db::name('payup_order')->alias('b')->join('usermember u','u.id=b.uid')->where($where)->order('b.id desc')->field('b.*,u.mobile,u.username')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });
        $this->assign('list',$data_list);
        $this->assign('meta_title','充值订单列表');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        if(!empty($get['excel'])){
            $excel_list =  Db::name('payup_order')->alias('b')->join('usermember u','u.id=b.uid')->where($where)->order('b.id desc')->field('b.*,u.mobile,u.username')->select();
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
        //$item['orderstatus']      = houseOrderStatus($item);
        return $item;
    }


    /**
    *导出excel
    */
    public function exportExcel($list){

        $thead = [
            ['title'=>'订单号','ziduan'=>'order_number'],
            ['title'=>'充值用户','ziduan'=>'username'],
            ['title'=>'联系电话','ziduan'=>'mobile'],
            ['title'=>'充值金额','ziduan'=>'price'],
            ['title'=>'支付状态','ziduan'=>'pay_status'],
            ['title'=>'支付方式','ziduan'=>'pay_type'],
            ['title'=>'下单时间','ziduan'=>'create_time'],
        ];

        foreach ($list as $ke=>$ve){
            $list[$ke]['pay_status'] = $ve['pay_status']==1?'支付成功':'待支付';
            $list[$ke]['pay_type'] = payTypeTitle($ve['pay_type']);
            $list[$ke]['create_time'] = mydate($ve['create_time'],2);
        }

        downExcelExport($thead,$list,'充值订单记录');
    }





}