<?php

namespace app\app\model;
use think\Model;
use app\app\model\Order as OrderModel;
class Car extends Model {
    protected $table = 'qyc_car';

    /**
    *我的购物车数量
     */
    public function my_car_num($post){
        if(!empty($post['uid'])){
            $where['c.uid'] = array('eq',$post['uid']);
            $vot = db('car c')->where($where)->count();
            return intval($vot);
        }else{
            return 0;
        }
    }

    /**
     *加入购物车
     */
    public function add_car($post){

        if(empty($post['product_id'])|| empty($post['uid']) || empty($post['addtype'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试');
        }
        if(empty($post['spec_id'])|| empty($post['main_gg_id'])){
            return array('code'=>201,'msg'=>'请选择规格');
        }

        $where['product_id']  = array('eq',$post['product_id']);
        $where['uid']         = array('eq',$post['uid']);
        $where['spec_id']     = array('eq',$post['spec_id']);
        $vo = db('car')->where($where)->find();
        $title = '';
        if(!empty($vo)){
            if($post['addtype']==1){
                $num = $vo['num'] + $post['num'];
                $title = '加入购物车';
            }else if($post['addtype']==2){
                $num = $vo['num'] - $post['num'];
            }else if($post['addtype']==3){
                $num = $post['num'];
            }
            $num = $num>0 ? $num : 1;
        }else{
            $title = '加入购物车';
            $num = $post['num'];
        }

        $map['p.status'] = array('eq',1);
        $map['p.id']     = array('eq',$post['product_id']);
        $vot = db('product p')->where($map)->find();
        if(empty($vot)){
            return array('code'=>201,'msg'=>'该商品已下架！');
        }
        /*if($vot['stock']<=0){
            return array('code'=>201,'msg'=>'该商品已卖完！');
        }
        if($vot['stock']<$num){
			if($post['addtype']!=2){
				return array('code'=>201,'msg'=>'库存不足！');
			}
        }*/

        if(!empty($vo)){
             //更新
            $res = $this->where('id',$vo['id'])->update(['num'=>$num,'create_time'=>time()]);
        }else{
            //新增
            $post['create_time']   = time();
            $post['num']           = $num;
            $res = $this->allowField(true)->save($post);
        }
        if($res){
            $result['num'] = $this->my_car_num($post);
            return array('code'=>200,'msg'=>$title.'成功！','data'=>$result);
        }else{
            return array('code'=>201,'msg'=>$title.'失败！');
        }
    }




    /**
     *购物车列表
     */
    public function car_list($post){
        $where['c.uid']         = array('eq',$post['uid']);
        $list = db('car c')->join('product p','p.id=c.product_id')->join('product_guige g','g.id = c.main_gg_id')->field('c.id as car_id,c.num,c.spec_id,p.*,g.new_price,g.bigdl_price,g.dls_price')->where($where)->select();
        foreach ($list as $ke=>$ve){
            unset($list[$ke]['content']);
            $list[$ke]['imgurl'] = get_image($ve['icon']); //规格缩略图

            //查询规格标题
            $list[$ke]['gg_info'] = guige_info(['product_id'=>$ve['id'],'spec_id'=>$ve['spec_id']]);



            if($ve['status']!=1){
                $kc_status = 1;//商品已下架
            }else{
                /*if($ve['num']>$ve['stock']){
                    $kc_status = 2;//库存不足
                }*/
                $kc_status = 0;//正常
            }
            $list[$ke]['kc_status'] = $kc_status;
        }


        $userinfo = db('usermember')->find($post['uid']);

        //总价
        $OrderModel = new OrderModel();
        $checkRes = $OrderModel->oldTotalPrice($list,$userinfo);
        $result['product_list'] = $checkRes['list'];
        $result['counts']        = count($list);
        $result['total_price']  = $checkRes['total_price'];

        return array('code'=>200,'msg'=>'成功！','data'=>$result);
    }




    /**
     *删除购物车
     */
    public function delete_car($post){
        $where['uid']         = array('eq',$post['uid']);
        $where['id']          = array('in',$post['car_id']);
        $res = db('car')->where($where)->delete();
        if($res){
            return array('code'=>200,'msg'=>'删除成功！');
        }else{
            return array('code'=>201,'msg'=>'删除失败！');
        }
    }


    /**
     *购物车中移到收藏夹
     */
    public function move_collect($post){
        $where['uid']         = array('eq',$post['uid']);
        $where['id']          = array('in',$post['car_id']);
        $list = db('car')->where($where)->select();
        $ii = 0;
        foreach ($list as $ke=>$ve){
            $map['uid'] = array('eq',$post['uid']);
            $map['return_id'] = array('eq',$ve['product_id']);
            $map['collect_type'] = array('eq',1);
            $vo = db('collect')->where($map)->find();
            if(empty($vo)){
                $data[$ii]['uid'] = $post['uid'];
                $data[$ii]['return_id'] = $ve['product_id'];
                $data[$ii]['add_time'] = time();
                $data[$ii]['collect_type'] = 1;
                $ii++;
            }
        }
        if(!empty($data)){
            $rest = db('collect')->insertAll($data);
        }
        $res = db('car')->where($where)->delete();

        if($res){
            return array('code'=>200,'msg'=>'操作成功！');
        }else{
            return array('code'=>201,'msg'=>'操作失败！');
        }

    }



}
