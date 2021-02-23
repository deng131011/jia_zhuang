<?php

namespace app\common\model;

use app\common\model\Base;

class Article extends Base {

    //表单验证
    public function checkform($post){
        if(empty($post['title'])){
            return array('code'=>201,'msg'=>'请填写标题！');
        }
        if(!empty($post['flag'])){
            $post['flag'] = implode(',',$post['flag']);
        }else{
            $post['flag'] = '';
        }

        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);
    }


    /**
     *获取商家列表
     */
    public function shop_list($post){
        $whet['title']    = array('like','%'.trim($post['keywords']).'%');
        $whet['status']   = array('eq',1);
        $list = db('shop')->where($whet)->order('id desc')->select();

        if(empty($list)){
            return array('code'=>201,'msg'=>'没有找到你要的商家！');
        }
        $str = '';
        foreach ($list as $ke=>$ve){
            $str .= '<tr><td class="bs-checkbox"><input type="checkbox" name="ids[]" value="'.$ve['id'].'"></td><td>'.$ve['id'].'</td><td class="course_title">'.$ve['title'].'</td><td><img src="'.get_image($ve['icon']).'" style="width:50px;" /></td></tr>';
        }
        return array('code'=>200,'msg'=>'成功！','data'=>$str);
    }
}
