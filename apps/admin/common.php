<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
//添加或更新多媒体附件分类
function update_media_term($media_id,$term_id){
    update_object_term($media_id,$term_id,'attachment');
}
//删除多媒体附件分类
function delete_media_term($media_id,$term_id){
    delete_object_term($media_id,$term_id,'attachment');
}

//禁用、启用状态
function forbidenStatus($status){
    if($status==1){
        return '<span class="fa fa-check text-success"></span>';
    }else{
        return '<span class="fa fa-ban text-danger"></span>';
    }
}



//订单类型
function orderType($order){
    if($order['order_type']==1){
        return ['status'=>1,'msg'=>'普通订单'];
    }else if($order['order_type']==2){
        return ['status'=>2,'msg'=>'<font style="color:#3c8dbc;">积分订单</font>'];
    }
}






