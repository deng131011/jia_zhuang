<?php

namespace App\Model;

/**
 * 订单操作模型
 */
class OrderModel {

    //下单时的订单信息,down_type:1直接下单，2购物车中提交
    public function order_info($post){
        if($post['down_type']==1){
            //购物车中提交过来的数据
             if(empty($post['car_id']) ){
                return array('code'=>201,'status'=>false,'msg'=>'缺少参数！');
             }
             $where['s.uid']    = array('eq',$post['uid']);
             $where['s.id']     = array('in',$post['car_id']);
             $where['s.status'] = array('eq',1);
             $where['p.status'] = array('eq',1);
             $list = M('shop_car as s')->join('bhy_product as p on p.id = s.product_id')->where($where)->field('p.*,s.id as car_id,s.order_date,s.order_dateb,s.attrs_id,s.num,s.between_time,s.uid')->select();
        }else{
            //直接购买的
            if(empty($post['id'])){
                return array('code'=>201,'status'=>false,'msg'=>'缺少参数！');
            }
            $where['id'] = array('eq',$post['id']);
            $list = M('product')->where($where)->select();
            foreach ($list as $ks=>$vs){
                $list[$ks]['attrs_id']   = $post['attrs_id'];
                $list[$ks]['order_date'] = $post['order_date']!='' && $post['order_date']!=0 ? strtotime($post['order_date']) : 0;
                $list[$ks]['order_dateb'] = $post['order_dateb']!='' && $post['order_dateb']!=0 ? strtotime($post['order_dateb']) : 0;
                $list[$ks]['num']        = $post['num'];
                $list[$ks]['between_time']  = $post['between_time'];
            }
        }
        //循环过滤或赋值
        foreach ($list as $ke=>$ve){
            $vot = $this->can_buy($ve); //过滤不可以买的
            if($vot['code']==202){
                unset($list[$ke]);
            }else{
                $list[$ke]['imgurl'] = picture($ve['icon']);
                $list[$ke]['attr_title'] = product_attr($ve['attrs_id'],'title');
                $list[$ke]['riqi_geshi'] = date('Y-m-d',$ve['order_date']);
                $payprice = $this->get_product_price($ve['id'],$ve['order_date'],$ve['old_price'],$ve['new_price'],$ve['order_dateb'],$ve['attrs_id']);//获取最新单价
                $list[$ke]['payprice'] = $payprice;
                if($ve['type1']==4){
                    $list[$ke]['sum_payprice'] = $payprice*$ve['num'];
                }else{
                    $list[$ke]['sum_payprice'] = $payprice;
                }

            }
        }
        if(empty($list)){
            return array('code'=>201,'status'=>false,'msg'=>'没有符合购买条件的产品！');
        }

        $shoparr = array_unique(array_column($list,'shop_id'));
        $shoplist = array();
        foreach ($shoparr as $k=>$v){
            $shop = M('shop')->find($v);
            $shop['imgurl'] = picture($shop['icon']);
            $shoplist[] = $shop;
            foreach ($list as $kk=>$vv){
                if($vv['shop_id'] == $v){
					if($vv['attrs_id'] > 0){
					
						$attr = M('product_attrs')->where(array('ggid'=>$vv['attrs_id']))->find();
						
						$vv['new_price'] = $attr['gg_price'];
						
					}
                    $shoplist[$k]['son_list'][] = $vv;
                }
            }
        }

        $data['list'] = $shoplist;  //订单商品列表
        $data['list_totalprice'] = array_sum(array_column($list,'sum_payprice'));
        $coupon = $this->big_coupon($post['uid'],$data['list_totalprice']);
        $data['youhui'] = $coupon['youhui_price'];
        $data['coupon_info'] = $coupon;

        //增值服务(派对服务)
        $zeng['city_id'] = array('eq',return_city_id($post['city_id']));
        $zeng['status']  = array('eq',1);
        $zeng['type1']   = array('eq',3);
        $zeng['zengzhi_type']   = array('eq',2);
        $paidui_fuwu = M('product')->where($zeng)->order('id desc')->select();
        foreach ($paidui_fuwu as $ks=>$vs){
            $paidui_fuwu[$ks]['images'] = picture($vs['icon']);
        }
        $data['paidui_fuwu'] = $paidui_fuwu;

        //增值服务(派对用品)
        $zeng['type1']   = array('eq',4);
        $paidui_yongpin = M('product')->where($zeng)->order('id desc')->select();
        foreach ($paidui_yongpin as $kt=>$vt){
            $paidui_yongpin[$kt]['images'] = picture($vt['icon']);
        }
        $data['paidui_yongpin'] = $paidui_yongpin;
        return array('code'=>200,'status'=>true,'data'=>$data);
    }

