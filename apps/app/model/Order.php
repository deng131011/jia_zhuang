<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Product as ProductModel;
use app\app\model\Score as ScoreModel;
use app\app\model\Coupon as CouponModel;
class Order extends Model {

    /**
     *收货地址信息
     */
    public function address_info($address_id=0,$where=array()){
        $data['id']          = array('eq',$address_id);
        $vo = db('address')->where($data)->find();
        /*$vo['province'] = modelField($vo['province_id'],'china','title');
        $vo['city']     = modelField($vo['city_id'],'china','title');
        $vo['county']   = modelField($vo['county_id'],'china','title');
        $vo['xx_address'] = $vo['province'].$vo['city'].$vo['county'].$vo['address'];*/
        $vo = $this->addressInfo($vo);

        return $vo;
    }

    public function addressInfo($vo){
        if(!empty($vo)){
            $vo['province'] = modelField($vo['province_id'],'china','title');
            $vo['city']     = modelField($vo['city_id'],'china','title');
            $vo['county']   = modelField($vo['county_id'],'china','title');
            $vo['xx_address'] = $vo['province'].$vo['city'].$vo['county'].$vo['address'];
            $vo['send_price'] = modelField($vo['city_id'],'china','send_price');
        }
        return $vo;
    }

    /**
     *运费
     */
    public function send_price($address,$price=0){
        $where['level'] = array('eq',2);
        $where['id']    = array('eq',$address['city_id']);
        $vo = db('china')->where($where)->find();

        $send_price = 0;
        if(empty($vo['manzu_price']) || $vo['manzu_price']<=0){
            $send_price = $vo['send_price'];

        }else{
            if($price<$vo['manzu_price']){
                $send_price = $vo['send_price'];
            }
        }
        return floatval($send_price);
    }




    /**
     *更新用户表
     * $data：参数
     */
    public function changeUserInfo($data=array()){
        $data['update_time'] = mydate();
        $res = Db::table('qyc_usermember')->update($data);
        return $res;
    }



