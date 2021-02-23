<?php

namespace app\common\model;

use app\common\model\Base;

class Spectype extends Base {

    protected  $table = 'qyc_spec_type';

    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写名称！');
        }
        if(empty($post['type'])) {
            return array('code' => 201, 'msg' => '请选择类别！');
        }
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);
    }

    //获取商品规格列表
    public function lists($id){

        if(empty($id)){

            $list = db("spec_type")->where("status=1")->field("id,title,type,have_picture")->select();

            return $list;

        }else{
            $map = array();
            $map["price"] = array("gt",0);$map["status"] = array("eq",1);
            $spec = db("product_spec")->where($map)->find();

            if(empty($spec)){
                $list = db("spec_type")->where("status=1")->field("id,title,type,have_picture")->select();

                return $list;
            }else{
                $list = db("spec_type")->where("(id=".$spec["typeid"]." or type=2) and status=1")->field("id,title,type,have_picture")->select();

                return $list;
            }

        }

    }
}
