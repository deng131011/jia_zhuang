<?php
// 用户管理控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 https://www.eacoophp.com, All rights reserved.         
// +----------------------------------------------------------------------
// | [EacooPHP] 并不是自由软件,可免费使用,未经许可不能去掉EacooPHP相关版权。
// | 禁止在EacooPHP整体或任何部分基础上发展任何派生、修改或第三方版本用于重新分发
// +----------------------------------------------------------------------
// | Author:  心云间、凝听 <981248356@qq.com>
// +----------------------------------------------------------------------
namespace app\user\admin;
use app\admin\controller\Admin;
use app\common\layout\Iframe;
use app\common\model\User as UserModel;
use app\app\model\Account as AccountModel;
use app\app\model\Vipcard as AppVipcardModel;
use think\Db;
use app\app\model\Address as AddressModel;
class User extends Admin {

    function _initialize()
    {
        parent::_initialize();

        $this->userModel = model('common/User');
    }

    /**
     * 用户列表
     * @return [type] [description]
     * @date   2018-02-05
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function index(){

        $get = $_REQUEST;
        $get['keyword'] = !empty($get['keyword']) ? $get['keyword'] : '';
        $get['dates'] = !empty($get['dates']) ? $get['dates'] : '';
        $get['user_type'] = !empty($get['user_type']) ? $get['user_type'] : '';


        if(!empty($get['keyword'])){
            $condition['username|mobile'] = ['like', '%'.trim($get['keyword']).'%'];
        }
        if(!empty($get['dates'])){
            $dates = explode('/',$get['dates']);
            $condition['reg_time'] = ['between',[$dates[0].' 00:00:00',$dates[1].' 23:59:59']];
        }
        if(!empty($get['user_type'])){
            $condition['user_type'] = ['eq',$get['user_type']];
        }
        $condition['status'] = ['egt', '0']; // 禁用和正常状态



        $data_list =  Db::name('usermember')->where($condition)->order('id desc')->paginate(20);

        $this->assign('list',$data_list);
        $this->assign('meta_title','用户列表');

        $get['dates'] = return_dates($get);
        $this->assign('get',$get);
        return $this->fetch();

        return (new Iframe())
                ->setMetaTitle('用户列表')
                ->search([
                    ['name'=>'reg_time_range','type'=>'daterange','extra_attr'=>'placeholder="注册时间"'],
                    //['name'=>'status','type'=>'select','title'=>'状态','options'=>[1=>'正常',0=>'禁用']],
                  //  ['name'=>'sex','type'=>'select','title'=>'性别','options'=>[0=>'未知',1=>'男',2=>'女']],
                    //['name'=>'is_lock','type'=>'select','title'=>'是否锁定','options'=>[0=>'否',1=>'是']],
                   // ['name'=>'actived','type'=>'select','title'=>'激活','options'=>[0=>'否',1=>'是']],
                    ['name'=>'keyword','type'=>'text','extra_attr'=>'placeholder="请输入查询关键字"'],
                ])
                ->content($this->grid());
    }

    /**
     * Make a grid builder.
     * @return [type] [description]
     * @date   2018-09-08
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function grid()
    {
        //$search_setting = $this->buildModelSearchSetting();
        $get = $_REQUEST;
        $condition = array();

        // 获取所有用户

        if(!empty($get['keyword'])){
            $condition['username|mobile'] = ['like', '%'.trim($get['keyword']).'%'];
        }
        $condition['status'] = ['egt', '0']; // 禁用和正常状态
        $condition['user_type'] = ['not in', '3,4']; // 禁用和正常状态

        list($data_list,$total) = $this->userModel->getListByPage($condition,true,'reg_time desc');
        //echo $this->userModel->getLastSql();exit;
        $reset_password = [
            'icon'         => 'fa fa-recycle',
            'title'        => '重置原始密码',
            'class'        => 'btn btn-default ajax-table-btn confirm btn-sm',
            'confirm-info' => '该操作会重置用户密码为123456，请谨慎操作',
            'href'         => url('resetPassword')
        ];

        return builder('list')
                ->setMetaTitle('用户列表') // 设置页面标题
                //->addTopButton('addnew')  // 添加新增按钮
                ->addTopButton('resume')  // 添加启用按钮
                ->addTopButton('forbid')  // 添加禁用按钮
                ->addTopButton('delete')  // 添加删除按钮

                //->setSearch('custom','请输入ID/用户名/昵称')
                ->setActionUrl(url('grid')) //设置请求地址
                ->keyListItem('id', 'UID')
                ->keyListItem('username', '姓名')
                ->keyListItem('head_icon', '头像', 'picture',['style'=>'width:50px; height:50px;'])
                ->keyListItem('user_type', '身份','array',['1'=>'学生','2'=>'家长'])
                ->keyListItem('sex', '性别')
                ->keyListItem('birthday', '生日')
                ->keyListItem('mobile', '手机号')
                ->keyListItem('reg_time', '注册时间')
                ->keyListItem('status', '状态','status')
                ->keyListItem('right_button', '操作', 'btn')
                ->setListPrimaryKey('id')
                ->setListData($data_list)    // 数据列表
                ->setListPage($total) // 数据列表分页
                ->addRightButton('edit')

                //->addRightButton('forbid')  // 添加编辑按钮
                ->fetch();
    }

    /**
     * 编辑用户
     */
    public function edit($id = 0) {
        $title = $id ? "编辑" : "新增";
        if (IS_POST) {
            $data = input('param.');

            // 密码为空表示不修改密码
            if($data['id']>0){
                if ($data['password'] == '') {
                    unset($data['password']);
                }
            }else{
                if ($data['password'] == '') {
                    $data['password']='123456';
                }
            }
            if(empty($data['nickname'])){
                $this->error('昵称不能为空');
            }
            if(empty($data['mobile'])){
                $this->error('手机号不能为空');
            }
            $where['mobile'] = array('eq',trim($data['mobile']));
            if(!empty($data['id'])){
                $where['id'] = array('neq',$data['id']);
            }
            $vo = db('usermember')->where($where)->find();
			
            if(!empty($vo)){
                $this->error('该手机号已经存在');
            }


            $uid  = isset($data['id']) && $data['id']>0 ? intval($data['id']) : false;

            /*if ($uid>0) {
                $this->validateData($data,'User.edit');
            } else{
                $this->validateData($data,'User.add');
            }*/

            
            // 提交数据
            //$data里包含主键id，则editData就会更新数据，否则是新增数据
            $result = $this->userModel->editData($data);

            if ($result) {
                if(empty($data['id'])){
                    $accountModel = new AccountModel();
                    $uid = $this->userModel->id;
                    $path = $accountModel->small_ewm(['id'=>$uid],1);
                    db('usermember')->where('id',$uid)->update(['ewm_url'=>$path]);
                }
                if ($uid==is_login()) {//如果是编辑状态下
                    logic('common/User')->updateLoginSession($id);
                }
                $this->success($title.'成功', url('index'));
            } else {
                $this->error($this->userModel->getError());
            }
        } else {

            return (new Iframe())
                    ->setMetaTitle($title.'用户')
                    ->content($this->form($id));

        }
    }

