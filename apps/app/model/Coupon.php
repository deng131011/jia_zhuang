<?php

namespace app\app\model;
use think\Db;
use think\Model;
class Coupon extends Model {
    protected $table = 'qyc_coupon';

    /**
     *没领的优惠券列表
     */
    public function noget_coupon($post){
        $whe['uid']         = array('eq',$post['uid']);
        $recodeList = db('coupon_recode')->where($whe)->select();
        if(!empty($recodeList)){
            $idarr = implode(',',array_column($recodeList,'coupon_id'));
            $where['id']      = array('not in',$idarr);
        }
        $where['status']      = array('eq',1);
        $where['get_type']    = array('eq',1);
        $list = db('coupon')->where($where)->order('id desc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->foreach_coupon($ve);
        }
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }



    public function foreach_coupon($ve){
        $ve['end_time'] = mydate($ve['end_time'],1);
        return $ve;
    }



    /**
     *领取优惠券
     */
    public function get_coupon($post){
        //检测是否已领取过
        $map['uid'] = array('eq',$post['uid']);
        $map['coupon_id'] = array('eq',$post['coupon_id']);
        $checkRes = db('coupon_recode')->where($map)->find();
        if(!empty($checkRes)){
            return array('code'=>201,'msg'=>'您已领取过');
        }
        $whe['id']          = array('eq',$post['coupon_id']);
        $whe['status']      = array('eq',1);
        $vo = db('coupon')->where($whe)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'已被别人抢完了');
        }
        Db::startTrans();
        try{
            //添加记录
            $data['coupon_id']  = $vo['id'];
            $data['end_time']   = time()+($vo['days']*86400);
            $data['uid']        = $post['uid'];
            $data['get_time']   = time();
            $data['get_date']   = mydate('',1);
            $res2 = db('coupon_recode')->insert($data);
            //执行提交操作
            Db::commit();
            return array('code'=>200,'msg'=>'领取成功');
        }catch(\Exception $e){
            //回滚事务
            Db::rollback();
            //获取提示信息
            return array('code'=>201,'msg'=>'领取失败');
        }
    }



    /**
     *我的优惠券列表
     * type:1未使用，2已使用,3已过期
     */
    public function my_coupon($post,$count=''){
        if($post['type']==1){
            $where['r.is_use']      = array('eq',0);
            $where['r.end_time']    = array('gt',time());
        }else if($post['type']==2){
            $where['r.is_use']      = array('eq',1);
        }else if($post['type']==3){
            $where['r.is_use']      = array('eq',0);
            $where['r.end_time']    = array('elt',time());
        }
        $where['r.uid']         = array('eq',$post['uid']);
        $where['r.user_status']      = array('eq',1);

        if($count=='count'){
            $listcount = db('coupon c')->join('coupon_recode r','r.coupon_id=c.id')->where($where)->count();
            return $listcount;
        }
        $list = db('coupon c')->join('coupon_recode r','r.coupon_id=c.id')->field('c.*')->where($where)->order('r.id desc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->foreach_coupon($ve);
        }
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }




    /**
     *判断是否有可用的优惠券
     * $area_type:1商城优惠券，2课程优惠券,3直播优惠券，4题库
     */
    public function check_coupon($uid,$total_price=0,$area_type=1){
        $where['c.status']      = array('eq',1);
        $where['c.area_type']   = array('eq',$area_type);
        $where['r.end_time']    = array('gt',time());
        $where['r.uid']         = array('eq',$uid);
        $where['r.is_use']      = array('eq',0);
        $where['r.user_status'] = array('eq',1);
        $couponRes = db('coupon c')->join('coupon_recode r','r.coupon_id=c.id')->where($where)->field('c.*,r.id as recode_id')->order('r.id desc')->select();
        if(empty($couponRes)){
            return array('code'=>201,'msg'=>'没有可用的优惠券');
        }

        foreach ($couponRes as $ke=>$ve){
             $couponPrice = model('Coupon')->dikou_price($ve,$total_price);
             $couponRes[$ke]['newTotalPrice'] = $couponPrice['total_price'];
             $couponRes[$ke]['dikouPrice']    = $couponPrice['dikou_price'];
        }

        array_multisort(array_column($couponRes,'dikouPrice'),SORT_DESC,$couponRes); //把优惠券力度最大展示在最前面
        return array('code'=>200,'msg'=>'成功','data'=>$couponRes);
    }


    /**
     *计算抵扣价格
     * $coupon:优惠券信息
     */
    public function dikou_price($coupon,$total_price=0){
        if($coupon['type']==1){
            //现金优惠券
            $res['total_price']  = $coupon['val']<=$total_price ? $total_price-$coupon['val'] : 0;
            $res['dikou_price']  = $coupon['val']; //抵扣的金额
        }else if($coupon['type']==2){
            //满折优惠券
            $res['total_price']  = round(($total_price*$coupon['val']),2);
            $res['dikou_price'] = $total_price-$res['total_price']; //抵扣的金额
        }else if($coupon['type']==3){
            //满减优惠券
            $res['total_price']  = $total_price-$coupon['val'];
            $res['dikou_price']  = $coupon['val']; //抵扣的金额
        }else{
            $res['total_price']  = $total_price;
            $res['dikou_price']  = 0;//抵扣的金额
        }
        return $res;
    }


    /**
     *我的优惠券数量
     */
    public function coupon_num($uid){
        $where['r.uid']        = array('eq',$uid);
        $where['r.is_use']     = array('eq',0);
        $where['c.start_time'] = array('elt',time());
        $where['c.end_time']   = array('gt',time());
        $count = db('coupon c')->join('coupon_recode r','r.coupon_id = c.id')->where($where)->count();
        return $count;
    }


    /**
    *判断优惠券是否能使用
     */
    public function canuse_coupon($uid=0,$coupon_id=0,$total_price=0,$area_type=0){
        $where['r.uid']       = array('eq',$uid);
        $where['r.is_use']    = array('eq',0);
        $where['r.coupon_id'] = array('eq',$coupon_id);
        $where['c.area_type'] = array('eq',$area_type);
        $vot = db('coupon c')->join('coupon_recode r','c.id = r.coupon_id')->where($where)->field('c.*,r.end_time as r_end_time')->find();
        if(empty($vot)){
            return ['code'=>201,'msg'=>'优惠券不存在或已被使用'];
        }
        if($vot['r_end_time']<=time()){
            return ['code'=>201,'msg'=>'优惠券已过期'];
        }
        $res = $this->dikou_price($vot,$total_price);
        return ['code'=>200,'msg'=>'成功','data'=>$res];
    }


    /**
    *获取抵扣余额
     */
    public function use_balance($balance=0,$total_price=0){
        if($balance>=0){
            if($balance<$total_price){
                $yue_dikou_price = $balance;
            }else{
                $yue_dikou_price = $total_price;
            }
        }else{
            $yue_dikou_price = 0;
        }
        return $yue_dikou_price;
    }


}
