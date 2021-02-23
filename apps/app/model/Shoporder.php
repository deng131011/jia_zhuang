<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Comment as CommentModel;
class Shoporder extends Model {


    /**
     *商品订单列表
     * type:1已预约，2待评价，3已评价，4已过期
     * $count='count' 时查询数量
     * $is_pc:1返回条件
     **/
    public function order_list($post,$count=''){
        $post['type'] = !empty($post['type']) ? $post['type'] :1;
        if(!empty($post['keywords'])){
            $where['o.order_user|o.order_mobile']      = array('like','%'.trim($post['keywords']).'%');
        }
        $ss = time()-3600;
        if($post['type']==1){
            $where['o.hexiao_status']      = array('eq',0);
            $where['o.order_date']         = array('gt',mydate($ss,2));
        }else if($post['type']==2){
            if($post['saoma_type']!=2){
                $where['o.is_comment']         = array('eq',0);
            }
            //用户端获取已完成的列表
            $where['o.hexiao_status']      = array('eq',1);

        }else if($post['type']==3){
            $where['o.hexiao_status']      = array('eq',1);
            $where['o.is_comment']         = array('eq',1);
        }else if($post['type']==4){
            $where['o.hexiao_status']      = array('eq',0);
            $where['o.order_date']         = array('elt',mydate($ss,2));
        }

        if($post['saoma_type']==1){
            //扫码获取订单
            $where['o.uid']          = array('eq',$post['m_uid']); //买家用户id
            $where['o.hexiao_status']      = array('eq',0);
        }else if($post['saoma_type']==2){
            //用户个人中心预约单
            $where['o.uid']          = array('eq',$post['uid']); //用户id
            $where['o.user_status']  = array('eq',1);
        }else{
            //商家端订单
            $where['s.uid']          = array('eq',$post['uid']); //商家用户id
            $where['o.status']       = array('eq',1);
            if($post['need_order']==1){
                $where['o.order_type']   = array('eq',1); //需预约的
            }else if($post['need_order']==2){
                $where['o.order_type']   = array('eq',2); //不需预约的
            }
        }

        if($count=='count'){
            $listCount = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->count();
            return $listCount; //返回数量
        }

        $num = 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;
        $list = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->order('o.id desc')->field('o.*,s.icon as shop_imgs,s.title as shop_title')->limit($page,$num)->select();

        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->foreach_shop_order($ve);
        }
        return $list;
    }

    public function foreach_shop_order($ve){
        $ve['create_date']    = mydate($ve['create_time']);
        $ve['shop_imgs']      = get_image($ve['shop_imgs']);

        if((strtotime($ve['order_date'])+3600)<=time()){
            $ve['out_status'] = 1; //已过期
        }else{
            $ve['out_status'] = 2; //未过期
        }
        return $ve;
    }



    /**
     *商家订单详情
     **/
    public function order_details($post){
        if(!empty($post['hexiao_code'])){
            //预约号查询是否有单
            $where['o.hexiao_code'] = array('eq',trim($post['hexiao_code']));
        }else{
            $where['o.id'] = array('eq',$post['order_id']);
        }
        $where['s.uid']      = array('eq',$post['uid']);

        $order = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->field('o.*,s.icon as shop_imgs,s.title as shop_title')->find();
        if(!empty($post['hexiao_code'])){
            if(empty($order)){
               return array('code'=>201,'msg'=>'预约单不存在');
            }
            if($order['hexiao_status']==1){
                return array('code'=>201,'msg'=>'该预约号已被核销');
            }
            if((strtotime($order['order_date'])+3600)<=time()){
                return array('code'=>201,'msg'=>'该预约已过期，请重新预约');
            }
        }

        $order = $this->foreach_shop_order($order);
        $result['order_info'] = $order;

        if($order['is_comment']==1){

            //评价信息
            $wheren['return_id'] = array('eq',$order['shop_id']);
            $wheren['order_id'] = array('eq',$order['id']);
            $wheren['type']     = array('eq',2);

            $vos = db('comment')->where($wheren)->find();

            if(!empty($vos)){
                $vos['imgarr'] = imgArr($vos['imgarr']);
            }

            $result['comment'] = $vos;
        }
        //商品
        if($order['hexiao_status']==0){
            $map['l.order_id']       = array('eq',$order['id']);
            $sonlist = db('shop_order_list l')->where($map)->select();
            foreach ($sonlist as $ks=>$vs){
                $sonlist[$ks]['info_json']  = json_decode($vs['info_json'],true);
            }
            $result['product_list'] = $sonlist;
        }

        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }


    /**
     *提交核销
     **/
    public function sure_hexiao($post){
        $where['o.id'] = array('eq',$post['order_id']);
        $where['s.uid']      = array('eq',$post['uid']);
        $order = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->field('o.*,s.icon as shop_imgs')->find();
        if(empty($order)){
            return array('code'=>201,'msg'=>'预约单不存在');
        }
        if($order['hexiao_status']==1){
            return array('code'=>201,'msg'=>'该预约号已被核销，请勿重复核销');
        }
        if((strtotime($order['order_date'])+3600)<=time()){
            return array('code'=>201,'msg'=>'该预约单已过期，不能提交核销');
        }
        $data['hexiao_time'] = time();
        $data['hexiao_status'] = 1;
        $res = db('shop_order o')->where('id',$order['id'])->update($data);
        if($res){
            return array('code'=>200,'msg'=>'核销成功');
        }else{
            return array('code'=>201,'msg'=>'核销失败');
        }
    }


    /**
     *提交金额折算记录
     */
    public function add_price_recode($post){
       $data['total_price']   = floatval($post['total_price']);
       $data['zhe_kou_price'] = $post['zhe_kou']>0 ? round(($data['total_price']*($post['zhe_kou']/10)),2) : $data['total_price'];
       $data['pay_price']     = floatval($post['pay_price']);
        $res = db('shop_order')->where('id',$post['order_id'])->update($data);
        if($res){
            return array('code'=>200,'msg'=>'提交成功');
        }else{
            return array('code'=>201,'msg'=>'提交失败');
        }

    }


    /**
     *消费记录
     * uid:商家自己，m_uid：其他用户
     */
    public function sale_recode($post,$count=''){
        $where['o.uid'] = array('eq',$post['m_uid']);
        $where['o.status'] = array('eq',1);
        $where['s.uid'] = array('eq',$post['uid']);
        if($count=='count'){
            $listcount = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->count();
            return $listcount;
        }

        $num = 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;
        $list = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->field('o.*')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['hexiao_time'] = mydate($ve['hexiao_time'],2);
        }
        return $list;
    }


    /**
     *删除消费记录
     * uid:商家自己
     */
    public function del_recode($post){
        $where['o.id']  = array('eq',$post['order_id']);
        if($post['user_type']==1){
            $where['o.uid'] = array('eq',$post['uid']);
            $data['user_status'] = 2;
        }else if($post['user_type']==2){
            $where['s.uid'] = array('eq',$post['uid']);
            $data['status'] = 2;
        }

        $vo = db('shop_order o')->join('shop s','s.id = o.shop_id')->where($where)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'订单不存在');
        }
        $res = db('shop_order')->where('id',$post['order_id'])->update($data);
        if($res){
            return array('code'=>200,'msg'=>'删除成功');
        }else{
            return array('code'=>201,'msg'=>'删除失败');
        }
    }


    /**
     *扫码添加不需要预约的订单
     * uid:商家自己，m_uid:扫码出来的用户id
     */
    public function saoma_addorder($post){
        $maps['uid'] = array('eq',$post['uid']);
        $shop = db('shop')->where($maps)->find();
        if(empty($shop)){
            return array('code'=>201,'msg'=>'请联系管理员绑定商家');exit;
        }
        if($shop['status']!=1){
            return array('code'=>201,'msg'=>'该商家已被管理员禁用');exit;
        }
        //添加订单
        $data['order_number']        = order_number('shop_order');
        $data['hexiao_code']         = hexiao_code('shop_order',$shop['id']);
        $data['uid']                 = $post['m_uid'];
        $data['shop_id']             = $shop['id'];
        $data['order_type']          = 2;
        $data['zhe_kou']             = floatval($shop['zhe_kou']); //折扣
        $data['create_time']         = time();
        $data['hexiao_status']       = 1;
        $data['hexiao_time']         = time();
        $res = db('shop_order')->insertGetId($data);
        if($res){
            return array('code'=>200,'msg'=>'核销成功');
        }else{
            return array('code'=>201,'msg'=>'核销失败');
        }
    }



}
