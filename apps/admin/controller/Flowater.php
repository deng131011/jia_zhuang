<?php

namespace app\admin\controller;
use app\admin\model\Fastdi as FastdiModel;
use think\Db;

class Flowater extends Admin{

    function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 流水
     */
    public function index() {

        $get = input('param.');
        $get['zf_type'] = !empty($get['zf_type'])? $get['zf_type'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['dates']     = !empty($get['dates'])? $get['dates'] : '';


        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
        }
        if(!empty($get['zf_type'])){
            $where['b.zf_type'] = array('eq',$get['zf_type']);
        }
        $where['b.status'] = array('gt',-1);
        $where['b.pt_type'] = array('eq',2);

        $map = $where;
        //金额
        $map['b.type'] = array('eq','1');
        $sumprice_a = Db::name('flowater')->alias('b')->where($map)->sum('pay_price');
        $map['b.type'] = array('eq',2);
        $sumprice_b = Db::name('flowater')->alias('b')->where($map)->sum('pay_price');

        $sumprice = floatval($sumprice_a)-floatval($sumprice_b);


        $data_list =  Db::name('flowater')->alias('b')->where($where)->order('id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });
        $this->assign('list',$data_list);
        $this->assign('meta_title','平台流水');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        $this->assign('sumprice',$sumprice);
        $this->assign('sumprice_a',$sumprice_a);
        $this->assign('sumprice_b',$sumprice_b);
        if(!empty($get['excel'])){
           /* $excel_list =  Db::name('order')->alias('b')->where($where)->order('id desc')->select();
            foreach ($excel_list as $ks=>$vs){
                $excel_list[$ks] = $this->foreach_list($vs);
            }
            $this->exportExcel($excel_list); //导出excel*/
        }
        $get['dates'] = return_dates($get);
        $this->assign('get',$get);
        return $this->fetch();
    }

    //公共循环插入
    public function foreach_list($item){
        $user = db('usermember')->find($item['return_uid']);
        $item['user'] = $user;
        $item['flow_type'] = price_flowater_zftype($item['zf_type']);

		
        return $item;
    }


    /**
    *乐币明细
     **/
    public function lebi_list(){
        $get = input('param.');
        $get['zf_type']   = !empty($get['zf_type'])? $get['zf_type'] : '';
        $get['keywords']  = !empty($get['keywords'])? $get['keywords'] : '';
        $get['dates']     = !empty($get['dates'])? $get['dates'] : '';


        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
            $get['dates'] = $timearr[0].' / '.$timearr[1];
        }
        $this->assign('get',$get);

        if(!empty($get['zf_type'])){
            $where['b.zf_type'] = array('eq',$get['zf_type']);
        }
        $where['b.uid'] = array('eq',$get['uid']);
        $where['b.status'] = array('gt',-1);

        $data_list =  Db::name('flowater_lebi')->alias('b')->join('usermember u','b.uid = u.id')->where($where)->field('b.*,u.username')->order('b.id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list_lebi($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','乐币明细');
        $this->assign('field',['name'=>'dates','id'=>'dates']);

        //
        $user = db('usermember')->find($get['uid']);
        $this->assign('user',$user);
       // $this->assign('sumprice',$sumprice);
        return $this->fetch();
    }

    public function foreach_list_lebi($item){
        $item['title'] = flowater_lebi_title($item,'title');
        $item['create_date'] = mydate($item['create_time']);
        return $item;
    }




    /**
     * 用户资金流水
     */
    public function price_list() {

        $get = input('param.');
        $get['zf_type'] = !empty($get['zf_type'])? $get['zf_type'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['dates']     = !empty($get['dates'])? $get['dates'] : '';

        if(!empty($get['dates'])){
            $timearr = explode('/',$get['dates']);
            $time = timeCondition($timearr[0],$timearr[1]);
            if(!empty($time)){
                $where['b.create_time'] = $time;
            }
            $get['dates'] = $timearr[0].' / '.$timearr[1];
        }
        $this->assign('get',$get);
        if(!empty($get['zf_type'])){
            $where['b.zf_type'] = array('eq',$get['zf_type']);
        }
        $where['b.uid']     = array('eq',$get['uid']);
        $where['b.status']  = array('gt',-1);





        $data_list =  Db::name('flowater')->alias('b')->join('usermember u','b.uid = u.id')->where($where)->order('b.id desc')->field('b.*,u.username,u.mobile')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','用户流水');
        $this->assign('field',['name'=>'dates','id'=>'dates']);

        return $this->fetch();
    }




}