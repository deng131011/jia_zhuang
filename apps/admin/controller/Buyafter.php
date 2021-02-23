<?php

namespace app\admin\controller;
use think\Db;

class Buyafter extends Admin{

    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Buyafter');
        $this->applyType = ['1'=>'仅退款','2'=>'退款退货'];
    }

    /**
     * 商品售后申请记录
     */
    public function apply() {

        $get = input('param.');
        $get['after_status'] = !empty($get['after_status'])? $get['after_status'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['dates']     = !empty($get['dates'])? $get['dates'] : '';


        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
            $get['dates'] = $timearr[0].' / '.$timearr[1];
        }
        $this->assign('get',$get);
        if(!empty($get['after_status'])){
            $where['l.after_status'] = array('eq',$get['after_status']);
        }else{
            $where['l.after_status'] = array('gt',0);
        }

        $where['b.type'] = array('eq',1);
        $where['b.status'] = array('eq',1);

        $data_list =  Db::name('buyafter')->alias('b')->join('order_list l','b.order_listid = l.id')->where($where)->order('b.id desc')->field('b.*,b.order_number as tkorder_number,l.after_status,l.pay_price')->paginate(20)->each(function($item,$key){
            return $this->buyafter_foreach($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','售后申请记录');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        return $this->fetch();
    }


    //公共循环插入
    public function buyafter_foreach($item){
        if($item['type']==2){
            $item['product']      = modelField($item['product_id'],'hotel','title');
        }else{
            $item['order_number'] = modelField($item['order_id'],'order','order_number');
            $item['product']      = modelField($item['product_id'],'product','title');
        }

        $user = db('usermember')->find($item['uid']);
        $item['username'] = $user['nickname'] ;
        $item['afterStatus'] = afterStatus($item);
        $item['imgarr'] = imgArr($item['imgarr']) ;
        return $item;
    }



    /**
    *售后审核或详情
     */
    public function check_details(){
        if(IS_POST){

        }else{
            $get = input('param.');
            $where['b.id'] = array('eq',$get['id']);
            $get['type'] = !empty($get['type']) ? $get['type'] : 1;
            if($get['type']==2){
                $vo = Db::name('buyafter')->alias('b')->join('hotel_order l','b.order_id = l.id')->where($where)->field('b.*,b.order_number as tkorder_number,l.order_number')->find();
                if(!empty($vo)){
                    $vo['kou_scale'] = (config('hotel_refund_scale')*100).'%';
                }
            }else{
                $vo = Db::name('buyafter')->alias('b')->join('order_list l','b.order_listid = l.id')->where($where)->field('b.*,b.order_number as tkorder_number,l.after_status')->find();
            }


            $votb = $this->buyafter_foreach($vo);
            $vo = array_merge($vo,$votb);
            $this->assign('info',$vo);
            $this->assign('meta_title','售后审核详情');
            if($vo['type']==2){
                return $this->fetch('hotel_details');
            }else{
                return $this->fetch();
            }

        }
    }


    /**
    *商品审核
     */
    public function changeStatus(){
        $post = input('param.');
        if(empty($post['after_status'])){
            $this->error('请选择审核结果');
        }
        if($post['after_status']==3 && empty($post['reason'])){
            $this->error('请填写拒绝退款原因');
        }
        $post['reason'] = !empty($post['reason']) ? $post['reason'] : '';

        $mapt['id'] = array('eq',$post['id']);
        $mapt['order_number'] = array('eq',$post['order_number']);
        $buyafter = db('buyafter')->where($mapt)->find();
        if(empty($buyafter)){
            $this->error('非法操作，退款订单不存在');
        }
        Db::startTrans();
        try{
            db('buyafter')->where('id',$post['id'])->update(['chuli_status'=>$post['after_status'],'reason'=>$post['reason'],'check_time'=>time()]);

            //更新状态
            $res1 = db('order_list')->where('id',$post['order_listid'])->update(array('after_status'=>$post['after_status']));

            //添加记录
            $msg= '';
            if($post['after_status']==2){
                $msg = '平台同意退款';
                $tk_type = 6;
            }else if($post['after_status']==3){
                $msg = '平台拒绝退款，拒绝原因：'.$post['reason'];
                $tk_type = 7;
            }
            order_use_recode($buyafter['order_id'],0,2,$tk_type,$msg,$buyafter['order_listid']);

            addMessage($buyafter['uid'],'退款申请通知',$msg,2,1,$buyafter['id']);//添加系统消息

            Db::commit();
            $true = true;

        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $true = false;

        }

        if($true){
            $this->success('操作成功',url('apply'));
        }else{
            $this->error('操作失败');
        }

    }



    /**
    *退款
     */
    public function refund_kuan(){
        $post = input('param.');

        //调起退款
        $result =  model('Buyafter')->return_price($post);
        $this->ajaxReturn($result);
    }

    /**
     *判断是否要退运费
     */
    public function check_send_price(){
        if(IS_POST){
            $post = input('param.');
            $mapt['id'] = array('eq',$post['buyafter_id']);
            $mapt['order_number'] = array('eq',$post['tkorder_number']);
            $buyafter = db('buyafter')->where($mapt)->find();
            if(empty($buyafter)){
                $this->ajaxReturn(['code'=>201,'msg'=>'售后记录不存在']);
            }
            $result = model('Buyafter')->check_send_price($buyafter);
            $this->ajaxReturn($result);
        }
    }



    /**
     * 酒店售后申请记录
     */
    public function hotel_refund() {

        $get = input('param.');
        $get['after_status'] = !empty($get['after_status'])? $get['after_status'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['dates']     = !empty($get['dates'])? $get['dates'] : '';


        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
            $get['dates'] = $timearr[0].' / '.$timearr[1];
        }
        $this->assign('get',$get);


        $where['b.type']   = array('eq',2);
        $where['b.status'] = array('eq',1);
        $data_list =  Db::name('buyafter')->alias('b')->join('hotel_order l','b.order_id = l.id')->where($where)->order('b.id desc')->field('b.*,b.order_number as tkorder_number,l.order_number')->paginate(20)->each(function($item,$key){
            return $this->buyafter_foreach($item);
        });


        $this->assign('list',$data_list);
        $this->assign('meta_title','售后申请记录');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        return $this->fetch();
    }


    /**
     *酒店审核
     */
    public function hotel_change_status(){
        $post = input('param.');
        if(empty($post['after_status'])){
            $this->error('请选择审核结果');
        }
        if($post['after_status']==3 && empty($post['reason'])){
            $this->error('请填写拒绝退款原因');
        }
        $post['reason'] = !empty($post['reason']) ? $post['reason'] : '';

        $mapt['id'] = array('eq',$post['id']);
        $mapt['order_number'] = array('eq',$post['order_number']);
        $buyafter = db('buyafter')->where($mapt)->find();
        if(empty($buyafter)){
            $this->error('非法操作，退款订单不存在');
        }
        Db::startTrans();
        try{
            db('buyafter')->where('id',$post['id'])->update(['chuli_status'=>$post['after_status'],'reason'=>$post['reason'],'check_time'=>time()]);

            //更新状态
            $res1 = db('hotel_order')->where('id',$post['order_id'])->update(array('after_status'=>$post['after_status']));

            //添加记录
            $msg= '';
            if($post['after_status']==2){
                $msg = '平台同意退款';
                $tk_type = 6;
            }else if($post['after_status']==3){
                $msg = '平台拒绝退款，拒绝原因：'.$post['reason'];
                $tk_type = 7;
            }
            order_use_recode($buyafter['order_id'],0,2,$tk_type,$msg,0,2);

            addMessage($buyafter['uid'],'退款申请通知',$msg,3,2,$buyafter['id']);//添加系统消息

            Db::commit();
            $true = true;

        }catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $true = false;

        }

        if($true){
            $this->success('操作成功',url('hotel_refund'));
        }else{
            $this->error('操作失败');
        }

    }



}