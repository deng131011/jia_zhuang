<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Product as ProductModel;
use app\app\model\Logistics as LogisticsModel;
class Myorder extends Model {





    /**
    *商品订单数量
     **/
    public function order_count($post){
        $result['all_count'] = $this->order_list(['type'=>'','uid'=>$post['uid']],'count');
        $result['dzf_count'] = $this->order_list(['type'=>1,'uid'=>$post['uid']],'count');
        $result['dfh_count'] = $this->order_list(['type'=>2,'uid'=>$post['uid']],'count');
        $result['dsh_count'] = $this->order_list(['type'=>3,'uid'=>$post['uid']],'count');
        $result['dpj_count'] = $this->order_list(['type'=>4,'uid'=>$post['uid']],'count');
        $result['shsp_count'] = $this->shproduct_list(['uid'=>$post['uid']],'count');
        return $result;
    }



    /**
     *订单列表
     * type:1待支付，2待发货，3待收货，4待评价
     * $count='count' 时查询数量
     **/
    public function order_list($post,$count='',$is_pc = ''){
        $post['type'] = !empty($post['type']) ? $post['type'] : '';
		if(!empty($post['keywords'])){
			 $where['o.order_number|l.product_title']      = array('like','%'.trim($post['keywords']).'%');
		}
		$out_time = config('order_pay_out_time');
        if($post['type']==1){
            $where['o.pay_status']         = array('eq',0);
            $where['o.create_time']        = array('gt',(time()-$out_time));
        }else if($post['type']==2){
            $where['o.pay_status']         = array('eq',1);
            $where['o.order_status']         = array('eq',0);
        }else if($post['type']==3){
            $where['o.order_status']         = array('eq',1);
        }else if($post['type']==4){
            $where['o.order_status']         = array('eq',2);
        }
		
		if(!empty($post['type'])){
			$where['o.user_status']  = array('eq',1);
            $where['l.after_status'] = array('eq',0);
		}else{
            $where['o.user_status']  = array('egt',1);
        }
        $where['o.uid']          = array('eq',$post['uid']);
        $where['o.status']       = array('eq',1);

        if($is_pc==1){
            return $where; //pc端请求条件
        }

        if($count=='count'){
            $listCount = db('order o')->join('order_list l','l.order_id = o.id')->group('o.id')->where($where)->count();
            return $listCount; //返回数量
        }

        $num = 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;
        $list = db('order o')->join('order_list l','l.order_id = o.id')->where($where)->order('o.id desc')->field('o.*')->group('o.id')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
             $list[$ke] = $this->foreach_orderlist($ve);
        }
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }

    public function foreach_orderlist($ve){
        $ve['create_date'] = mydate($ve['create_time']);
        $ve['end_date']    = mydate($ve['end_time']);
        $ve['pay_date']    = mydate($ve['pay_time']);
        $ve['order_status_info'] = orderStatus($ve); //订单状态

        $zfend_time = $ve['create_time']+config('order_pay_out_time')-time();//待支付订单倒计时
        $zfend_time = $zfend_time>0 ? $zfend_time : 0;
        $ve['zfend_time']  = $zfend_time;//待支付订单倒计时;

        $map['l.order_id'] = array('eq',$ve['id']);
        $sonlist = db('order_list l')->where($map)->select();
        foreach ($sonlist as $ks=>$vs){
            $sonlist[$ks]['info_json']     = json_decode($vs['info_json'],true);
            $sonlist[$ks]['son_buyafter_info'] = buyafter_status($vs['after_status']);
        }
        $ve['son_list'] = $sonlist;
        return $ve;
    }



    /**
     *普通订单详情
     **/
    public function orderDetails($post){

        $where['uid']      = array('eq',$post['uid']);
        $where['id'] = array('eq',$post['order_id']);
        $order = db('order')->where($where)->find();
        $order['pay_type_title'] = payTypeTitle($order['pay_type']);
        $order['create_date'] = mydate($order['create_time']);
        $order['pay_time'] = !empty($order['pay_time']) ? mydate($order['pay_time']) : '';
        $order['close_time'] = !empty($order['close_time']) ? mydate($order['close_time']) : '';
        $order['end_time'] = !empty($order['end_time']) ? mydate($order['end_time']) : '';
        $order['order_status_info'] = orderStatus($order); //状态


        //查看物流信息
        if(!empty($order['wuliu_code']) && !empty($order['wuliu_number'])){
            $wldata = model('Logistics')->logisticsApi($order['wuliu_code'],$order['wuliu_number']);
            $order['wuliu_info'] = $wldata;
        }else{
            $order['wuliu_info'] = '';
        }


        //商品
        $map['l.order_id']       = array('eq',$order['id']);
        $sonlist = db('order_list l')->where($map)->select();
        foreach ($sonlist as $ks=>$vs){
            $sonlist[$ks]['info_json']  = json_decode($vs['info_json'],true);
            $sonlist[$ks]['son_buyafter_info'] = buyafter_status($vs['after_status']);
        }
        $order['product_list'] = $sonlist;
		$zfend_time = $order['create_time']+config('order_pay_out_time')-time();//待支付订单倒计时
        $zfend_time = $zfend_time>0 ? $zfend_time : 0;
		
		$order['zfend_time'] = $zfend_time;
		
        return array('code'=>200,'msg'=>'成功','data'=>$order);
    }




    /**
     *售后商品列表
     */
    public function shproduct_list($post,$count=''){
        $map['b.uid']            = array('eq',$post['uid']);
        $map['b.status']         = array('eq',1);
        $map['b.type']           = array('eq',1);
       // $map['b.chuli_status']   = array('neq',3);

        if($count=='count'){
            $listcount = db('buyafter b')->join('order_list o','o.id=b.order_listid')->where($map)->count();
            return $listcount;
        }

        $num = 20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = db('buyafter b')->join('order_list o','o.id=b.order_listid')->where($map)->field('b.*,o.pay_price,o.info_json,o.new_price')->order('b.id desc')->limit($page,$num)->select();
        foreach ($list as $ks=>$vs) {
            $list[$ks]['info_json']    = json_decode($vs['info_json'],true);
            $list[$ks]['create_date']  = mydate($vs['create_time']);
            $list[$ks]['after_info']   = buyafter_status($vs['chuli_status']);
            $list[$ks]['after_status'] = afterStatus($vs);
        }
        return array('code'=>200,'msg'=>'成功','data'=>$list);
    }


    /**
     *物流详情
     */
    public function logistics_info($order_id,$order_number){
        $where['id'] = array('eq',$order_id);
        $where['order_number'] = array('eq',$order_number);
        $vo = db('order')->where($where)->find(); //快递公司信息
        if(empty($vo['wuliu_code']) || empty($vo['wuliu_number'])){
            return array('code'=>201,'msg'=>'暂无物流信息');
        }
        $LogisticsModel = new LogisticsModel();

        $data = $LogisticsModel->logisticsApi($vo['wuliu_code'],$vo['wuliu_number']);
        if(empty($data)){
            return array('code'=>201,'msg'=>'暂无物流信息');
        }

        $maps['code'] = array('eq',trim($vo['wuliu_code']));
        $gsvo = db('logistics_company')->where($maps)->find();

        $result['order'] =  $vo;
        $result['wuliu_company'] = !empty($gsvo['title']) ? $gsvo['title'] : '';
        $result['wuliu_info'] = $data;
        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }



    /**
     *取消订单
     **/
    public function deleteOrder($post){
        if(empty($post['uid']) || empty($post['order_id'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }

            $where['uid']  = array('eq',$post['uid']);
            $where['id']   = array('eq',$post['order_id']);
            $order = db('order')->where($where)->find();
            if($order['pay_status']==0){

                Db::startTrans();
                try{
                    $res = db('order')->where($where)->update(['user_status'=>2,'close_time'=>time()]);

                    //释放冻结库存
                    $wherepo['l.order_id'] = array('eq',$order['id']);
                    $list = db('order_list l')->join('product g','g.id = l.product_id')->where($wherepo)->field('l.num,g.id as product_id,g.sale_num as gg_sale_num,g.stock,g.freeze_stock')->select();

                    foreach($list as $ke=>$ve){
                        $datall[$ke]['id']              = $ve['product_id'];
                        $datall[$ke]['freeze_stock']    = $ve['freeze_stock']-$ve['num'];
                        $datall[$ke]['stock']           = $ve['stock']+$ve['num'];
                    }

                    if(!empty($datall)){
                        $ProductModel = new ProductModel();
                        $res2 = $ProductModel->saveAll($datall);
                    }

                    //添加操作记录
                    order_use_recode($post['order_id'],$post['uid'],1,4,'用户取消了订单：'.$order['order_number']);
                    Db::commit();
                    return array('code'=>200,'msg'=>'取消成功');
                }catch (\Exception $e){
                    Db::rollback();
                    return array('code'=>201,'msg'=>'取消失败');
                }
            }else{
                return array('code'=>201,'msg'=>'该订单不符合取消条件');
            }

    }
	
	
	/**
     *删除商品订单
     **/
    public function delOrder($post){

        $where['uid']  = array('eq',$post['uid']);
        $where['id']   = array('eq',$post['order_id']);
        $order = db('order')->where($where)->find();
        if($order['pay_status']==0){
            $res = db('order')->where($where)->delete();
        }else if($order['order_status']==3){
            $res = db('order')->where($where)->update(['user_status'=>-1,'update_time'=>time()]);
        }else{
            return array('code'=>201,'msg'=>'该订单不符合删除条件');
        }
        if($res){
            return array('code'=>200,'msg'=>'删除成功');
        }else{
            return array('code'=>201,'msg'=>'删除失败');
        }
    }
	
	
	
	

    /**
     *确认收货
     */
    public function sure_accept($post){
        Db::startTrans();
        try{
            $maps['o.id']     = array('eq',$post['order_id']);
            $maps['l.after_status'] = array('in','1,2,4');
            $vos_count = db('order o')->join('order_list l','o.id = l.order_id')->where($maps)->count();
            if(!empty($vos_count) && $vos_count>0){
                Db::rollback();
                return array('code'=>201,'msg'=>'您当前有商品存在退款中，暂不能确认收货！');exit;
            }
            $map['id'] = array('eq',$post['order_id']);
            $map['uid'] = array('eq',$post['uid']);
            $res = db('order')->where($map)->update(array('order_status'=>2,'end_time'=>time()));

            //添加操作记录
            order_use_recode($post['order_id'],$post['uid'],1,2,'用户确认收货');
            Db::commit();
            return array('code'=>200,'msg'=>'操作成功！');
        }catch (\Exception $e){
            Db::rollback();
            return array('code'=>201,'msg'=>'操作失败！');
        }
    }







}
