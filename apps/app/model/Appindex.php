<?php

namespace app\app\model;

use think\Cache;
use think\Db;
use think\Model;
use app\app\model\Product as ProductModel;
class Appindex extends Model {





    /**
      * APP首页
      * */
    public function appIndex(){

        //banner图
        $result['banner'] = $this->banner();
        //标签
        $tab = config('index_tab_string');
        $result['tab_title'] = explode('&',$tab);

        $ProductModel = new ProductModel();
        //爆款推荐
        $result['baokuan_list'] = $ProductModel->product(['flag'=>1,'page'=>1,'num'=>3]);

        //推荐二级分类
        $result['tjtype_list'] = $ProductModel->product_type(['tj_type'=>1]);

        //产品推荐
        $result['tj_list'] = $ProductModel->product(['flag'=>2,'page'=>1,'num'=>10]);
        return $result;
    }

    /**
     * APP首页Banner图
     */
   public function banner(){
       $banner = getAdImages(3,'',2);
       return $banner;
   }



    /**
     * 热门搜索
     */
    public function hot_search($post){
        $where['status'] = array('eq',1);
        $num = 10;
        $page = $post['page']>1 ? ($post['page']-1)*$num : 0;
        $list = db('hot_search')->where($where)->order('id desc')->limit($page,$num)->select();
        return array('msg'=>'成功','code'=>200,'data'=>$list);
    }






}
