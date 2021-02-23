<?php

namespace app\app\model;
use think\Model;
use think\Cache;
class Address extends Model {
    protected $table = 'qyc_address';

    /**
     *省市区
     * pid:上级id
     */
    public function zone_list($post){
        $where['pid']    = array('eq',$post['pid']);
        $where['status'] = array('eq',1);
        $list = db('china')->where($where)->cache('csq_zone_list_'.$post['pid'],86400)->order('id asc')->select();
        return $list;
    }

    /**
     *省市区全部获取
     * pid:上级id
     */
    public function all_zone_list(){
        $where['pid']    = array('eq',0);
        $where['status'] = array('eq',1);
        $list = db('china')->where($where)->order('id asc')->select();

        foreach ($list as $ke=>$ve){
            $wheret['pid']    = array('eq',$ve['id']);
            $wheret['status']    = array('eq',1);
            $two_list = db('china')->where($wheret)->order('id asc')->select();
            foreach ($two_list as $ks=>$vs){
                $map['pid']    = array('eq',$vs['id']);
                $map['status']    = array('eq',1);
                $three_list = db('china')->where($map)->order('id asc')->select();
                $two_list[$ks]['three_list'] = $three_list;
            }
            $list[$ke]['two_list'] = $two_list;
        }
        return $list;
    }

    /**
     *收货地址详情
     */
    public function zone_details($post){
        $vo = db('address')->find($post['address_id']);

        return array('code'=>200,'msg'=>'成功','data'=>$vo);
    }


    /**
     *添加、编辑收货地址
     */
    public function add_address($post){
        if(empty($post['uid']) || empty($post['token']) ||  empty(trim($post['mobile'])) || empty($post['province_id']) || empty($post['city_id']) || empty(trim($post['username'])) || empty(trim($post['address'])) ){
            return array('code'=>201,'msg'=>'请完善收货信息！');
        }
        $post['is_default'] = !empty($post['is_default']) ? $post['is_default'] : 2;

        $mpt['token'] = array('eq',$post['token']);
        $mpt['id'] = array('eq',$post['uid']);
        $user = db('usermember')->where($mpt)->find();
        if(empty($user)){
            return array('code'=>201,'msg'=>'网络错误，请稍后再试');
        }
        $where['uid'] = array('eq',$post['uid']);
        $vo = db('address')->where($where)->find();
        if(empty($vo)){
            $post['is_default'] = 1;
        }

        $post['pro_zone'] =  provinceCityCounty($post,'zone_title');//省市区名称

        if(!empty($post['id'])){
            $post['update_time'] = mydate(time());
            $res = $this->allowField(true)->save($post,['id'=>$post['id']]);
            $id = $post['id'];
        }else{
            $post['create_time'] = mydate(time());
            $res = $this->allowField(true)->save($post);
            $id = $this->id;
        }

        if($res){
            if($post['is_default']==1){
                $map['uid'] = array('eq',$post['uid']);
                $map['id']  = array('neq',$id);
                $this->where($map)->update(array('is_default'=>2));

            }
            return array('code'=>200,'msg'=>'提交成功');
        }else{
            return array('code'=>201,'msg'=>'提交失败');
        }
    }



    /**
     *收货地址列表
     */
    public function lists($post){
        $where['uid']    = array('eq',$post['uid']);
        $where['status'] = array('eq',1);
        $list = db('address')->where($where)->order('is_default asc,id desc')->select();
        foreach ($list as $ke=>$ve){
            $list[$ke]['xx_address'] = $ve['pro_zone'].$ve['address'];
        }
        return array('code'=>200,'msg'=>'提交成功','data'=>$list);
    }


    /**
     *城市列表
     */
    public function city_list($post=array()){
        //热门城市
        $where['status'] = array('eq',1);
        if(!empty($post['keywords'])){
            //手动输入搜索时
            $where['title'] = array('like','%'.trim($post['keywords']).'%');
            $where['level'] = array(array('eq',2),array('eq',3),'or');
            $result['search_city'] = db('china')->where($where)->select();
            return array('code'=>200,'msg'=>'成功','data'=>$result);
        }else{
            $where['is_hot'] = array('eq',1);
            $result['hot_city'] = db('china')->where($where)->select();
        }
        //其他区县
        $map['level']  = array('eq',3);
        $map['status'] = array('eq',1);
        $list = db('china')->where($map)->cache('other_city',86400)->orderRaw('convert(title using gbk) asc')->select();
        $result['other_city'] = $list;
        return array('code'=>200,'msg'=>'成功','data'=>$result);
    }


    /**
     *删除收货地址
     */
    public function delete_address($post){

        if(empty($post['uid']) || empty($post['address_id'])){
            return array('code'=>201,'msg'=>'缺少参数！');
        }
        $where['uid'] = array('eq',$post['uid']);
        $where['id'] = array('eq',$post['address_id']);
        $res = db('address')->where($where)->delete();
        if($res){
            return array('code'=>200,'msg'=>'删除成功');
        }else{
            return array('code'=>201,'msg'=>'删除失败');
        }
    }


    /**
     *获取经纬度
     */
    public function get_lng_lat(){
        $post = input('param.');
        $address = trim($post['start_addr']);
        $result = file_get_contents('http://restapi.amap.com/v3/geocode/geo?key=f7a3aeb17d5402d577930f469a666131&s=rsv3&address='.$address);
        $res = json_decode($result,true);

        if($res['status'] == 1 && $res['info'] == 'OK' && !empty($res['geocodes'][0]['location'])){
            return array('code'=>200,'msg'=>'成功','data'=>$res['geocodes'][0]['location']);
        }else{
            return array('code'=>201,'msg'=>'经纬度获取失败');
        }
    }


}
