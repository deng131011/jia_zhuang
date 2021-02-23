<?php

namespace app\common\model;

use app\common\model\Base;

class Product extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写商品名称！');
        }

        if(empty($post['type1'])){
            return array('code'=>201,'msg'=>'请选择一级分类！');
        }
        if(empty($post['type2'])){
            return array('code'=>201,'msg'=>'请选择二级分类！');
        }

        if(floatval($post['old_price'])<=0 || floatval($post['show_price'])<=0){
            return array('code'=>201,'msg'=>'请完善展示价格！');
        }

        if(!empty($post['flag'])){
            $post['flag'] = implode(',',$post['flag']);
        }else{
            $post['flag'] = '';
        }


        $data = array();
        if(!empty($post['data_title'])){
            $ii = 0;
            foreach ($post['data_title'] as $ke=>$ve){
                if(!empty($ve) || $post['data_val'][$ke]){
                    $data[$ii]['data_title'] = $ve;
                    $data[$ii]['data_val']   = $post['data_val'][$ke];
                    $ii++;
                }
            }
        }
        $post['data_json'] = json_encode($data,JSON_UNESCAPED_UNICODE);


        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);
    }


    /**
    *ajax搜索产品返回
     **/
    public function ajax_product($post){
        $where['title'] = array('like','%'.$post['keywords'].'%');
        $where['status'] = array('eq',1);
        $list = db('product')->where($where)->select();
        return array('items'=>$list,'total_count'=>count($list));
    }



}