    //判断商品是否可以购买
    public function can_buy($ve){
        $where['o.pay_status']     = array('eq',1);
        $where['l.product_status'] = array('eq',1);
        $where['l.product_id']     = array('eq',$ve['id']);
        $where['l.attrs_id']     = array('eq',$ve['attrs_id']);
        if($ve['type1']==3){
            $dqdate = strtotime(date('Y-m-d'));
            if($ve['order_date']<$dqdate){
                return array('code'=>202,'msg'=>'预约日期不能小于当前日期！');
            }
            $where['l.order_date']     = array('eq',$ve['order_date']);
            $arr = M('order as o')->join('bhy_order_list as l on l.order_id = o.id')->where($where)->select();
            if($ve['big_val']<=count($arr)){
                return array('code'=>202,'msg'=>'当前日期预约已达上限，请另选择日期！');
            }else{
                return array('code'=>200,'msg'=>'可以预约！');
            }
        }else if($ve['type1']==1 || $ve['type1']==2){
            $dqdate = strtotime(date('Y-m-d'));
            if($ve['order_date']<$dqdate){
                return array('code'=>202,'msg'=>'预约日期不能小于当前日期！');
            }
            $where['l.order_time']     = array(array('egt',$ve['order_date']),array('elt',$ve['order_dateb']));
            $vo = M('order as o')->join('bhy_order_date as l on l.order_id = o.id')->where($where)->find();
            if(!empty($vo)){
                return array('code'=>202,'msg'=>'您选择的日期中已经有人预定了！');
            }else{
                return array('code'=>200,'msg'=>'可以预约！');
            }
        }else{
            return array('code'=>200,'msg'=>'可以购买！');
        }
    }

    //获取商品最新价格
    public function get_product_price($product_id,$date_time,$old_price,$new_price,$date_timeb,$attrs_id=0){
            if($attrs_id>0){
                 $attr = M('product_attrs')->where(array('ggid'=>$attrs_id))->find();
                 $payprice = $attr['gg_price'];
            }else{
                if($new_price<=0){
                    $payprice = $old_price;
                }else{
                    $payprice = $new_price<=$old_price?$new_price:$old_price;
                }
            }
          $payprice = $payprice>0 ? $payprice : 0.1; //支付价格为0时默认为1分
          $where['product_id'] = array('eq',$product_id);
          if($date_timeb>0 && $date_time>0){
              $day = $date_timeb-$date_time;
          }else{
              $day = 0;
          }

          if($day>0){
              $day = floor($day/86400);
              $paypri = 0;
              for($i=0;$i<=$day;$i++){
                  $datetime = $date_time+(86400*$i);
                 // $hh .=$date_time.'_'.(86400*$i).'-' ;
                  $where['date_time']  = array('eq',$datetime);
                  $vo = M('date_price')->where($where)->find();

                  if(!empty($vo)){
                      $pri = $vo['price'];
                  }else{
                      $pri = $payprice;
                  }
                  $paypri +=$pri;
              }
              return $paypri;
          }else{
              $where['date_time']  = array('eq',$date_time);
              $vo = M('date_price')->where($where)->find();
              if(!empty($vo)){
                  $payprice = $vo['price'];
              }
              return $payprice;
          }

    }


    //获取可用的优惠券
    public function can_use_coupon($uid,$total_price,$coupon_id){
        $dqtime = time();
        $where['c.uid']         = array('eq',$uid);
        $where['c.is_use']      = array('eq',0);
        $where['c.user_status'] = array('eq',1);
        $where['co.status']     = array('eq',1);
        $where['co.start_time'] = array('elt',$dqtime);
        $where['co.end_time']   = array('egt',$dqtime);
        if($total_price>0){
            $where['co.manzu']   = array('elt',$total_price);
        }
        if($coupon_id>0){
            $where['co.id']   = array('elt',$coupon_id);
        }
        $list = M('coupon as co')->join('bhy_coupon_numcode as c on c.coupon_id = co.id')->where($where)->field('co.*,c.id as recode_id')->order('co.id desc')->select();
		
        return $list;
    }
    //获取最大的优惠券
    public function big_coupon($uid,$total_price){
       $list = $this->can_use_coupon($uid,$total_price);

       if(!empty($list)){
           foreach ($list as $ke=>$ve){
               if($ve['type']==2){
                   $list[$ke]['youhui_price'] =$total_price-($total_price*$ve['val2']);
               }else if($ve['type']==3){
                   $list[$ke]['youhui_price'] =$ve['val3'] ;
               }else{
                   $list[$ke]['youhui_price'] = 0;
               }
           }

           array_multisort(array_column($list,'youhui_price'),SORT_DESC,$list);
           return $list[0];
       }else{
           return '';
       }

    }






    


    


    
    

}

?>
