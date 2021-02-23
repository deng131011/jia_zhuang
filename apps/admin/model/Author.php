<?php

namespace app\common\model;

use think\Model;

class Author extends Base {

    protected $table = 'qyc_music_author';

    //表单验证
    public function checkform($post){
        if(empty($post['names'])){
            return array('code'=>201,'msg'=>'请填写作者姓名！');
        }

        $where['names'] = array('eq',trim($post['names']));
        if(!empty($post['id'])){
            $where['id'] = array('neq',$post['id']);
        }
        $vo = db('music_author')->where($where)->find();
        if(!empty($vo)){
            return array('code'=>201,'msg'=>'该姓名已经存在！');
        }
        if(empty($post['id'])){
           $post['create_time'] = mydate();
           $post['status'] = 1;
        }
        return array('code'=>200,'msg'=>'验证成功！','data'=>$post);

    }






}
