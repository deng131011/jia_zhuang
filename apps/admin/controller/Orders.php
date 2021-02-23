<?php

namespace app\admin\controller;
use think\Db;

class Orders extends Admin{

    function _initialize()
    {

        parent::_initialize();

        $this->model = model('Orders');
    }

    /**
     * 订单列表
     */
    public function index() {

        $get = input('param.');
        $get['pay_status']   = !empty($get['pay_status'])? $get['pay_status'] : '';
        $get['order_status'] = !empty($get['order_status'])? $get['order_status'] : '';
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';


        $where = model('Orders')->search_form($get); //搜索条件
        $data_list =  Db::name('order')->alias('b')->where($where)->order('id desc')->paginate(20)->each(function($item,$key){
            return $this->foreach_list($item);
        });

        $this->assign('list',$data_list);
        $this->assign('meta_title','商品订单列表');
        $this->assign('field',['name'=>'dates','id'=>'dates']);
        if(!empty($get['excel'])){
            $excel_list =  Db::name('order')->alias('b')->where($where)->order('id desc')->select();
            foreach ($excel_list as $ks=>$vs){
                $excel_list[$ks] = $this->foreach_list($vs);
            }
            $this->exportExcel($excel_list); //导出excel
        }

        $get['dates'] = return_dates($get);
        $this->assign('get',$get);
        return $this->fetch();
    }


    //公共循环插入
    public function foreach_list($item){
        $item['ordertype']      = orderType($item);
        $item['orderstatus']    = orderStatus($item);
        return $item;
    }


    /**
     *订单详情
     */
    public function details(){

        $post =  input('param.');
        if(empty($post['order_id'])){
            $this->error('非法操作！');
        }
        $vo = db('order')->find($post['order_id']);
        $vo = $this->foreach_list($vo);
        //物流公司
		if(!empty($vo['wuliu_code'])){
            $logic = db('logistics_company')->where('code',$vo['wuliu_code'])->find();
            $vo['logistics_title'] = $logic['title'];
		}

        $user = db('usermember')->find($vo['uid']);

        //商品列表
        $sonlist = $this->product_list($post['order_id']);

        //快递公司
        $company_list = db('logistics_company')->where('status',1)->order('id desc')->select();

        $this->assign('info',$vo);
        $this->assign('sonlist',$sonlist);
        $this->assign('company_list',$company_list);
        $this->assign('meta_title','订单详情');
        return $this->fetch();
    }

    //订单商品列表
    public function product_list($order_id){
        $where['l.order_id'] = array('eq',$order_id);
        $sonlist =  db('order_list l ')->where($where)->order('l.id desc')->field('l.*')->select();

        foreach ($sonlist as $ke=>$ve){
            $sonlist[$ke]['info_json'] = json_decode($ve['info_json'],true);

            //判断是否有售后商品
            /*
            $mapst['order_id']     = array('eq',$ve['order_id']);
            $mapst['order_listid'] = array('eq',$ve['id']);
            $mapst['type']         = array('eq',1);
            $buyafter = db('buyafter')->where($mapst)->find();*/
            $sonlist[$ke]['afterStatus'] = buyafter_status($ve['after_status']);
        }
        return $sonlist;
    }


    /**
    *打印
     */
    public function prints(){
         return $this->details();
    }





    /**
     *删除订单
     */
    public function delOrder(){
        $post =  input('param.');
        if(empty($post['ids'])){
            $this->error('请选择数据！');
        }
        $res = model('Orders')->delOrder($post);
        if($res['code']==200){
            $this->success('删除成功！');
        }else{
            $this->error($res['msg']);
        }
    }


    /**
     *更改订单状态
     */
    public function changStatus(){
        $post =  input('param.');
        if(empty($post['order_id']) || empty($post['order_status'])){
            $this->error('缺少参数！');
        }
        $res = model('Orders')->changStatus($post);
        if($res['code']==200){
            $this->success($res['msg']);
        }else{
            $this->error($res['msg']);
        }
    }



    /**
    *导出excel
    */
    public function exportExcel($list){

        $thead = [
            ['title'=>'订单号','ziduan'=>'order_number'],
            ['title'=>'收货人','ziduan'=>'order_user'],
            ['title'=>'联系电话','ziduan'=>'order_mobile'],
            ['title'=>'收货地址','ziduan'=>'address'],
            ['title'=>'订单类型','ziduan'=>'ordertype'],
            ['title'=>'支付状态','ziduan'=>'pay_status'],
            ['title'=>'支付金额','ziduan'=>'pay_price'],
            ['title'=>'下单时间','ziduan'=>'create_time'],
            ['title'=>'订单状态','ziduan'=>'orderstatus'],
        ];

        foreach ($list as $ke=>$ve){
            $list[$ke]['ordertype']  = strip_tags($ve['ordertype']['msg']);
            $list[$ke]['pay_status'] = $ve['pay_status']==1?'支付成功':'待支付';
            $list[$ke]['pay_price']  = $ve['order_type'] ==2 ? $ve['pay_score'].'积分': '￥'.$ve['pay_price'];
            $list[$ke]['create_time'] = mydate($ve['create_time'],2);
            $list[$ke]['orderstatus'] = strip_tags($ve['orderstatus']['msg']);
        }

        downExcelExport($thead,$list,'订单记录');
    }

	
	