    /**
     * 表单构建
     * @param  integer $uid [description]
     * @return [type] [description]
     * @date   2018-10-03
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function form($uid = 0)
    {
        $info = [
            'sex'     =>0,
            'user_type'     =>'1',
            'is_lock' =>0,
            'status'  =>1
        ];
        // 获取账号信息
        if ($uid>0) {
            $info = $this->userModel->get($uid);
            unset($info['password']);
        }

        return builder('Form')
                    ->addFormItem('id', 'hidden', 'UID', '')
                    ->addFormItem('nickname', 'text', '用户昵称', '填写一个有个性的昵称吧','','require')
                    //->addFormItem('username', 'text', '用户名', '登录账户所用名称','','require')
                    ->addFormItem('password', 'password', '用户密码', '新增默认密码123456','','placeholder="留空则不修改密码"')
                    //->addFormItem('email', 'email', '邮箱', '','','data-rule="email" data-tip="请填写一个邮箱地址"')
                    ->addFormItem('mobile', 'left_icon_number', '手机号码', '',['icon'=>'<i class="fa fa-phone"></i>'],'placeholder="填写手机号"')

                    ->addFormItem('sex', 'radio', '用户性别', '',['男'=>'男','女'=>'女'])
                    ->addFormItem('status', 'radio', '禁用状态', '',[1=>'正常',0=>'禁用'])

                    ->addFormItem('head_icon', 'picture', '用户头像')
                    ->setFormData($info)//->setAjaxSubmit(false)
                    ->addButton('submit')
                    ->addButton('back')    // 设置表单按钮
                    ->fetch();
    }
    
    /**
     * 构建模型搜索查询条件
     * @return [type] [description]
     * @date   2018-09-30
     * @author 心云间、凝听 <981248356@qq.com>
     */
    private function buildModelSearchSetting()
    {
        //时间范围
        $timegap = input('reg_time_range');
        $extend_conditions = [];
        if($timegap){
            $gap = explode('—', $timegap);
            $reg_begin = $gap[0];
            $reg_end = $gap[1];

            $extend_conditions =[
                'reg_time'=>['between',[$reg_begin.' 00:00:00',$reg_end.' 23:59:59']]
            ];
        }
        //自定义查询条件
        $search_setting = [
            'keyword_condition'=>'id|username|nickname|mobile',
            //忽略数据库不存在的字段
            'ignore_keys' => ['reg_time_range'],
            //扩展的查询条件
            'extend_conditions'=>$extend_conditions
        ];

        return $search_setting;
    }

