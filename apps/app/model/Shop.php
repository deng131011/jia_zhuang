<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Comment as CommentModel;
use app\app\model\Invitation as InvitationModel;
class Shop extends Model {



    /**
     *商家列表
     */
    public function shop_list($post){

        $sort = 'h.sort desc';

        if(!empty($post['xqtj_type']) && $post['xqtj_type']==1){
            //获取商家详情中的推荐商家
            if(!empty($post['shop_id']) && !empty($post['type1'])){
                $where['h.id']       = array('neq',$post['shop_id']);
                $where['h.type1']    = array('eq',$post['type1']);
            }
        }


        if(!empty($post['flag'])){
            $where[]    = array('exp',Db::raw('FIND_IN_SET('.$post['flag'].',h.flag)'));
        }
        if(!empty($post['keywords'])){
            $where['h.title']    = array('like','%'.trim($post['keywords']).'%');
        }
        if(!empty($post['type1'])){
            $where['h.type1']    = array('eq',$post['type1']);
        }
        $where['h.status']    = array('eq',1);
        $where['u.user_type'] = array('eq',2);

        $num =  !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num:0;

        $list = db('shop h')->join('usermember u','h.uid = u.id')->where($where)->field('h.*')->order($sort)->limit($page,$num)->select();

        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->shop_foreach($ve,$post);
        }
        return $list;
    }

   /**
   *商家循环
    * cx_type:2发布帖子中获取商家
    */
    public function shop_foreach($ve,$post=[]){
        $ve['imgurl']      = get_image($ve['icon']);
        $CommentModel = new CommentModel();
        $count_res = $CommentModel->comment_list(['return_id'=>$ve['id'],'type'=>2],'count');
        $ve['comment_score'] = comment_avg($count_res['star'],$count_res['counts']); //评论平均分数
        if(!empty($post['cx_type']) && $post['cx_type']==1){
            $lng_lat = explode(',',$ve['lng_lat']);
            $ve['ju_li'] = getdistance($post['lng'], $post['lat'], $lng_lat[0], $lng_lat[1]); //距离
        }
        return $ve;
    }



    /**
     *商家详情
     */
    public function details($post){
        $post['uid'] = !empty($post['uid']) ? $post['uid'] : 0;

        $where['id'] = array('eq',$post['shop_id']);
        $where['status'] = array('eq',1);
        db('shop')->where($where)->setInc('views'); //浏览量

        $vo = db('shop')->where($where)->find();
        if(empty($vo)){
             return array('code'=>201,'msg'=>'商家不存在');
        }

        $vo = $this->shop_foreach($vo,$post);
        $vo['content']     = preg_replace('/(<img.+?src=")(.*?)/', '$1'.config('index_url').'$2', $vo['content']);
       
        $vo['is_collect'] = model('Product')->isHaveCollect($vo['id'],$post['uid'],2);
        $result['details'] = $vo;
        //营业时间
        if(!empty($vo['yingye_time'])){
            $yetime = explode('-',$vo['yingye_time']);
            $start = explode(':',$yetime[0]);
            $end   = explode(':',$yetime[1]);
            $yy_times['start']['hour'] = $start[0];
            $yy_times['start']['mint'] = $start[1];
            $yy_times['end']['hour'] = $end[0];
            $yy_times['end']['mint'] = $end[1];
            $result['yy_times'] = $yy_times;
        }else{
            $result['yy_times'] = [];
        }



        $CommentModel = new CommentModel();

        //人气排名
        $maps['views'] = array('gt',$vo['views']);
        $rqcount = db('shop')->where($maps)->count();
        $result['rq_counts'] = intval($rqcount)+1;

        //好评排名
        $whes['c.star']   = array('egt',4);
        $whes['c.type']   = array('eq',2);
        $whes['c.status'] = array('eq',1);
        $comm_count = db('comment c')->join('shop s','s.id = c.return_id')->where($whes)->group('c.return_id')->field('sum(c.star) as stars,c.return_id')->order('stars desc')->select();
        $hp_pm = 0;
        foreach ($comm_count as $ke=>$ve){
             if($ve['return_id']==$vo['id']){
                 $hp_pm =($ke+1);
             }
        }
        $result['hppm_counts'] = $hp_pm;
        //推荐列表
        $result['tj_list'] = $this->shop_tjlist(['shop_id'=>$post['shop_id'],'page'=>1,'num'=>5]);
        $result['tj_list_count'] = $this->shop_tjlist(['shop_id'=>$post['shop_id']],'count'); //数量
        //评价列表
        $arr = ['return_id'=>$vo['id'],'see_type'=>1,'type'=>2,'page'=>1,'num'=>3];
        $result['comment']['count']        = $CommentModel->comment_list($arr,'count'); //数量
        $result['comment']['comment_list'] = $CommentModel->comment_list($arr); //列表

        //打卡动态
        $InvitationModel   = new InvitationModel();
        $result['daka_list'] = $InvitationModel->get_invitation(['shop_id'=>$vo['id'],'category_id'=>0,'page'=>1,'num'=>4]);

        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }


    /**
    *商家推荐列表
     */
    public function shop_tjlist($post,$count=''){
        $maps['status']    = array('eq',1);
        $maps['shop_id']   = array('eq',$post['shop_id']);
        if($count=='count'){
            $listcount = db('shop_tjlist')->where($maps)->count();
            return $listcount;
        }

        $num =  !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = db('shop_tjlist')->where($maps)->order('id desc')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl'] = get_image($ve['icon']);
        }
        return $list;
    }


    /**
     *商家菜单分类
     */
    public function menu_type($post){
        $maps['status']    = array('eq',1);
        $maps['shop_id']   = array('eq',$post['shop_id']);
        $list = db('shop_menu_type')->where($maps)->field('id,title,icon')->order('sort asc')->select();
        return $list;
    }

    /**
     *商家菜单列表
     */
    public function menu_list($post){
        if(!empty($post['keywords'])){
            $maps['title']    = array('like','%'.trim($post['keywords']).'%');
        }
        if(!empty($post['type1'])){
            $maps['type1']    = array('eq',$post['type1']);
        }
        $maps['status']    = array('eq',1);
        $maps['shop_id']   = array('eq',$post['shop_id']);
        $list = db('shop_menu')->where($maps)->order('sort desc,id desc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl'] = get_image($ve['icon']);
        }
        return $list;
    }

    /**
     *提交预约订单
     * order_type:1订餐单
     */
    public function add_order($post){
        if(empty($post['uid']) || empty($post['token'])){
            return ['code'=>201,'msg'=>'请先登录'];
        }
        if(empty($post['shop_id'])){
            return ['code'=>201,'msg'=>'请选择商家'];
        }
        if(empty($post['order_type'])){
            return ['code'=>201,'msg'=>'请选择订单类型'];
        }

        if(!empty($post['order_date'])){
            if($post['order_date']<date('Y-m-d H:i')){
                return ['code'=>201,'msg'=>'预约时间不能小于当前时间'];
            }
        }

        if(empty(trim($post['order_user'])) || empty(trim($post['order_mobile'])) || empty(trim($post['order_date']))){
            return ['code'=>201,'msg'=>'请完善信息'];
        }
        if($post['order_type']==1 && empty($post['product_info'])){
            return ['code'=>201,'msg'=>'请选择菜品'];
        }
        $where['h.id'] = array('eq',$post['shop_id']);
        $where['h.status'] = array('eq',1);
        $where['u.user_type'] = array('eq',2);
        $shop = db('shop h')->join('usermember u','h.uid = u.id')->where($where)->field('h.*')->find();
        if(empty($shop)){
            return ['code'=>201,'msg'=>'该商家不存在或以下架'];
        }
        Db::startTrans();
        try{
            if($post['order_type']==1){
                $product_arr = []; $ii = 0;
                foreach ($post['product_info'] as $ke=>$ve){
                    $maps['id']      = array('eq',$ve['product_id']);
                    $maps['shop_id'] = array('eq',$post['shop_id']);
                    $product = db('shop_menu')->where($maps)->find();
                    if(!empty($product) && $product['status']==1){
                        $product['imgurl'] = get_image($product['icon']);
                        $product_arr[$ii] = $product;
                        $product_arr[$ii]['num']           = $ve['num'] ;
                        $product_arr[$ii]['one_sum_price'] = $ve['num'] * floatval($product['new_price']);
                        $ii++;
                    }else{
                        Db::rollback();
                        return ['code'=>201,'msg'=>$product['title'].' 已下架'];
                    }
                }
                $total_price = array_sum(array_column($product_arr,'one_sum_price')); //总价
            }else{
                $total_price = 0;
            }



            //添加订单
            $data['order_number']        = order_number('shop_order');
            $data['hexiao_code']         = hexiao_code('shop_order',$post['shop_id']);
            $data['uid']                 = $post['uid'];
            $data['shop_id']             = $post['shop_id'];
            $data['order_user']          = $post['order_user'];
            $data['order_mobile']        = $post['order_mobile'];
            $data['order_date']          = $post['order_date'];
            $data['person_num']          = $post['person_num'];
            $data['remark']              = $post['remark'];
            $data['order_type']          = $post['order_type'];
            $data['zhe_kou']             = floatval($shop['zhe_kou']); //折扣
            $data['total_price']         = $total_price;
            $data['remark']              = !empty($post['remark']) ? $post['remark'] : '';
            $data['create_time']         = time();
            $res1 = db('shop_order')->insertGetId($data);

            if($post['order_type']==1){
                //附表
                foreach ($product_arr as $ks=>$vs){
                    $datab[$ks]['order_id']   = $res1;
                    $datab[$ks]['shop_id']    = $post['shop_id'];
                    $datab[$ks]['menu_id']    = $vs['id'];
                    $datab[$ks]['info_json']  = json_encode($vs,JSON_UNESCAPED_UNICODE);
                    $datab[$ks]['num']        = $vs['num'];
                    $datab[$ks]['new_price']  = floatval($vs['new_price']);
                    $datab[$ks]['pay_price']    = $vs['one_sum_price'];
                }
                $res = Db::table('qyc_shop_order_list')->insertAll($datab);
            }

            Db::commit();
            $result['order_id']     = $res1;
            $result['order_number'] = $data['order_number'];
            return ['code'=>200,'msg'=>'提交成功','data'=>$result];
        }catch (\Exception $e){
            Db::rollback();
            return ['code'=>201,'msg'=>'提交失败'];
        }
    }


    /**
     *商家订单详情
     */
    public function order_details($post){
        $where['id'] = array('eq',$post['order_id']);
        $where['order_number'] = array('eq',$post['order_number']);
        $order = db('shop_order')->where($where)->find();
        if(!empty($order)){
            $order['create_date'] = mydate($order['create_time']);
			//商家信息
            $shop = db('shop')->field('id as shop_id,title,icon')->find($order['shop_id']);
			if(empty($post["uid"])){
				
				$order["is_comment"] = 0;
				
			}else{
				
				$map = array();
				$map["uid"] = array('eq',$post["uid"]);
				$map["order_id"] = array('eq',$order["id"]);
				$map["status"] = array('eq',1);
				
				$comment = db("comment")->where($map)->find();
				
				$order["is_comment"] = empty($comment)?0:1;
				
			}
			
			$order["shop_title"] = $shop["title"];
			$order["shop_imgs"] = get_image($shop["icon"]);
			
            $result['order_info'] = $order;

            
            $result['shop'] = $shop;

            //商品信息
            if($order['order_type']==1){
                $maps['order_id'] = array('eq',$post['order_id']);
                $order_list = db('shop_order_list')->where($maps)->select();
                foreach ($order_list as $ke=>$ve){
                    $order_list[$ke]['info_json'] = json_decode($ve['info_json'],true);
                }
                $result['order_list'] = $order_list;
            }
			
			
        }
        return !empty($result) ? $result : [];
    }



    /**
     *地图地名
     */
    public function map_name($post=[]){
       $where['status'] = array('eq',1);
       $list = db('zonename')->where($where)->field('id,title,lng_lat')->order('sort desc,id desc')->select();
       foreach ($list as $ke=>$ve){
           $list[$ke]['lng_lat'] = explode(',',$ve['lng_lat']);
       }
       return $list;
    }

    /**
     *服务日程列表
     */
    public function days_list($post=[]){
        $where['status'] = array('eq',1);
        $where['type'] = array('eq',1);
        $list = db('article')->where($where)->order('sort asc,id desc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl'] = get_image($ve['icon']);
        }
        return $list;
    }

    /**
     *日程详情
     */
    public function days_details($post){
        $where['type'] = array('eq',1);
        $where['id']   = array('eq',$post['list_id']);
        $vo = db('article')->where($where)->find();
        if(!empty($vo)){
            $vo['imgurl'] = get_image($vo['icon']);
        }
        $result['details'] = $vo;

        //关联商家
        if(!empty($vo['shop_ids'])){
            $maps['h.id'] = array('in',$vo['shop_ids']);
            $shop_list = db('shop h')->join('usermember u','h.uid = u.id')->where($maps)->field('h.*')->select();
            $post['cx_type'] = 1;
            foreach ($shop_list as $ke=>$ve){
                $shop_list[$ke] = $this->shop_foreach($ve,$post);
            }
        }
        $result['shop_list'] = !empty($shop_list) ? $shop_list : [];
        return $result;
    }


    /**
     *客服热线分类
     */
    public function kefu_type($post){
        $where['pid']    = array('eq',61);
        $where['status'] = array('eq',1);
        $list = db('category')->where($where)->order('sort asc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl'] = get_image($ve['icon']);
            $msp['type1'] = array('eq',$ve['id']);
            $msp['status'] = array('eq',1);
            $list[$ke]['dw_counts'] = db('contact')->where($msp)->count();
        }
        return $list;
    }


    /**
     *客服热线列表
     */
    public function kefu_list($post){
        $where['type1']  = array('eq',$post['type1']);
        $where['status'] = array('eq',1);
        $list = db('contact')->where($where)->order('sort asc,id desc')->select();
        return $list;
    }

    /**
     *服务首页
     */
    public function fuwu_index($post=[]){
        $result['banner'] = getAdImages(1,'',2);
        //新店尝鲜
        $list_a = $this->shop_list(['flag'=>1,'page'=>1,'num'=>1]);
        foreach ($list_a as $ka=>$va){
            $list_a[$ka]['imgurl'] = get_image($va['icon']);
        }
        $result['xdcx_info'] = $list_a;

        //好店推荐
        $list_b = $this->shop_list(['flag'=>2,'page'=>1,'num'=>1]);
        foreach ($list_b as $kb=>$vb){
            $list_b[$kb]['imgurl'] = get_image($vb['icon']);
        }
        $result['hdtj_info'] = $list_b;

        //商家大促
        $list_c = $this->shop_list(['flag'=>3,'page'=>1,'num'=>1]);
        foreach ($list_c as $kc=>$vc){
            $list_c[$kc]['imgurl'] = get_image($vc['icon']);
        }
        $result['sjdc_info'] = $list_c;

        //本周人气
        $list_d = $this->shop_list(['flag'=>4,'page'=>1,'num'=>1]);
        foreach ($list_d as $kd=>$vd){
            $list_d[$kd]['imgurl'] = get_image($vd['icon']);
        }
        $result['bzrq_info'] = $list_d;

        return $result;
    }


    /******************************  商家端 *********************************/
    /**
     *商家入驻资料提交
     */
    public function ruzhu_add($post){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试');
        }
        if(empty(trim($post['uid'])) || empty(trim($post['tel']))){
            return array('code'=>201,'msg'=>'请输入姓名或电话');
        }
        if(empty(trim($post['code_a'])) || empty(trim($post['code_b']))){
            return array('code'=>201,'msg'=>'请上传身份证正反面');
        }
        unset($post['token']);
        $where['uid']  = array('eq',$post['uid']);
        $vo = db('shop_apply')->where($where)->find();
        if(!empty($vo)){
            $post['check_status'] = 0;
            $post['check_time']   = 0;
            $post['check_reason'] = '';
			$post['update_time'] = time();
            $res = db('shop_apply')->where('id',$vo['id'])->update($post);
        }else{
            $post['create_time']  = time();
            $res = db('shop_apply')->insertGetId($post);
        }

       if($res){
           return array('code'=>200,'msg'=>'提交成功，等待审核');
       }else{
           return array('code'=>201,'msg'=>'提交失败');
       }
    }



    /**
     *商家申请详情
     */
    public function apply_details($post){
        if(empty($post['uid']) || empty($post['token'])){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试');
        }
        $where['uid']  = array('eq',$post['uid']);
        $vo = db('shop_apply')->where($where)->find();
        if(!empty($vo)){
            $vo['code_a'] = get_image($vo['code_a']) ;
            $vo['code_b'] = get_image($vo['code_b']) ;
            $vo['yyzz_img'] = get_image($vo['yyzz_img']) ;
        }
        return array('code'=>200,'msg'=>'成功','data'=>$vo);
    }

    /**
     *撤销商家申请
     */
    public function cancel_apply($post){
        $where['uid']  = array('eq',$post['uid']);
        $vo = db('shop_apply')->where($where)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'申请记录不存在');
        }
        if($vo['check_status']==1){
            return array('code'=>201,'msg'=>'已审核成功，不能取消');
        }
        $res = db('shop_apply')->where('id',$vo['id'])->delete();
        if($res){
            return array('code'=>200,'msg'=>'取消成功');
        }else{
            return array('code'=>201,'msg'=>'取消失败');
        }
    }


}
