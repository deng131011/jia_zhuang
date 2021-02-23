<?php

namespace app\app\model;
use think\Db;
use think\Exception;
use think\Model;
class Invitation extends Model {
    protected $table = 'qyc_invitation';

    //发帖
    public function set_invitation($data,$uid){

        Db::startTrans();
        try{

            //数据拼凑
            $value['title']=$data['title'];
            $value['uid']=$uid;
            if($data['status']==1){
                $value['status']=0;
            }else{
                $value['status']=$data['status'];//1 发帖， 2存入草稿箱
            }
            $value['category_id']=$data['category_id'];
            $value['create_time']=time();
            $value['update_time']=time();
            $value['imgarr']=!empty($data['imgarr'])?$data['imgarr']:"";
            $value['icon']=!empty($data['icon'])?$data['icon']:0;
            $value['content']=!empty($data['content'])?$data['content']:"";
            $value['video']=!empty($data['video'])?$data['video']:"";
            $value['product_id']=!empty($data['product_id'])?$data['product_id']:"";
            $value['invitation_type']=!empty($data['invitation_type'])?$data['invitation_type']:1;
            if ($data['status']==1){//正常发帖
                //查看有没有草稿箱
                $map['uid']   =$uid;
                if(!empty($data['invitation_id'])){
                    $map['id']    =$data['invitation_id'];
					$map['status']=2;//草稿箱
					$f=Db::table('qyc_invitation')->where($map)->find();
					if ($f){
						Db::table('qyc_invitation')->where($map)->update(array('status'=>0,'update_time'=>time()));         
						$father_id=$f['id'];
					}
                }else{
                    $father_id=Db::table('qyc_invitation')->insertGetId($value);
                    //$s=$this->set_data($data['list'],$father_id);
                    //Db::table('qyc_invitation_son')->insertAll($s);
                }
                /*$socre=config('publish_msg_score')?config('publish_msg_score'):0;
                //发布动态添加积分
                model('Score')->duty_get_score($uid,7,$socre,'发布动态',$father_id);*/
            }else{//保存到草稿箱
                $father_id=Db::table('qyc_invitation')->insertGetId($value);
                //$s=$this->set_data($data['list'],$father_id);
                //Db::table('qyc_invitation_son')->insertAll($s);
            }
            // 提交事务
            Db::commit();
            return array('code'=>200,'msg'=>'提交成功！','data'=>$father_id);

        }catch (\Exception $e){
            // 回滚事务
            Db::rollback();
            return array('code'=>201,'msg'=>'网络连接失败，请稍后再试');
        }



    }

    //拼凑子数据
    public function set_data($data,$father_id){
        $value=[];
        if ($data){
            foreach ($data as $k=>$v){
                $icon=isset($v['icon'])?$v['icon']:0;
                $video=isset($v['video'])?$v['video']:0;
                $content=isset($v['content'])?$v['content']:'';
                $value[$k]['type']=$v['type'];
                $value[$k]['invitation_id']=$father_id;
                $value[$k]['content']=$content;//文字
                $value[$k]['icon']=$icon;//图片
                $value[$k]['video']=$video;//图片
                $value[$k]['invitation_id']=$father_id;
            }
        }

        return $value;

    }

    //删帖
    public function del_invitation($param){
        $where['uid']=array('eq',$param['uid']);
        $where['id']=array('eq',$param['id']);
        $rst=db('invitation')->where($where)->delete();
        if($rst){
            return array('code'=>200,'msg'=>'删除成功');
        }else{
            return array('code'=>201,'msg'=>'删除失败');
        }
        
    }

    //获取帖子分类

