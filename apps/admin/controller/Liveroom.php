<?php

namespace app\admin\controller;

use eacoo\Tree;
use think\Db;
use think\Request;

/**
 * 直播
 */
class Liveroom extends Admin
{
    function _initialize()
    {
        parent::_initialize();
        $this->model = model('Liveroom');
    }

    public function index()
    {
        $get = $_GET;
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $get['status']     = !empty($get['status'])? $get['status'] : -1;
        $get['recommend']     = !empty($get['recommend'])? $get['recommend'] : '';
        $get['category_id']     = !empty($get['category_id'])? $get['category_id'] : '';
        $this->assign('get',$get);
        $map = array();
        if (!empty($get['keyword'])) {
            $map['i.title'] = array('like', '%' . trim($get['keyword']) . '%');
        }

        if (!empty($get['recommend'])) {
            $map['i.flag'] = array('eq', $get['recommend']);
        }
        //$map['i.status'] = array('eq', 2);

        $data_list = Db::table('qyc_live_room')->alias('i')
            ->where($map)
            ->order('i.id desc')
            ->paginate(20,false,['query'=>request()->param()])
            ->each(function ($item,$key) {
                return $this->foreach_list($item);
            });

        $this->assign('list', $data_list);
        $this->assign('meta_title', '直播列表');
        return $this->fetch();

    }

    //数据处理
    public function foreach_list($item)
    {

        return $item;
    }



    /**
     * 编辑
     */
    public function edit($id = 0)
    {
        $title = $id > 0 ? "编辑" : "新增";
        $info = array();
        if (IS_POST) {
            $post = input('param.');

            //修改类容
            $res = model('Liveroom')->checkform($post);
            if($res['code']==201){
                $this->error($res['msg']);
            }
            $data = $res['data'];
            $data['create_time']=time();
            if ($this->model->editData($data)) {
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->model->getError());
            }


        } else {
            $info = Db::table('qyc_live_room')->where('id', $id)->find();
            if(!empty($info)){
                $info['start_time'] = $info['start_time'].'-'.$info['over_time'];
            }
            $this->assign('meta_title', $title);
            $this->assign('info', $info);
            return $this->fetch();

        }
    }

}