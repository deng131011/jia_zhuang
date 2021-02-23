<?php

namespace app\app\controller;
use think\Controller;
use think\Db;
use app\app\model\OrderList as OrderListModel;
class Timego extends Controller {

    

    /**
     *超时未付订单
     *解冻冻结库存
     **/
    public function outtime_order(){
        Db::startTrans();
        try{
            $time = config('order_pay_out_time');
            $chatime = time()-$time;
            $where['o.pay_status']   = array('eq',0);
            $where['o.is_freeze']    = array('eq',0);
            $where['o.order_status'] = array('eq',0);
            $where['o.user_status']  = array('eq',1);
            $where['o.create_time']  = array('elt',$chatime);
            $list = db('order o')->join('order_list l','l.order_id = o.id')->join('product g','g.id = l.product_id')->field('l.order_id,sum(l.num) as num,g.id,g.stock,g.freeze_stock')->group('g.id')->where($where)->lock(true)->select();
            foreach ($list as $ke=>$ve){
                $datall[$ke]['id']              = $ve['id'];
                $datall[$ke]['freeze_stock']    = $ve['freeze_stock'] - $ve['num'];
                $datall[$ke]['stock']           = $ve['stock'] + $ve['num'];
            }

            if(!empty($datall)){
                $ProductGuige = new ProductGuigeModel();
                $res2 = $ProductGuige->saveAll($datall);
            }

            if(!empty($list)){
                db('order o')->where($where)->update(['is_freeze'=>1,'close_time'=>time()]);
            }

            Db::commit();
            p('ok');
        }catch (\Exception $e){
            Db::rollback();
            p('fail');
        }
    }


    /**
    *商品超过15天未评价的自动评价
     **/
    public function comment_product(){
        $chatime = time()-(1*86400);

        $where['o.pay_status']   = array('eq',1);
        $where['o.order_status'] = array('eq',2);
        $where['o.after_status'] = array('eq',0);
        $where['o.user_status']  = array('eq',1);
        $where['o.end_time']     = array('elt',$chatime);
        $where['l.is_comment']   = array('eq',0);
        $list = db('order o')->join('order_list l','o.id = l.order_id')->where($where)->field('o.uid,l.*')->select();
        foreach ($list as $ke=>$ve){
            $data[$ke]['type']      = 1;
            $data[$ke]['order_id']  = $ve['order_id'];
            $data[$ke]['return_id'] = $ve['product_id'];
            $data[$ke]['uid']       = $ve['uid'];
            $data[$ke]['is_system'] = 1;
            $data[$ke]['content']   = '系统默认好评';
            $data[$ke]['comment_type']   = 1;
            $data[$ke]['create_time']   = time();
            $data[$ke]['check_status']  = 1;

            $data2[$ke]['id']          = $ve['id'];
            $data2[$ke]['is_comment']  = 1;
            $data2[$ke]['update_time'] = mydate();
        }
        if(!empty($data)){
            db('comment')->insertAll($data);
        }

        if(!empty($data2)){
            $OrderListModel = new OrderListModel();
            $OrderListModel->saveAll($data2);
        }

        $orderlist = db('order o')->join('order_list l','o.id = l.order_id')->where($where)->field('o.*')->group('o.id')->select();
        $ii = 0;
        foreach ($orderlist as $ks=>$vs){
            //判断是否有未评价的商品
            $waap['order_id']     = array('eq', $vs['id']);
            $waap['after_status'] = array(array('eq', 0), array('eq', 3), 'or');
            $waap['is_comment']   = array('eq', 0);
            $vos = db('order_list')->where($waap)->find();
            if (empty($vos)) {
                $datab[$ii] = $vs['id'];
                $ii++;
            }
        }
        if(!empty($datab)){
            $mshp['id'] = array('in',implode(',',$datab));
            db('order')->where($mshp)->update(['order_status' => 3]);
        }
        p('ok');
    }


    /**
     *酒店超过15天未评价的自动评价
     **/
    public function comment_hotel(){
        $chatime = time()-(1*86400);
        $where['o.pay_status']   = array('eq',1);
        $where['o.order_status'] = array('eq',0);
        $where['o.after_status'] = array(array('eq',0),array('eq',3),'or');
        $where['o.user_status']  = array('eq',1);
        $where['o.level_date']   = array('elt',mydate($chatime,2));
        $list = db('hotel_order o')->where($where)->field('o.*')->select();

        $ii = 0;
        foreach ($list as $ke=>$ve){
            $data[$ke]['type']      = 2;
            $data[$ke]['order_id']  = $ve['id'];
            $data[$ke]['return_id'] = $ve['hotel_id'];
            $data[$ke]['uid']       = $ve['uid'];
            $data[$ke]['is_system'] = 1;
            $data[$ke]['content']   = '系统默认好评';
            $data[$ke]['comment_type']   = 1;
            $data[$ke]['create_time']   = time();
            $data[$ke]['check_status']  = 1;

            $datab[$ii]          = $ve['id'];
            $ii++;
        }
        if(!empty($data)){
            db('comment')->insertAll($data);
        }
        if(!empty($datab)){
            $mshp['id'] = array('in',implode(',',$datab));
            db('hotel_order')->where($mshp)->update(['order_status'=>3]);
        }
        p('ok');
    }




}