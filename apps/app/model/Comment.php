<?php

namespace app\app\model;
use think\Db;
use think\Model;
use app\app\model\Product as ProductModel;
class Comment extends Model {



    /**
     *添加单个评价
     * type:1商品  2课程，3直播，4题库
     **/
    public function add_one_comment($post){

        Db::startTrans();
        try{
            $data['uid']            = $post['uid'];
            $data['type']           = $post['type'];
            $data['return_id']      = $post['return_id'];
            $data['content']        = $post['content'];
            $data['imgarr']         = !empty($post['imgarr']) ? $post['imgarr'] : '';
            $data['order_id']       = !empty($post['order_id']) ? $post['order_id'] : 0;
            $data['good_type']      = !empty($post['good_type']) ? $post['good_type'] : 1;
            $data['create_time']    = time();
            $res = db('comment')->insertGetId($data);

            if ($post['type'] == 1) {
                $map['product_id'] = array('eq', $post['return_id']);
                $map['order_id']   = array('eq', $post['order_id']);
                db('order_list')->where($map)->update(['is_comment' => 1]);

                //判断是否有未评价的商品
                $waap['order_id']     = array('eq', $post['order_id']);
                $waap['after_status'] = array(array('eq', 0), array('eq', 3), 'or');
                $waap['is_comment']   = array('eq', 0);
                $vos = db('order_list')->where($waap)->find();
                if (empty($vos)) {
                    db('order')->where('id', $post['order_id'])->update(['order_status' => 3]);
                }

            }
            Db::commit();
            $result['comment_id'] = $res;
            return array('code' => 200, 'msg' => '提交成功', 'data' => $result);
        }catch (\Exception $e){
            Db::rollback();
            return array('code'=>201,'msg'=>'提交失败');
        }
    }

    /**
     *评论列表
     * $count='count'时查询数量
     * see_type:1商品详情中，2个人中心
     * $whe:附加条件
     */
    public function comment_list($post,$count='',$whe=array()){
        $post['uid'] = !empty($post['uid']) ? $post['uid'] : 0;

        if(!empty($post['return_id'])){
            $where['c.return_id'] = array('eq',$post['return_id']);
        }

        $where['c.type']         = array('eq',$post['type']);
        $where['c.pid']          = array('eq',0);
        $where['c.status']       = array('eq',1);
        //$where['c.check_status']       = array('eq',1);
        if(!empty($whe)){
            $where = array_merge($where,$whe);
        }
        if($count=='count'){
            $listCount = db('comment c')->join('usermember u','u.id = c.uid')->field('count(*) as counts,sum(star) as star')->where($where)->find();
            $listCount['star'] = !empty($listCount['star']) ? $listCount['star'] : 0;
            return $listCount; //数量
        }

        $num = !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;

        $list = db('comment c')->join('usermember u','u.id = c.uid')->where($where)->field('u.nickname,u.mobile,u.head_icon,u.wxheadimg,c.*')->order('c.id desc')->limit($page,$num)->select();

        foreach ($list as $ke=>$ve){
            $list[$ke] = $this->foreach_comment($ve,$post['uid']);
        }
        return $list; //列表
    }




    /**
    *评论公共信息
     **/
    public function foreach_comment($ve,$uid=0){
        //$ve['username']    = !empty($ve['username']) ? substr_cuttitle($ve['username']) : '';
		
        $ve['mobile']      = mobile_four_star($ve['mobile']);
        $ve['head_img']    = head_img_url($ve['head_icon'],$ve['wxheadimg']);
        $ve['imgarr']      = imgArr($ve['imgarr']);
        $ve['create_date'] = mydate($ve['create_time'],2);

        //评论数量（回复数量）
        $mpes['pid']    = array('eq',$ve['id']);
        $mpes['status'] = array('eq',1);
        $ve['recomment_count'] = db('comment')->where($mpes)->count();

        //点赞数量
        $ve['click_count'] = model('Product')->click_num($ve['id'],2);
        //是否点赞
        $ve['click_status'] = model('Product')->isHaveClick($ve['id'],$uid,2); //1已点赞，2未点赞

        $ve['is_self'] = $ve['uid']==$uid ? 1 : 2;//1是自己，2不是

        return $ve;
    }