    /**
     * 个人资料
     * @param  integer $uid [description]
     * @return [type] [description]
     * @date   2017-12-28
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function profile($uid = 0) {
        
        if (IS_POST) {

            $data = $this->request->param();
            // 提交数据
            $result = $this->userModel->editData($data);
            if ($result) {
                $uid = $data['uid'];
                if ($uid==is_login()) {//如果是编辑状态下
                    logic('common/User')->updateLoginSession($uid);
                }

                $this->success('提交成功', url('profile',['uid'=>$uid]));
            } else {
                $msg = $this->userModel->getError();
                if (!$msg) {
                    $msg = '操作失败';
                }
                $this->error($msg);
            }
        } else {
            $this->assign('meta_title','个人资料');
            $this->assign('page_config',['disable_panel'=>true]);
            // 获取账号信息
            if ($uid>0) {
                $user_info = get_user_info($uid);
                unset($user_info['password']);
                //unset($user_info['auth_group']['max']);
            }
            $this->assign('user_info',$user_info);
            return $this->fetch();
        }
    }

    /**
     * 个人资料修改密码
     * @return [type] [description]
     * @date   2018-02-19
     * @author 心云间、凝听 <981248356@qq.com>
     */
    public function resetPassword(){
        if (IS_POST) {
            $params = $this->request->param();
            $result = $this->validate($params,[
                ['id','number|>=:1','用户ID格式不正确|用户ID格式不正确'],
                ['newpassword','min:6','重置密码长度不能少于6位'],
                ['repassword','min:6|confirm:newpassword','重复密码不正确|重复密码不一致'],
            ]);
            if(true !== $result){
                // 验证失败 输出错误信息
                $this->error($result);
            }
            if (!isset($params['ids']) && !isset($params['uid'])) {
                $this->error('操作用户不存在');
            }
            $map = [];
            if (isset($params['ids'])) {
                $map['uid'] = ['in',$params['ids']];
                $newpassword = 123456;
            } elseif (isset($params['uid'])) {
                if (!isset($params['newpassword']) || !isset($params['repassword']) ||!$params['newpassword']) {
                    $this->error('请填写一个合适的密码');
                }
                $map['id'] = $params['uid'];
                $newpassword = $params['newpassword'];
                $repassword  = $params['repassword'];
            }
            //$oldpassword=input('param.oldpassword',false);
            $new_password = encrypt($newpassword);
            $res = UserModel::where($map)->setField('password',$new_password);
            if ($res) {
                if (isset($params['uid']) && $params['uid']==is_login()) {
                    session(null);
                    $this->success('已重置密码成功，新密码：'.$newpassword, url('admin/login/index'));
                } else{
                    $this->success('已重置密码成功，新密码：'.$newpassword);
                }
                
            } else{
                $this->error('密码重置失败');
            }
        } else {
            // 获取账号信息
            $info = $this->userModel->get(is_login());

            $content = builder('form')
                    ->addFormItem('uid', 'hidden', 'UID', '')
                    //->addFormItem('oldpassword', 'password', '原密码', '','','','placeholder="填写旧密码"')
                    ->addFormItem('newpassword', 'password', '新密码', '','','placeholder="填写新密码"')
                    ->addFormItem('repassword', 'password', '重复密码', '','','placeholder="填写重复密码"')
                    ->setFormData($info)
                    //->setAjaxSubmit(false)
                    ->addButton('submit')->addButton('back')    // 设置表单按钮
                    ->fetch();

            return (new Iframe())
                    ->setMetaTitle('重置密码') // 设置页面标题
                    ->content($content);
        }
    }

