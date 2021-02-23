<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Myorder as MyorderModel;
class Buyafter extends Model {

    /**
     *申请售后原因列表
     */
    public function apply_reason($post){
        if($post['type']==1){
            $where['pid']    = array('eq',8);
        }else if($post['type']==2){
            $where['pid']    = array('eq',61);
        }

        $where['status'] = array('eq',1);
        $list = db('category')->where($where)->field('id,title,sort')->order('sort asc')->select();
        return array('code'=>200,'msg'=>'成功！','data'=>$list);
    }

    /**
     *添加售后申请
     * apply_type:1仅退款，2退货退款
     */
    public function add_apply($post){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }
        if(empty($post['apply_type'])){
            return array('code'=>201,'msg'=>'请选择退款类型');
        }
        if(empty($post['reason_id'])){
            return array('code'=>201,'msg'=>'请选择退款原因');
        }
        if(empty($post['content'])){
            return array('code'=>201,'msg'=>'请填写退款说明');
        }

        if($post['type']==1){
            if( empty($post['order_id']) || empty($post['order_listid'])){
                return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
            }
            $res = $this->refund_product($post); //商品退款
        }else{
            return array('code'=>201,'msg'=>'非法操作');
        }

        return $res;
    }

    //商品退款
    public function refund_product($post){
        $mapt['o.pay_status']     = array('eq',1);
        $mapt['o.uid']            = array('eq',$post['uid']);
        $mapt['o.id']             = array('eq',$post['order_id']);
        $mapt['l.id']             = array('eq',$post['order_listid']);

        $order = db('order o')->join('order_list l','o.id = l.order_id')->field('l.*,l.id as order_listid,o.order_status')->where($mapt)->find();
        if(empty($order)){
            return array('code'=>201,'msg'=>'非法操作，订单不存在！');
        }

        Db::startTrans();
        try{
            $mapter['uid']          = array('eq',$post['uid']);
           // $mapter['status']       = array('eq',1);
            $mapter['type']         = array('eq',1);
            $mapter['order_id']     = array('eq',$post['order_id']);
            $mapter['order_listid'] = array('eq',$post['order_listid']);
            $vtp = db('buyafter')->where($mapter)->order('id desc')->find();

            if(!empty($vtp)){
                if($vtp['chuli_status']==3 || $vtp['status']==-2){


                }else{
                    Db::rollback();
                    return array('code'=>201,'msg'=>'您已经提交过申请，请勿重复提交！');
                }
            }

            $post['product_id']    = $order['product_id'];
            $post['type']          = 1;
            $post['num']           = $order['num'];
            $post['yue_price']     = floatval($order['yue_price']);
            $post['apply_price']   = $order['pay_price'];
            $post['create_time']   = time();
            if(!empty($vtp)){
                //再次提交
                $post['order_number']  = order_number('buyafter');
                $post['chuli_status']  = 1;
                $post['status']        = 1;
                $post['check_time']    = 0;
                $res = $this->allowField(true)->save($post,['id'=>$vtp['id']]);
                $refund_id = $vtp['id'];

                //删除前一次的时间记录
               
                $mpth['order_id'] = array('eq',$post['order_id']);
                $mpth['order_listid'] = array('eq',$post['order_listid']);
                $mpth['dx_type'] = array('eq',1);
                $mpth['type'] = array('in','5,6,7,10');
                db('order_recode')->where($mpth)->delete();

            }else{
                //添加
                $post['order_number']  = order_number('buyafter');
                $res = $this->allowField(true)->save($post);
                $refund_id = $this->id;
            }

            //更新订单副表
            $where['order_id']      = array('eq',$post['order_id']);
            $where['product_id']    = array('eq',$post['product_id']);
            $where['id']            = array('eq',$post['order_listid']);
            $res2 = db('order_list')->where($where)->update(['after_status'=>1]);

            //添加记录
            order_use_recode($post['order_id'],$post['uid'],1,5,'用户申请退款',$order['order_listid'],1);

            Db::commit();
            $resultt['refund_id'] = $refund_id;
            return array('code'=>200,'msg'=>'提交成功，待审核！','data'=>$resultt);
        }catch (\Exception $e) {
            Db::rollback();
            return array('code'=>201,'msg'=>'提交失败！');
        }
    }




    /**
     *退款详情
     */
    public function refund_details($post){
        if(empty($post['uid']) || empty($post['order_id'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }
        if($post['type']==1){
            if(empty($post['order_listid'])){
                return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
            }
        }

        $mapter['b.type']         = array('eq',$post['type']);
        $mapter['b.uid']          = array('eq',$post['uid']);
        $mapter['b.order_id']     = array('eq',$post['order_id']);
        if($post['type']==1){
            $mapter['b.order_listid'] = array('eq',$post['order_listid']);
        }
        $vo = db('buyafter b')->join('usermember u','b.uid = u.id')->where($mapter)->field('b.*,b.id as buyafter_id,u.nickname')->order('b.id desc')->find();

        if(empty($vo)){
            return array('code'=>201,'msg'=>'退款记录不存在！','data'=>$vo);
        }else{
            $vo['apply_type_title']        = $vo['apply_type']==1 ? '仅退款' : '退货退款';
            $vo['create_date']       = mydate($vo['create_time']);
            $vo['check_date']        = !empty($vo['check_time']) ? mydate($vo['check_time']) : '';
            $vo['imgarr']            = imgArr($vo['imgarr']);
            $vo['reason_type']       = !empty($vo['reason_id']) ? modelField($vo['reason_id'],'category','title') : '';
            $vo['check_reason']      =  $vo['reason'];
            $vo['after_status']      = afterStatus($vo) ;
            $result['details'] = $vo;


            //订单状态
            if($post['type']==1){
                $MyorderModel = new MyorderModel();
                $result['order_info'] = $MyorderModel->pcOrderDetails($post);
            }


            //退款记录
            $whereu['dx_type']      = array('eq',$vo['type']);
            $whereu['order_id']     = array('eq',$vo['order_id']);
            $whereu['type']         = array('in','5,6,7,8,9,10');
            if($vo['type']==1){
                $whereu['order_listid'] = array('eq',$vo['order_listid']);
            }
            $recode_list = db('order_recode')->where($whereu)->field('type,max(create_time) as create_time')->group('type')->select();
            $title = []; $tk_time = 0;
            foreach ($recode_list as $ke=>$ve){
                if($ve['type']==5){
                    $title = ['title'=>'申请退款时间'];
                }else if($ve['type']==6){
                    $title = ['title'=>'同意退款时间'];
                    $tk_time = $vo['create_time'];
                }else if($ve['type']==7){
                    $title =  ['title'=>'拒绝退款时间'];
                }else if($ve['type']==8){
                    $title = ['title'=>'寄回商品时间'];
                }else if($ve['type']==9){
                    $title = ['title'=>'平台打款时间'];
                }else if($ve['type']==10){
                    $title = ['title'=>'取消退款时间'];;
                }
                $recode_list[$ke]['time_title'] = $title;
                $recode_list[$ke]['create_time'] = mydate($ve['create_time'],2);
            }
            $jhsy_time = $tk_time>0 ? $tk_time+(86400*5)-time() : 0;
            $jhsy_time = $jhsy_time>0 ? $jhsy_time : 0;
            $result['jhsy_time'] = $jhsy_time; //寄回包裹剩余时间

            $result['time_list']  = $recode_list;

            //退货地址
            $result['addr']['address'] = config('WEB_ACCEPT_ADDRESS');
            $result['addr']['username'] = config('WEB_ACCEPT_PERSON');
            $result['addr']['mobile'] = config('WEB_ACCEPT_MOBILE');
            return array('code'=>200,'msg'=>'成功！','data'=>$result);
        }
    }


    /**
     *取消退款
     */
    public function return_refund($post){
        if(empty($post['uid']) || empty($post['token'])){
           return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }
        $mapter['uid']          = array('eq',$post['uid']);
        $mapter['id']           = array('eq',$post['buyafter_id']);
        $mapter['order_number'] = array('eq',$post['buyafter_number']);
        $vo = db('buyafter')->where($mapter)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'退款记录不存在！');
        }
        if($vo['chuli_status']>3){
            return array('code'=>201,'msg'=>'商品已寄出，无法取消退款！');
        }

        Db::startTrans();
        try{

            //更新申请表
            $res = db('buyafter')->where('id',$vo['id'])->update(['status'=>-2]);

            if($vo['type']==1){
                //更新订单副表 商品
                db('order_list')->where('id',$vo['order_listid'])->update(['after_status'=>0]);
            }


            //添加记录
            order_use_recode($vo['order_id'],$post['uid'],1,10,'用户取消退款',$vo['order_listid'],$vo['type']);

            Db::commit();
            return array('code'=>200,'msg'=>'取消成功！');
        }catch (\Exception $e){
            Db::rollback();
            return array('code'=>201,'msg'=>'取消失败！');
        }
    }


    /**
     *添加退货快递信息
     */
    public function add_kuaidi($post){
        if(empty($post['uid']) || empty($post['token']) || empty($post['buyafter_id'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试！');
        }
        if( empty($post['kuaidi_hao']) || empty($post['kuaidi_company'])){
            return array('code'=>201,'msg'=>'请填写快递公司和快递单号！');
        }

        Db::startTrans();
        try{

            $where['id']             = array('eq',$post['buyafter_id']);
            $where['uid']            = array('eq',$post['uid']);
            $where['order_number'] = array('eq',$post['buyafter_number']);
            $order = db('buyafter')->where($where)->find();

            if(empty($order)){
                return array('code'=>201,'msg'=>'没有找到您的申请记录！');
            }
            if(!empty($order['kuaidi_company']) || !empty($order['kuaidi_hao'])){
                return array('code'=>201,'msg'=>'您已提交过，请勿重复提交！');
            }

            $data1['kuaidi_hao']     = $post['kuaidi_hao'];
            $data1['kuaidi_company'] = $post['kuaidi_company'];
            $data1['chuli_status']   = 4;
            $res = db('buyafter')->where($where)->update($data1);

            $res = db('order_list')->where('id',$order['order_listid'])->update(array('after_status'=>4)); //更新状态

            //添加记录
            order_use_recode($order['order_id'],$post['uid'],1,8,'用户已寄回商品，物流单号：'.$post['kuaidi_hao'].'，物流公司：'.$post['kuaidi_company'],$order['order_listid'],$order['type']);

            Db::commit();
            return array('code'=>200,'msg'=>'提交成功！');
        }catch (\Exception $e){
            Db::rollback();
            return array('code'=>201,'msg'=>'提交失败！');
        }

    }


    /**
     *退货记录
     */
    public function afterRecode($buyafter_id=0,$type=0,$field=''){
        $map['buyafter_id'] = array('eq',$buyafter_id);
        $map['type']        = array('eq',$type);
        $vo = db('buyafter_recode')->where($map)->find();
        return !empty($field) ? $vo[$field] : $vo;
    }



}
