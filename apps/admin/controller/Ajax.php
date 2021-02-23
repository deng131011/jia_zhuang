<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use \think\Request;
use app\common\controller\Base;
use app\app\model\Address as Address;
class Ajax extends Base {

    /**
    *获取地区
     **/
    public function get_zone(){
        if(IS_POST){
            $post = input('param.');
            $Address = new Address();

            $pid = !empty($post['pid']) ? $post['pid'] :0;

            $list = $Address->zone_list(['pid'=>$pid]);
            if($post['level']==1){
                $str = '<option value="0">省</option>';
            }else if($post['level']==2){
                $str = '<option value="0">市</option>';
            }else if($post['level']==3){
                $str = '<option value="0">区县</option>';
            }else{
                $str = '';
            }

            foreach ($list as $ke=>$ve){
                //$selected = !empty($post['zone_id']) && $post['zone_id']==$ve['id'] ? 'selected' : ''; //选中
                $str .= '<option value="'.$ve['id'].'"  >'.$ve['title'].'</option>';
            }

            $result['html'] = $str;
            $this->ajaxReturn(array('code'=>200,'msg'=>'成功！','egmsg'=>'Succeed','data'=>$result));
        }
    }





}