    public function set_type($uid){

        if (IS_POST) {
            $data = input('param.');
            if(empty($data['user_type'])){
                $this->error('请选择用户身份');
            }
            if(empty($data['username'])){
                $this->error('姓名不能为空');
            }
            if(empty($data['company'])){
                $this->error('公司名称不能为空');
            }
            Db::startTrans();
            try{
                // 提交数据
                //$data里包含主键id，则editData就会更新数据，否则是新增数据
                $main['id'] = $data['uid'];
                $main['user_type'] = $data['user_type'];
                $main['update_time'] = mydate();
                $result = $this->userModel->editData($main);

                $maps['uid'] = array('eq',$data['uid']);
                $vo = db('userdata')->where($maps)->find();
                $data['pro_title'] =  provinceCityCounty($data,'zone_title');//省市区名称
                unset($data['user_type']);

                if(!empty($vo)){
                    $data['update_time'] = time();
                    db('userdata')->where('id',$vo['id'])->update($data);
                }else{
                    db('userdata')->insert($data);
                }
                Db::commit();
                $code = 200;
            }catch (\Exception $e){
                Db::rollback();
                $code = 201;
            }
            if($code==200){
                $this->success('提交成功',url('index'));
            }else{
                $this->error('提交失败');
            }

        } else {
            $where['u.id'] = array('eq',$uid);
            $info = db('usermember u')->join('userdata d','d.uid = u.id','LEFT')->field('u.id as uuid,u.user_type,d.*')->find();
            $this->assign('info',$info);
            $this->assign('meta_title','身份设置');

            //查询省
            $AddressModel = new AddressModel();
            $province = $AddressModel->zone_list(['pid'=>0]);
            $this->assign('province',$province);

            //市
            if(!empty($info['province_id'])){
                $city_list = $AddressModel->zone_list(['pid'=>$info['province_id']]);
            }else{
                $city_list = array();
            }
            $this->assign('city_list',$city_list);
            //区
            if(!empty($info['city_id'])){
                $county_list = $AddressModel->zone_list(['pid'=>$info['city_id']]);
            }else{
                $county_list = array();
            }
            $this->assign('county_list',$county_list);

            return $this->fetch();

        }

    }



    /**
     * 设置用户的状态
     */
    public function setStatus($model = CONTROLLER_NAME,$script=false){
        $ids = input('param.ids/a');

        if (!empty($ids)) {
            foreach ($ids as $key => $uid) {
                //清理缓存
                cache('User_info_'.$uid, null);
            }
        }
        parent::setStatus($model);
    }


    /**
     *我的会员
     */
    public function my_visit(){
        $get = input('param.');
        $get['keywords']     = !empty($get['keywords'])? $get['keywords'] : '';
        $this->assign('get',$get);

        if(!empty($get['keywords'])){
            $where['nickname|mobile'] = array('like','%'.trim($get['keywords']).'%');
        }

        $where['visit_code'] = array('eq',$get['visit_code']);
        $where['status'] = array('gt',-1);
        $data_list =  Db::name('usermember')->where($where)->paginate(20);

        $this->assign('list',$data_list);
        $this->assign('meta_title','邀请记录');
        return $this->fetch();
    }




}