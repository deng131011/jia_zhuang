<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class Shopapply extends Base {

    protected $table='qyc_shop_apply';

    /**
     *提交审核结果
     **/
    public function add_check_result($post){

        Db::startTrans();
        try{
            $vo = db('shop_apply')->find($post['id']);
            if($vo['check_status']>0){
                return ['code'=>201,'msg'=>'该记录已审核过，请勿重复审核'];
            }
            if($post['check_status']==2 && empty($post['check_reason'])){
                return ['code'=>201,'msg'=>'审核失败时必须填写失败原因'];
            }

            $data['check_time']   = time();
            $data['check_status'] = $post['check_status'];
            $data['check_reason'] = $post['check_status']==1 ? '审核成功' : $post['check_reason'];
            db('shop_apply')->where('id',$post['id'])->update($data);

            //更新用户表状态
            if($post['check_status']==1){
                $dataer['user_type']   = 2;
                $dataer['update_time'] = mydate();
                db('usermember')->where('id',$vo['uid'])->update($dataer);
            }
            Db::commit();
            return ['code'=>200,'msg'=>'提交成功'];
        }catch (\Exception $e){
            Db::rollback();
            return ['code'=>200,'msg'=>'提交失败'];
        }
    }




}