    /**
     *评价详情
     **/
    public function comment_details($post){
        $where['c.id'] = array('eq',$post['comment_id']);
        $where['c.status'] = array('eq',1);
        $vo = db('comment c')->join('usermember u','u.id = c.uid')->where($where)->field('u.username,u.mobile,u.head_icon,u.wxheadimg,c.*')->find();
        $vo = $this->foreach_comment($vo);

        $ProductModel = new ProductModel();
        $click = $ProductModel->isHaveClick($vo['id'],$post['uid'],$vo['type']);
        $vo['click_status'] = !empty($click) ? 1 : 0 ;//1已点赞，0没有
        $result['info'] = $vo;

        //评论列表
        $result['list'] = $this->digui_commentb($vo['id'],1,$post['uid']);
        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }
    /**
     *添加回复
     **/
    public function add_reply($post){

        if(!empty($post['token'])){
            unset($post['token']);
        }

        $post['create_time'] = time();
        $post['reply_uid']  = intval($post['reply_uid']);
        $res = db('comment')->insertGetId($post);
        if($res){
            $post['insert_id'] = $res;
            $post['id'] = $res;
            $user = db('usermember')->field('username,mobile,head_icon')->find($post['uid']);
            if(!empty($user)){
                $user['head_img'] = get_image($user['head_icon']);
            }
            $post['userinfo'] = $user;
            $post['create_date'] = mydate($post['create_time'],2);
            return array('code'=>200,'msg'=>'回复成功','data'=>$post);
        }else{
            return array('code'=>201,'msg'=>'回复失败');
        }
    }


    /**
     *删除自己的评价
     **/
    public function delmy_comment($post){
        $where['uid'] = array('eq',$post['uid']);
        $where['type'] = array('eq',$post['type']);
        $where['id'] = array('eq',$post['comment_id']);
        $res = db('comment')->where($where)->update(['status'=>-1,'update_time'=>time()]);
        if($res){
            return array('code'=>200,'msg'=>'删除成功');
        }else{
            return array('code'=>201,'msg'=>'删除失败');
        }
    }


    /**
     *查看订单的评价
     **/
    public function order_comment($post){

        $where['c.pid']       = array('eq',0);
        if(!empty($post['comment_id'])){
            $where['c.id']      = array('eq',$post['comment_id']);
        }else{
            $where['c.type']      = array('eq',$post['type']);
            $where['c.order_id']  = array('eq',$post['order_id']);
            $where['c.return_id'] = array('eq',$post['return_id']);
        }
        $vo = db('comment c')->join('usermember u','c.uid = u.id')->where($where)->field('c.*,u.username,u.head_icon,u.wxheadimg,u.mobile')->find();
        $return_info = [];
        if(!empty($vo)){
            $vo['head_img']    = head_img_url($vo['head_icon'],$vo['wxheadimg']);
            $vo['imgarr']      = imgArr($vo['imgarr']);
            $vo['create_date'] = mydate($vo['create_time'],1);
			$vo['mobile'] = substr_replace($vo['mobile'], '****', 3, 4);

            if($vo['type']==2){
                $shop = db('shop')->find($post['return_id']);
                $shop['imgurl'] = get_image($shop['icon']);
                $shop['is_collect'] = model('Product')->isHaveCollect($post['return_id'],$post['uid'],2);
                $return_info = $shop;
            }
        }
        $result['info'] = $vo;
        $result['return_info'] = $return_info;
        return $result;
    }

    /**
     *递归获取评论
     */
    function digui_commentb($pid,$lev=1,$uid=0){

        $where['c.pid'] = array('eq',$pid);
        $where['c.status'] = array('eq',1);
        $list = db('comment c')->join('usermember u','u.id = c.uid')->where($where)->field('c.*,u.username,u.head_icon,u.mobile,u.wxheadimg')->order('c.create_time desc')->select();
        $arr = array();
        foreach ($list as $k=>$v){
            $v = $this->foreach_comment($v,$uid);
            //是否点赞
            if(!empty($v['reply_uid'])){
                $v['reply_name'] = modelField($v['reply_uid'],'usermember','username');
            }else{
                $v['reply_name'] = '';
            }

            $v['lev']= $lev;
            $arr []= $v;
            $arr = array_merge($arr, $this->digui_commentb($v['id'],$lev+1,$uid));
        }
        return $arr;
    }



    /**
     *我的商家评价
     **/
    public function myshop_comment($post){
        $post['uid'] = !empty($post['uid']) ? $post['uid'] : 0;


        $where['c.type']         = array('eq',2);
        $where['c.pid']          = array('eq',0);
        $where['c.status']       = array('eq',1);
        $where['c.uid']       = array('eq',$post['uid']);
        if(!empty($whe)){
            $where = array_merge($where,$whe);
        }


        $num = !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num : 0;

        $list = db('comment c')->join('usermember u','u.id = c.uid')->where($where)->field('u.username,u.mobile,u.head_icon,u.wxheadimg,c.*')->order('c.id desc')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['head_img']    = head_img_url($ve['head_icon'],$ve['wxheadimg']);
            $list[$ke]['imgarr']      = imgArr($ve['imgarr']);
            $list[$ke]['create_date'] = mydate($ve['create_time'],2);

            $shop = db('shop')->find($ve['return_id']);
            if(!empty($shop)){
                $shop['imgurl'] = get_image($shop['icon']);
                $shop['is_collect'] = model('Product')->isHaveCollect($shop['id'],$post['uid'],2);
            }
            $list[$ke]['shop'] = $shop;
        }
        return $list; //列表
    }

}
