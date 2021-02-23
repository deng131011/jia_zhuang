<?php

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class PayOrder extends Base {

    protected  $table = 'qyc_payup_order';


    //搜索条件
    public function search_form($get=array()){


        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
        }
        if($get['pay_status']=='dzf'){
            $where['b.pay_status'] = array('eq',0);
        }else if($get['pay_status']=='yzf'){
            $where['b.pay_status'] = array('eq',1);
        }
        if(!empty($get['keywords'])){
            $where['b.order_number|u.username|u.mobile'] = array('like','%'.trim($get['keywords']).'%');
        }
        $where['b.status'] = array('gt',-1);
        return $where;

    }





}
