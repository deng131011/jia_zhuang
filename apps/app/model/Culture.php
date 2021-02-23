<?php

namespace app\app\model;
use think\Db;
use think\Model;
class Culture extends Model {


    /**
     *文章列表
     * type:1服务助手首页滚动通知，2园区介绍，3精彩放送，4帮助，5安全
     *
     */
    public function article_list($post){

        if($post['type']==2 || $post['type']==5){
            //单页
            if($post['type']==2){
                $id = 82;
            }else if($post['type']==5){
                $id = 81;
            }
            $vo = db('category')->find($id);
			if(!empty($vo)){
				$vo['imgurl'] = get_image($vo['icon']);
			}
            $result['type'] = $post['type'];
            $result['info'] = $vo;
            return array('code'=>200,'msg'=>'成功','data'=>$result);
        }else{
            //列表页
            if($post['type']==1){
                $map['typeid'] = array('eq',97);

                $result['banner'] = getAdImages(1,'',2); //商家推荐轮播广告
            }else if($post['type']==3){
                $map['typeid'] = array('eq',84);
                
            }else if($post['type']==4){
				if(!empty($post['typeid'])){
					$map['typeid'] = array('eq',$post['typeid']);
                    //return array('code'=>201,'msg'=>'请选择分类');;
                }
			}
        }

        $map['status'] = array('eq',1);
        $num =  !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = db('article')->where($map)->order('sort desc,create_time desc')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl']     = get_image($ve['icon']);
            $list[$ke]['create_date']  = mydate(strtotime($ve['create_time']),2);
        }
        $result['type'] = $post['type'];
        $result['list'] = $list;
        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }



    /**
     *文章详情
     */
    public function article_details($post){
        db('article')->where('id',$post['article_id'])->setInc('views');//增加浏览量

        $vo = db('article')->find($post['article_id']);
		if(!empty($vo)){
            $vo['create_date']  = mydate(strtotime($vo['create_time']),2);
            $vo['content'] = preg_replace('/(<img.+?src=")(.*?)/','$1'.config('index_url').'$2', $vo['content']);
            $vo['imgurl'] = !empty($vo['icon']) ? get_image($vo['icon']) : '';
		}
        $result['details'] = $vo;
        return $result;
    }


    /**
     *活动列表
     */
    public function activit_list($post){
        $post['uid'] = !empty($post['uid']) ? $post['uid'] : 0;

        $map['a.status'] = array('eq',1);
        if(!empty($post['dates'])){
            $map['a.dates']  = array('eq',$post['dates']);
        }else{
            $map['a.dates']  = array('egt',date('Y-m-d'));
        }

        $num =  !empty($post['num']) ? $post['num'] : 20;
        $page = $post['page']>1?($post['page']-1)*$num:0;

        if(!empty($post['my_type']) && $post['my_type']==1){
            $map['c.collect_type']  = array('eq',3);
            $map['c.uid']           = array('eq',$post['uid']);
            $list = db('activit a')->join('collect c','a.id = c.return_id')->where($map)->order('a.sort desc,a.id desc')->field('a.*,c.id as collect_id')->limit($page,$num)->select();
        }else{
            $list = db('activit a')->where($map)->order('sort desc,id desc')->limit($page,$num)->select();
        }


        foreach ($list as $ke=>$ve){
            $list[$ke]['imgurl']     = get_image($ve['icon']);
            if(empty($post['my_type']) || $post['my_type']!=1){
                $mpert['uid']          = array('eq',$post['uid']);
                $mpert['collect_type'] = array('eq',3);
                $mpert['return_id']    = array('eq',$ve['id']);
                $vores =  db('collect')->where($mpert)->find();
                $list[$ke]['is_guanzhu']     = !empty($vores) ? 1 : 2; //1已关注
            }

        }
        return $list;
    }


    /**
     *7天的日期
     */
    public function seven_days(){
        $weekarray=array("日","一","二","三","四","五","六");
        for($i=0;$i<7;$i++){
            $dates[$i]['dates'] = date('Y-m-d',strtotime( '+' . $i .' days',time()));
            $dates[$i]['days']  = date('d',strtotime( '+' . $i .' days',time()));
            $dates[$i]['week']  = $weekarray[date('w',strtotime( '+' . $i .' days',time()))];

            $map['status'] = array('eq',1);
            $map['dates']  = array('eq',$dates[$i]['dates']);
            $count = db('activit')->where($map)->count();
            $dates[$i]['have_status'] = $count>0 ? 1 : 2;//1有活动，2没有
        }
        return $dates;
    }



    /**
     *互动赞消息
     */
    public function zan_msg($post,$count=''){
        if(!empty($post['see']) && $post['see']==1){
            $where['c.is_see'] = array('eq',0);
        }
        $where['c.clicked_uid'] = array('eq',$post['uid']);
        if($count=='count'){
            $listcount = db('click_list c')->where($where)->count();
            return $listcount;
        }

        db('click_list c')->where($where)->update(['is_see'=>1]);//更改成已看

        $users = db('usermember')->find($post['uid']);

        $num = 20;
        $page = (!empty($post['page']) && ($post['page']>1))?($post['page']-1)*$num:0;
        $list = db('click_list c')->join('usermember u','u.id = c.uid')->field('c.*,u.username,u.head_icon,u.wxheadimg')->where($where)->order('c.id desc')->limit($page,$num)->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['headimg']     = head_img_url($ve['head_icon'],$ve['wxheadimg']);
            $list[$ke]['create_date'] = mydate($ve['add_time'],2);

            if($ve['collect_type']==1){
                $tie=db('invitation')->where('id',$ve['return_id'])->field('id,icon,title')->find();
                if(!empty($tie)){
                    $tie['icon_url']=get_image($tie['icon']);
                    $tie['name']    =$users['username'];
                }
                $list[$ke]['tie_info'] = $tie;
            }elseif($ve['collect_type']==2){
                $comment=db('comment')->where('id',$ve['return_id'])->field('id,content,imgarr,return_id')->find();
                if(!empty($comment)){
                    $comment['imgarr'] = imgArr($comment['imgarr']);
                    $comment['name'] = $users['username'];
                }
                $list[$ke]['comment_info'] = $comment;
            }
        }
        return $list;
    }



    /**
     *互动评价消息
     */
    public function shops_msg($post,$count=''){
        if(!empty($post['see']) && $post['see']==1){
            $where['c.is_see'] = array('eq',0);
        }
        $where['c.type']=array('eq',3);
        $where['c.status']    = array('eq',1);
        $where['c.reply_uid'] = array('eq',$post['uid']);
        if($count=='count'){
            $listcount = db('comment c')->where($where)->count();
            return $listcount;
        }

        db('comment c')->where($where)->update(['is_see'=>1]);//更改成已看


        $users = db('usermember')->find($post['uid']);

        $num = 20;
        $page = $post['page']>1?($post['page']-1)*$num:0;
        $list = db('comment c')->join('usermember u','u.id = c.uid')->field('c.*,u.username,u.head_icon,u.wxheadimg')->where($where)->order('c.id desc')->limit($page,$num)->select();

        foreach ($list as $ke=>$ve){
            $list[$ke]['headimg'] = head_img_url($ve['head_icon'],$ve['wxheadimg']);
            $list[$ke]['create_date'] = mydate($ve['create_time'],2);
            if($ve['pid']==0){
                $tie=db('invitation')->where('id',$ve['return_id'])->field('id,icon,title')->find();
                if(!empty($tie['icon'])){
                    $tie['icon_url']=get_image($tie['icon']);
                    $tie['name'] = $users['username'];
                }
                $list[$ke]['tie_info'] = $tie;
            }else{
                $comment=db('comment')->where('id',$ve['pid'])->field('id,content,return_id,imgarr')->find();
                if(!empty($comment)){
                    $comment['imgarr'] = imgArr($comment['imgarr']);
                    $comment['name']   = $users['username'];
                }
                $list[$ke]['comment_info']=$comment;
            }
        }
        return $list;
    }




}
