<?php

namespace app\app\model;
use think\Db;
use think\Model;
class Score extends Model {



    /**
    *购买商品详情情况
     */
    public function buy_product_list($list){

        $product = [];
        foreach ($list as $ke=>$ve){

            $where['g.id']         = ['eq',$ve['main_gg_id']];
            $where['p.id']         = ['eq',$ve['product_id']];
            $where['p.status']     = ['eq',1];
            $vo = db('product p')->join('product_guige g','p.id = g.product_id')->where($where)->field('p.*,p.id as product_id,g.new_price,g.bigdl_price,g.dls_price,g.icon as gg_icon')->lock(true)->find();
            if(empty($vo)){

                return array('code'=>201,'msg'=>$vo['title'].' 商品已下架，请退出重新购买！');exit;
            }
            /*if($vo['stock']<$ve['num']){
                return array('code'=>201,'msg'=>$vo['title'].'库存不足！');exit;
            }*/

            $vo['gg_info'] = guige_info($ve);



            $vo['num']         = $ve['num'];
            $vo['car_id']      = !empty($ve['car_id']) ? $ve['car_id'] : 0;
            $vo['imgurl']      = get_image($vo['icon']);
            $vo['gg_imgurl']   = get_image($vo['gg_icon']);
            $product[$ke] = $vo;
        }

        return array('code'=>200,'msg'=>'可以购买！','data'=>$product);exit;
    }





    /**
     *购物车中购买的商品
     */
    public function buycar_product_list($post){
        $where['c.id']      = array('in',$post['car_id']);
        $where['c.uid']     = array('eq',$post['uid']);
        $where['p.status']  = array('eq',1);
        $list = db('product p')->join('car c','c.product_id = p.id')->join('product_guige g','g.id = c.main_gg_id')->where($where)->field('p.*,c.id as car_id,c.num,c.product_id,c.main_gg_id,c.spec_id,g.new_price,g.bigdl_price,g.dls_price,g.icon as gg_icon')->select();

        if(empty($list)){
            return array('code'=>201,'msg'=>'所购买商品已下架，请重新购买！');exit;
        }
        foreach ($list as $ke=>$ve){
           /* if($ve['stock']<$ve['num']){
                return array('code'=>201,'msg'=>$ve['title'].' 库存不足！');exit;
            }*/
            $list[$ke]['gg_info']     = guige_info($ve);
            $list[$ke]['imgurl']      = get_image($ve['icon']);
            $list[$ke]['gg_imgurl']   = get_image($ve['gg_icon']);
        }
        return array('code'=>200,'msg'=>'可以购买！','data'=>$list);exit;
    }








}