    public function get_category(){

       $map['status']=1;
       $map['pid']=8;
       $category=Db::table('qyc_category')->where($map)
           ->order('sort asc,id asc')
           ->field('id,title')
           ->select();
       return array('code'=>200,'msg'=>'请求成功','data'=>$category);
    }
    //获取帖子
    public function get_invitation($category_id,$uid=0,$page=1,$type=1,$param=[]){

        $map['i.status']=array('eq',1);
        if ($category_id==-1){
            //关注
            $where['uid']=$uid;
            $where['type']=1;
            $follow=Db::table('qyc_follow')->where($where)->column('target_id');
            if (!empty($follow)){
                $map['i.uid']=array('in',$follow);
            }else{
                $map['i.uid']=array('eq',0);//就是没有关注的会员
            }

        }elseif($category_id==-2){
            //推荐
            $map['i.recommend']=array('eq',1);
        }elseif($category_id==-3){
            $map['i.uid']=array('eq',$uid);
            $map['i.status']=array('eq',2);
        }else{
            $map['i.category_id']=array('eq',$category_id);
        }
        if(!empty($param['keywords'])){
            $map['i.title']=array('like','%'.$param['keywords'].'%');
        }
       

        $t=Db::table('qyc_usermember')->alias('u')
            ->join('qyc_invitation i','i.uid=u.id','RIGHT')
            ->where($map)
            ->order('i.top asc,i.create_time desc')
            ->field('i.*,u.username,u.mobile,u.head_icon')
            ->page($page,20)
            ->select();
        if ($t){
            $followModel=new Follow();
            foreach ($t as $k=>$v){
                // /$t[$k]['list']=$this->invitation_son($v);
                $t[$k]['icon_url']=$v['icon']?get_image($v['icon']):"";
                $image_info=$v['icon']?getimagesize($t[$k]['icon_url']):[];
                $t[$k]['icon_url_info']=$image_info;
                $t[$k]['icon_width']=$image_info[0];
                $t[$k]['icon_height']=$image_info[1];
                $t[$k]['head_icon_url']=$v['head_icon']?get_image($v['head_icon']):"";
                $t[$k]['username']= $v['username'] !='' ? $v['username'] : substr_replace($v['mobile'],'****',3,4);//会员昵称
            }
        }
        $count=Db::table('qyc_usermember')->alias('u')
            ->join('qyc_invitation i','i.uid=u.id','RIGHT')
            ->where($map)
            ->count();
        @$total_page=ceil($count/20);//总页码

        //未读消息数量
        $map['uid']=$uid;
        $map['see']=1;

        $no_see['no_see_zan']=model('culture')->zan_msg($map,'count');//赞列表
        $no_see['no_see_comment']=model('culture')->shops_msg($map,'count');
        $no_see['no_see_system']=model('culture')->system_msg($map,'count');
        $no_see['unread_msg_num'] = $no_see['no_see_zan']+$no_see['no_see_comment']+$no_see['no_see_system'];

        return array('page'=>$page,'total_page'=>$total_page,'list'=>$t,'count'=>$count,"no_see"=>$no_see);
    }

    //拼凑帖子的数据
    public function invitation_son($t){
        $son=[];
        if ($t){
            $map['invitation_id']=$t['id'];
            $son=Db::table('qyc_invitation_son')->where($map)->select();
            if ($son){
                foreach ($son as $k=>$v){
                    if ($v['type']==2){
                        $son[$k]['icon_path']=get_image($v['icon']);
                    }
                }
            }
        }

        return $son;

    }

    //获取帖子图片
    public function invitation_icon($t){
        $s=[];
        $i=0;
        if ($t){
            $map['invitation_id']=$t['id'];
            $son=Db::table('qyc_invitation_son')->where($map)->select();
            if ($son){
                foreach ($son as $k=>$v){
                    if ($v['type']==2){
                        $s[$i]['icon_path']=get_image($v['icon']);
                        ++$i;
                    }
                }
            }
        }

        return $s;
    }

    //获取帖子详情
    public function invitation_details($id,$uid=0){

        $map['i.id']=$id;
        //$map['i.status']=1;
        $t=Db::table('qyc_usermember')->alias('u')
            ->join('qyc_invitation i','i.uid=u.id','RIGHT')
            ->where($map)
            ->field('i.*,u.username,u.mobile,u.head_icon,u.id as uid')
            ->find();

        if ($t){
            $t['username']=  $t['username'] !='' ? $t['username'] : substr_replace($t['mobile'],'****',3,4);//会员昵称
            $t['category_title']=modelField($t['category_id'],'category','title');
            $t['create_date']=date('Y-m-d H:i',$t['create_time']);
            $t['head_icon_url']=head_img_url($t['head_icon'],'');
            $t['imgarr_url']=$t['imgarr']?imgArr($t['imgarr']):[];
            $t['video_url']=$t['video']?get_image($t['video']):"";
            $t['member_follow_status']=(new Follow())->get_follow_status(1,$t['uid'],$uid);//关注状态
            $t['invitation_fabulous_status']=(new Follow())->get_follow_status(2,$t['id'],$uid);//点赞状态
            $t['invitation_fabulous_num']=(new Follow())->get_follow_num(2,$t['id']);//点赞数量
            $t['invitation_collect_num']=(new Follow())->get_follow_num(3,$t['id']);//收藏数量
            $t['invitation_collect_status']=(new Follow())->get_follow_status(3,$t['id'],$uid);//点赞状态;//收藏状态

            $comment['uid']=$uid;
            $comment['type']=2;
            $whe['return_id']=$id;
            $whe['pid']=0;
            $t['comment']=(new Comment())->comment_list($comment,'',$whe);//帖子评论
            $t['comment_num']=(new Comment())->comment_list($comment,'count',$whe);//帖子评论数量
            if($t['product_id']){
                $product=db('product')->where('id',$t['product_id'])->field('id,icon,title,new_price')->find();

                if($product){
                    $product['icon_url']=get_image($product['icon']);
                }
                $t['product_info']=$product?$product:[];
            }else{
                $t['product_info']=(object)[];
            }
            $t['is_self']=($uid==$t['uid'])?1:2;

            //更改浏览量
            Db::table('qyc_invitation')->where('id',$id)->setInc('browse',1);

            return array('code'=>200,'msg'=>'请求成功','data'=>$t);

        }else{
            return array('code'=>201,'msg'=>'帖子已被删除');
        }
    }

}
