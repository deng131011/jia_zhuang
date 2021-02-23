<?php

namespace app\admin\model;

use app\common\model\Base;
use app\app\model\Pay as PayModel;
use app\app\model\Order as OrderModel;
use think\Db;

class Withdrawal extends Base {


  /**
  *同意提现
   */
  public function sure_forprice($post){
      $where['id']           = array('eq',$post['id']);
      $where['order_number'] = array('eq',$post['ordernum']);
      $where['status']       = array('eq',1);
      $where['dk_status']    = array('eq',1);
      $vo = db('withdrawal')->where($where)->find();
      if(empty($vo)){
          return ['code'=>201,'msg'=>'非法操作，提现记录不存在'];exit;
      }

      Db::startTrans();
      try{

          //更新主表
          $data['dk_status'] = 3;
          $data['dk_time']   = time();
          $res1 = db('withdrawal')->where('id',$vo['id'])->update($data);

          //添加平台流水
          $data3['uid']          = 0;
          $data3['type']         = 2;
          $data3['zf_type']      = 1;
          $data3['pay_price']    = $vo['tx_price'];
          $data3['balance']      = 0;
          $data3['return_id']    = $vo['id'];
          $data3['create_time']  = time();
          $data3['remark']       = '用户提现';
          $data3['return_uid']   = $vo['uid'];
          $res3 = db('flowater')->insert($data3);

          Db::commit();
          return ['code'=>200,'msg'=>'操作成功'];exit;
      }catch (\Exception $e){
          Db::rollback();
          return ['code'=>201,'msg'=>'操作失败'];exit;
      }



  }



}