	/**
    *地图订单点界面
    */
	public function map_order(){
		
		header("Content-type: text/html; charset=utf-8");
		
		$where = 'status=1 and order_status=0 and pay_status=1 and ((order_type = 4 and team_status=1) or order_type<>4)';
		
		$list = db('order')->where($where)->field('*,count(*) as counts')->group('county_id')->select();
		$ii = 0;
		foreach($list as $ke=>$ve){
			$province = modelField($ve['province_id'],'china','title');
			$city     = modelField($ve['city_id'],'china','title');
			$county   = modelField($ve['county_id'],'china','title');
			
			$address = $province.$city.$county;
			
			$result = file_get_contents('http://restapi.amap.com/v3/geocode/geo?key=f7a3aeb17d5402d577930f469a666131&s=rsv3&address='.$address);
            $res = json_decode($result,true);
			if($res['status'] == 1 && $res['info'] == 'OK' && !empty($res['geocodes'][0]['location'])){

                $jwd = explode(',',$res['geocodes'][0]['location']);
                $data[$ii]['text']      = $county;
                $data[$ii]['is']         = 'ok';
                $data[$ii]['name']       = $ve['counts'];
                $data[$ii]['county_id'] = $ve['county_id'];
                $data[$ii]['longitude'] = $jwd[0];//纬度
                $data[$ii]['latitude']  = $jwd[1];//纬度
				$ii++;
            }
			
		}
		
		if(empty($data)){
			$this->error('经纬度获取失败！');exit;
		}
		//p(json_encode($data));
		$this->assign('data',json_encode($data));
		 $this->assign('meta_title','地图订单列表');
		return $this->fetch();
	}
	
	
	/**
    *每个区的订单列表
    */
	public function county_order(){
		
		$get = input('param.');
        $this->assign('get',$get);
		
		$where = 'county_id='.$get['county_id'].' and status=1 and order_status=0 and pay_status=1 and ((order_type = 4 and team_status=1) or order_type<>4)';
        $data_list =  db('order')->where($where)->order('id desc')->select();
		foreach($data_list as $ke=>$ve){
			 $data_list[$ke] = $this->foreach_list($ve);
		}
        $this->assign('list',$data_list);
        $this->assign('meta_title','商品订单列表');
        
		
		return $this->fetch();
	}
	

	/**
    *订单路线界面
    */
	public function waypoint(){
		
		$get = input('param.');
        $this->assign('get',$get);
		
		
		$where = 'id in ('.$get['idarr'].') and status=1 and order_status=0 and pay_status=1 and ((order_type = 4 and team_status=1) or order_type<>4)';
        $data_list =  db('order')->where($where)->order('id desc')->select();
		
		//起点、终点
		$vo = db('china')->find($data_list[0]['city_id']);
		if(empty($vo['lng_lat'])){
			echo '该城市起点地址为空或者起点经纬度为空！';exit;
		}
		
		$start = explode(',',$vo['lng_lat']);
	    
		foreach($data_list as $ke=>$ve){
			$result = file_get_contents('http://restapi.amap.com/v3/geocode/geo?key=f7a3aeb17d5402d577930f469a666131&s=rsv3&address='.$ve['address']);
            $res = json_decode($result,true);
			
			
			if($res['status'] != 1 || $res['info'] != 'OK' || empty($res['geocodes'][0]['location'])){

			  echo '订单号：'.$ve['order_number'].'获取经纬度失败！';exit;
                
            }
			$lnglat = explode(',',$res['geocodes'][0]['location']);
			
			$data[$ke]['address'] = $ve['address'];//经度纬度
			$data[$ke]['lng'] = $lnglat[0];//经度纬度
			$data[$ke]['lat'] = $lnglat[1];//经度纬度
			
			
			$data[$ke]['length'] = getDistance($start[1], $start[0], $lnglat[1], $lnglat[0]);
			
		}
		
		//p($data);
		
		array_multisort(array_column($data,'length'),SORT_DESC,$data);
		
		
		
		
		$star_end['start'] = $vo['lng_lat'];
		$star_end['end']   = $data[0]['lng'].','.$data[0]['lat'];
		
		unset($data[0]);
		
		$arr = array();
		$arr = array_merge($arr,$data);
		
        $this->assign('data',json_encode($arr));
		
        $this->assign('star_end',$star_end);
        $this->assign('meta_title','订单路线');
        
		
		return $this->fetch();
	}
	

	
	 /**
     *地图界面点击发货
     */
    public function mapChangStatus(){
        $post =  input('param.');
        if(empty($post['order_id']) || empty($post['order_status'])){
			 $this->ajaxReturn(array('code'=>201,'msg'=>'缺少参数！'));
        }
        $res = model('Orders')->changStatus($post);
        $this->ajaxReturn($res);
    }


}