<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;
use app\app\model\Headteacher as HeadteacherModel;

class Shoporder extends Base {

    protected  $table = 'qyc_shop_order';


    //搜索条件
    public function search_form($get=array()){

        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
        }
        if($get['hexiao_status']=='dzf'){
            $where['b.hexiao_status'] = array('eq',0);
        }else if($get['hexiao_status']=='yzf'){
            $where['b.hexiao_status'] = array('eq',1);
        }

        if(!empty($get['keywords'])){
            $where['b.hexiao_code|b.order_mobile|u.mobile'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status']     = array('gt',-1);
        return $where;

    }


    /**
    *指派老师
     **/
    public function changStatus($post){
        $HeadteacherModel = new HeadteacherModel();
        $res = $HeadteacherModel->fenpei_class($post);
        return $res;
    }


    /**
     *获取班级
     */
    public function get_class($post){
        $str = '';
        $where['u.id'] = array('eq',$post['teacher_id']);
        $vo =db('usermember u')->join('teacher_data t','t.uid = u.id')->field('u.*,t.class_ids')->where($where)->find();

        $map['id'] = array('in',$vo['class_ids']);
        $map['status'] = array('eq',1);
        $list = db('classlist')->where($map)->select();
        foreach ($list as $ke=>$ve){
            $str .= '<option value="'.$ve['id'].'">'.$ve['title'].'</option>';
        }
        return array('code'=>200,'msg'=>'成功','html'=>$str);
    }

}
