<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Comment as CommentModel;
use app\app\model\Car as CarModel;
class Product extends Model {
    protected $table = 'qyc_product';



    /**
     * 商品分类
     */
    public function product_type($post){
        if(!empty($post['tj_type']) && $post['tj_type']==1){
            $where['flag']    = array('eq',1);
        }else{
            $pid = $post['pid']>0 ? $post['pid'] : 1;
            $where['pid']    = array('eq',$pid);
        }

        $where['status'] = array('eq',1);
        $list = db('producttype')->where($where)->field('id,title,icon')->order('sort asc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl'] = get_image($ve['icon']);
        }

        return $list;

    }

    /**
     * 普通商城首页
     */
    public function product_index($post){
        $post['uid'] = !empty($post['uid']) ? $post['uid'] : 0;
        //广告
        $result['banner'] = getAdImages(3,'',2);
        return array('code'=>200,'msg'=>'成功！','data'=>$result);
    }


    /**
     * 商品列表
     */
    public function product($post,$is_pc=''){
        if(!empty($post['keywords'])){
            $where['p.title'] = ['like','%'.trim($post['keywords']).'%'];
        }

        if(!empty($post['flag'])){
            $where[] = ['exp',Db::raw('FIND_IN_SET('.$post['flag'].',p.flag)')];
        }
        if(!empty($post['type1'])){
            $where['p.type1'] = ['eq',$post['type1']];
        }
        if(!empty($post['type2'])){
            $where['p.type2'] = ['eq',$post['type2']];
        }

        $where['p.status'] = ['eq',1];
        //$where['p.stock']  = ['gt',0];
        if($is_pc==1){
            return $where;
        }


        $num = !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;

        //排序
        $sort = '';
        //全部商品
        $list = db('product p')->where($where)->field('p.*')->order($sort)->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->foreach_list($ve);
        }

        //$CarModel = new CarModel();
        //$result['my_car_nums'] = $CarModel->my_car_num($post);

        return $list;
    }

    /**
     * 商品循环
     * $jiesuan_status:1结算页面中
     * $is_list:1列表中数据
     */
    public function foreach_list($ve,$jiesuan_status=0){
        if($jiesuan_status!=1) {
            $ve['content'] = preg_replace('/(<img.+?src=")(.*?)/', '$1'.config('index_url').'$2', $ve['content']);
        }else{
            unset($ve['content']);
        }
        $ve['imgurl']     = get_image($ve['icon']);

        return $ve;
    }


    /**
    *支付成功界面相似商品
     */
    public function same_product($post){
        $where['p.type1'] = array('in',$post['type_arr']);
        $num = !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;
        $list = db('product p')->where($where)->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->foreach_list($ve);
        }
        return array('code'=>200,'msg'=>'成功！','data'=>$list);
    }





    /**
     * 商品详情
     * product_id:商品id
     */
    public function product_details($post){
        $post['uid'] = !empty($post['uid']) ? $post['uid'] : 0;

        $where['id'] = array('eq',$post['product_id']);
        $vo = db('product')->where($where)->find();
        if(empty($vo)){
            return array('code'=>201,'msg'=>'非法操作，商品不存在！');
        }
       // $vo['content'] = preg_replace('/(<img.+?src=")(.*?)/','$1'.config('index_url').'$2', $vo['content']);
        $vo['content_imgarr'] = imgArr($vo['content_img']);
        $vo['data_json'] = json_decode($vo['data_json'],true);
		$vo['is_collect'] = $this->isHaveCollect($vo['id'],$post['uid'],1);
        //轮播
        $vo['imgarr'] = imgArr($vo['imgarr']);
        $vo['imgurl'] = get_image($vo['icon']);
        $result['details'] = $vo;

        //规格
        $maps['g.product_id']  = array('eq',$post['product_id']);
        $maps['g.status']      = array('eq',1);
        $gg_list =  db('product_guige g')->join('qyc_spec_type s','g.typeid = s.id')->where($maps)->group('g.typeid')->field('s.id,s.title')->order('g.is_price desc')->select();
        foreach($gg_list as $ks=>$vs){
            $maps['g.typeid']      = array('eq',$vs['id']);
            $son_list = db('product_guige g')->join('qyc_spec_type s','g.typeid = s.id')->where($maps)->order('g.sort asc')->field('g.id,g.gg_title,g.is_price,g.new_price,g.bigdl_price,g.dls_price,g.icon')->select();
            foreach($son_list as $km=>$vm){
                $son_list[$km]['imgurl'] = $vm['is_price']==1 ? get_image($vm['icon']) : '';
            }

            $gg_list[$ks]['son_list'] = $son_list;
        }
        $result['gg_list'] = $gg_list;

        //评论
        $Comment = new CommentModel();
        $arr = ['return_id'=>$vo['id'],'see_type'=>1,'type'=>1,'page'=>1,'num'=>3];
        $result['comment']['count']        = $Comment->comment_list($arr,'count'); //数量
        $result['comment']['comment_list'] = $Comment->comment_list($arr); //列表

        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }



    /**
     * 是否点赞
     */
    public function isHaveClick($reutrn_id,$uid=0,$collect_type=''){
        $where['return_id'] = array('eq',$reutrn_id);
        $where['uid']        = array('eq',$uid);
        $where['collect_type'] = array('eq',$collect_type);
        $vo = db('click_list')->where($where)->find();
        return !empty($vo) ? 1 : 2; //1已点赞，2未点赞
    }

    //点赞数量
	public function click_num($return_id,$collect_type){
        $mppe['return_id'] = array('eq',$return_id);
        $mppe['collect_type'] = array('eq',$collect_type);
        $count = db('click_list')->where($mppe)->count();
        return $count;
    }
	


    /**
     * 点赞
     */
    public function add_click_list($post){
        $where['return_id']   = array('eq',$post['return_id']);
        $where['uid']         = array('eq',$post['uid']);
        $where['collect_type']= array('eq',$post['collect_type']);
        $vo = db('click_list')->where($where)->find();
        if(!empty($vo)){
            $res = db('click_list')->delete($vo['id']);
            $msg = '已取消点赞';
        }else{
            $data['return_id']    = $post['return_id'];
            $data['uid']          = $post['uid'];
            $data['clicked_uid']  = !empty($post['clicked_uid']) ? $post['clicked_uid'] : 0;
            $data['collect_type'] = $post['collect_type'];
            $data['add_time']     = time();
            $res = db('click_list')->insertGetId($data);
            $msg = '点赞成功';
        }
        if($res){
            return array('code'=>200,'msg'=>$msg);
        }else{
            return array('code'=>201,'msg'=>'失败！');
        }
    }


    /**
     * 收藏
     */
    public function collect($post){
        $where['return_id']  = array('eq',$post['return_id']);
        $where['uid']         = array('eq',$post['uid']);
        $where['collect_type']= array('eq',$post['collect_type']);
        $vo = db('collect')->where($where)->find();
        if(!empty($vo)){
            $res2 = db('collect')->where('id',$vo['id'])->delete();
            if($res2){
                return array('code'=>200,'status'=>true,'msg'=>'取消收藏成功！');
            }else{
                return array('code'=>201,'status'=>false,'msg'=>'取消收藏失败！');
            }
        }
        $data['return_id']    = $post['return_id'];
        $data['uid']          = $post['uid'];
        $data['collect_type'] = $post['collect_type'];
        $data['add_time']     = time();
        $res = db('collect')->insert($data);
        if($res){
            return array('code'=>200,'status'=>true,'msg'=>'收藏成功！');
        }else{
            return array('code'=>201,'status'=>false,'msg'=>'收藏失败！');
        }
    }


    /**
     * 收藏列表
     *type: 1商品，2商家
     */
    public function collect_list($post){
        $where['c.uid'] = array('eq',$post['uid']);
        $where['c.collect_type'] = array('eq',$post['collect_type']);
        $num = 20;
        $page = $post['page']>1 ? ($post['page']-1)*$num : 0;
        $list = [];
        if($post['collect_type']==1){
            $where['p.status'] = array('eq',1);
            $list = db('collect c')->join('product p','p.id=c.return_id')->where($where)->field('p.*,c.id as collect_id')->group('p.id')->limit($page,$num)->select();
            foreach ($list as $ke=>$ve){
                $list[$ke] = $this->foreach_list($ve);
            }
        }else if($post['collect_type']==2){
            $where['p.status'] = array('eq',1);
            $list = db('collect c')->join('shop p','p.id=c.return_id')->where($where)->field('p.*,c.id as collect_id')->order('c.id desc')->limit($page,$num)->select();
            $post['cx_type'] = 1;
            foreach ($list as $ke=>$ve){
                $list[$ke] = model('Shop')->shop_foreach($ve,$post);
            }
        }

        return array('code'=>200,'msg'=>'成功！','data'=>$list);
    }


    /**
     * 删除收藏
     */
    public function del_collect($post){
        $where['uid']           = array('eq',$post['uid']);
        $where['collect_type']  = array('eq',$post['collect_type']);
        $where['id']            =  array('in',$post['collect_id']);
        $res = db('collect')->where($where)->delete();
        if($res){
            return array('msg'=>'删除成功','code'=>200);
        }else{
            return array('msg'=>'删除失败','code'=>201);
        }
    }

    /**
     * 是否收藏
     */
    public function isHaveCollect($reutrn_id,$uid=0,$collect_type=''){
        $where['return_id'] = array('eq',$reutrn_id);
        $where['uid']        = array('eq',$uid);
        $where['collect_type'] = array('eq',$collect_type);
        $vo = db('collect')->where($where)->find();
        return !empty($vo) ? 1 : 2; //1已收藏，2未收藏
    }


}
