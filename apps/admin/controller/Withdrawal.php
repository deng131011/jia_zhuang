<?php

namespace app\admin\controller;
use think\Db;
use app\admin\model\Withdrawal as WithdrawalModel;
class Withdrawal extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Buyafter');
        $this->applyType = ['1'=>'退货','2'=>'换货'];
    }

    /**
     * 提现列表
     */
    public function index() {

        $get = input('param.');
        $get['dk_status'] = !empty($get['dk_status'])? $get['dk_status'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['dates'])){
            $dates = explode('/',$get['dates']);
            $where['b.create_time'] = array('between',[strtotime($dates[0]),strtotime($dates[1].' 23:59:59')]);
        }
        if(!empty($get['dk_status'])){
            $where['b.dk_status'] = array('eq',$get['dk_status']);
        }

        $where['b.status'] = array('gt',-1);
        $data_list =  Db::name('withdrawal')->alias('b')->join('usermember u','u.id = b.uid')->where($where)->order('b.dk_status asc,b.id desc')->field('b.*,u.username')->paginate(20)->each(function($item,$key){
            return $this->buyafter_foreach($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','提现申请列表');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        return $this->fetch();
    }


    //公共循环插入
    public function buyafter_foreach($item){
        if($item['dk_status']==1){
            $item['status_title'] = '<font style="color: red"> 未打款 </font>';
        }else if($item['dk_status']==2){
            $item['status_title'] = '<font> 拒绝打款 </font>';
        }else if($item['dk_status']==3){
            $item['status_title'] = '<font> 已打款 </font>';
        }
        return $item;
    }


    /**
    *同意提现
     */
    public function sure_forprice(){
        $post = input('param.');
        if(empty($post['id']) || empty($post['ordernum'])){
            $this->error('非法操作');
        }
        $WithdrawalModel = new WithdrawalModel();
        $res = $WithdrawalModel->sure_forprice($post);
        if($res['code']==200){
            $this->success($res['msg']);
        }else{
            $this->error($res['msg']);
        }
    }


    //这是提现测试，待有3个月流水时接入提现接口中
    public function test(){
        $data['partner_trade_no'] = time().rand(10000,99999);
        $data['openid']           = 'oxMfz50x9_dhO3H9xLhO3nV8unuY';
        $data['total_amount']     = 0.01;
        $data['check_name']       = '张三';
        $data['desc']             = '余额提现';
        $res = model('Tixianmodel')->sendMoney($data);
        p($res);
    }



}