    /**
     *订单结算页面信息
     * enter_type:1直接购买进入结算，2购物车选择进入结算
     */
    public function order_info($post,$userinfo=[]){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'缺少用户参数！');
        }
        if($post['enter_type']!=1 && $post['enter_type']!=2){
            return array('code'=>201,'msg'=>'非法操作！');
        }

        if($post['enter_type']==1 ){
            if(empty($post['product_id'])|| empty($post['num'])){
                return array('code'=>201,'msg'=>'请选择购买的商品！');
            }
            if(empty($post['main_gg_id']) || empty($post['spec_id'])){
                return array('code'=>201,'msg'=>'请选择规格！');
            }
        }else if($post['enter_type']==2 && empty($post['car_id']) && empty($post['product_id'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }

        Db::startTrans();
        try{
            $ScoreModel = new ScoreModel();
            if($post['enter_type']==1){

                $arr = [
                    ['product_id'=>$post['product_id'],'num'=>$post['num'],'main_gg_id'=>$post['main_gg_id'],'spec_id'=>$post['spec_id']]
                ];

                $res  = $ScoreModel->buy_product_list($arr);

                if($res['code']==201){
                    Db::rollback();
                    return $res;
                }
            }else if($post['enter_type']==2){

                $res  = $ScoreModel->buycar_product_list($post);

                if($res['code']==201){
                    Db::rollback();
                    return $res;
                }
            }

            $list = $res['data'];

            $checkRes = $this->oldTotalPrice($list,$userinfo);

            Db::commit();
            return $checkRes;
        }catch (\Exception $e){
            Db::rollback();
            return ['code'=>201,'msg'=>'获取数据失败'];
        }

    }

    /**
     *计算抵扣前的总价
     */
    public function oldTotalPrice($list,$userinfo){
        $total_price = 0;

        foreach ($list as $ke=>$ve){

            $zx_new_price = product_price($ve,$userinfo);//根据用户身份判断单价
            $list[$ke]['zz_new_price'] = $zx_new_price; //根据各种情况判断后的最终单价

            $onesum_price = round(($zx_new_price*$ve['num']),2); //普通用户单个商品总价
            $list[$ke]['onesum_price'] = two_xiaoshu($onesum_price); //单个商品总价
            $total_price += $onesum_price;
        }
        $result['total_price'] = $total_price; //优惠前的总价
        $result['list'] = $list;
        return $result;
    }

    /**
    *默认地址
     */
    public function default_address($post){
        //默认地址
        $map['uid']        = array('eq',$post['uid']);
        $map['is_default'] = array('eq',1);
        $default = db('address')->where($map)->find();
        if(!empty($default)){
            $default['pro_zone'] = provinceCityCounty($default,'zone_title');
            $default['xx_address'] = $default['pro_zone'].$default['address'];
        }
        return $default;
    }




    /**
     *添加订单主表
     */
    public function orderAddForm($arr=array()){

        $data['order_number']   = order_number('order');

        $data['uid']            = $arr['uid'];

        $data['order_user']     = $arr['address_json']['username'];
        $data['order_mobile']   = $arr['address_json']['mobile'];
        $data['address']        = $arr['address_json']['xx_address'];

        $data['order_type']     = $arr['order_type'];
        $data['total_price']       = $arr['total_price'];
        $data['coupon_id']         = !empty($arr['coupon_id']) ? $arr['coupon_id'] : 0;
        $data['coupon_price']      = $arr['coupon_price'];
        $data['send_price']        = $this->send_price($arr['address_json'],$arr['total_price']); //运费

        $payprice = $arr['total_price']-$data['coupon_price']+floatval($data['send_price']);
        $data['pay_price']      = floatval($payprice)>0 ? floatval($payprice) :0; //实际支付的金额

        $data['remark']         = !empty($arr['remark']) ? $arr['remark'] : '';
        $data['pay_status']     = !empty($arr['pay_status']) ? $arr['pay_status'] : 0;
        $data['pay_type']       = !empty($arr['pay_type']) ? $arr['pay_type'] : 0;
        $data['pay_time']       = !empty($arr['pay_time']) ? $arr['pay_time'] : 0;
        $data['create_time']    = time();
        $res = Db::table('qyc_order')->insertGetId($data);
        return $res;
    }


    /**
     *增加订单商品列表
     * $data：参数
     */
    public function addOrderList($list=array(),$order_id=0,$new_total_price=0,$coupon_price=0){
        $ii = 0;
        foreach ($list as $ke=>$ve){
            unset($ve['content']);
            $data[$ke]['order_id']   = $order_id;
            $data[$ke]['product_id'] = $ve['id'];
            $data[$ke]['main_gg_id'] = $ve['main_gg_id'];
            $data[$ke]['spec_id']    = $ve['spec_id'];
            $data[$ke]['gg_title']   = json_encode($ve['gg_info'],JSON_UNESCAPED_UNICODE);
            $data[$ke]['info_json']  = json_encode($ve,JSON_UNESCAPED_UNICODE);
            $data[$ke]['num']        = $ve['num'];
            $data[$ke]['old_price']  = floatval($ve['new_price']);
            $data[$ke]['new_price']  = floatval($ve['zz_new_price']);

            if($coupon_price>0){
                $coupon_pricet = ($coupon_price/$new_total_price)*floatval($ve['onesum_price']);
            }
            $data[$ke]['coupon_price']  = !empty($coupon_pricet) ? round($coupon_pricet,2) : 0;
            $data[$ke]['pay_price']  = $ve['onesum_price']-$data[$ke]['coupon_price'];
            //冻结库存
            /*$dataer[$ii]['freeze_stock'] = $ve['freeze_stock']+$ve['num'];
            $dataer[$ii]['stock']        = $ve['stock']-$ve['num'];
            $dataer[$ii]['id']           = $ve['id'];
            $ii++;*/
        }

        $res = Db::table('qyc_order_list')->insertAll($data);

       /* if(!empty($dataer)){
            $Product = new ProductModel();
            $res2 = $Product->saveAll($dataer);
        }*/
        return $res;
    }

    /**
     *添加普通订单
     */
    public function add_order($post,$userinfo=[]){
        if(empty($post['product_info']) || empty($post['token'])  || empty($post['uid']) || empty($post['enter_type'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }
        if(empty($post['address_json'])){
            return array('code'=>201,'msg'=>'请选择收货地址！');
        }

        Db::startTrans(); //开启事务
        try{

            $ScoreModel = new ScoreModel();
            $res = $ScoreModel->buy_product_list($post['product_info']);
            if($res['code']==201){
                Db::rollback();
                return $res;
            }

            $product_list = $res['data'];//商品列表
            $listRes = $this->oldTotalPrice($product_list,$userinfo);//解析价格
            $new_total_price = $listRes['total_price'];//商品总金额


            //判断优惠券抵扣金额
            $CouponModel = new CouponModel();
            $coupon_price = 0;
            if(!empty($post['coupon_id'])){

                $coupon_res = $CouponModel->canuse_coupon($post['uid'],$post['coupon_id'],$new_total_price,1);

                if($coupon_res['code']==201){
                    Db::rollback();
                    return $coupon_res;
                }
                $coupon_price = $coupon_res['data']['dikou_price'];
            }

            $product_list    = $listRes['list'];

            //订单主表
            $post['total_price']    = $new_total_price;
            $post['coupon_price']   = $coupon_price;
            $post['order_type']     = 1;
            $res1 = $this->orderAddForm($post);

            //添加订单商品列表
            $res2 = $this->addOrderList($product_list,$res1,$new_total_price,$coupon_price);

            //修改优惠券为已使用
            if($post['coupon_id']>0 && $coupon_price>0){
                $whe['uid']       = array('eq',$post['uid']);
                $whe['coupon_id'] = array('eq',$post['coupon_id']);
                $whe['is_use']    = array('eq',0);
                $resty = db('coupon_recode')->where($whe)->update(array('is_use'=>1,'use_time'=>mydate()));
            }

            //减掉余额



            //删除购物车数据
            if($post['enter_type']==2){
                $car_id = implode(',',array_column($product_list,'car_id'));
                $map['uid'] = array('eq',$post['uid']);
                $map['id']  = array('in',$car_id);
                db('car')->where($map)->delete();
            }

            Db::commit();
            $order = db('order')->field('order_number,id as order_id,uid,pay_price')->find($res1); //返回订单号
			$zfend_time = config('order_pay_out_time');
            return array('code'=>200,'msg'=>'提交成功！','data'=>$order,'zfend_time'=>$zfend_time);
        }catch (\Exception $e) {
            Db::rollback();
            return array('code'=>201,'msg'=>'提交失败！');
        }
    }


    /**
     *物流详情
     */
    public function logistics_info($order_id){

        $where['o.order_id'] = array('eq',$order_id);
        $vo = db('logistics_company c')->join('order_logistics o','o.logistics_id=c.id')->where($where)->field('c.*,o.kuaidi_number')->find(); //快递公司信息

        $data = model('Logistics')->logisticsApi($vo['code'],$vo['kuaidi_number']);
        $vo['wuliu'] = $data;
        return array('code'=>200,'msg'=>'成功','data'=>$vo);
    }




}
