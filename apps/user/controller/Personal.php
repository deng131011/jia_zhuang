<?php
// 用户首页

namespace app\user\controller;
use app\home\controller\Home;

use app\common\model\User as UserModel;
use app\common\logic\User as UserLogic;

class Personal extends Home{
    function _initialize()
    {
        parent::_initialize();
        $this->userModel = new UserModel;
    }

    /*
     *  Description: 个人信息
     *  By: yyyvy  <QQ:76836785>
     *  Time: 2017-12-28 10:19:55
     * */
    public function index(){
      if(is_login()) {
        return $this->fetch();
      }
      $this->error('未登录');
    }

    /*
     *  Description: 修改个人信息
     *  By: yyyvy  <QQ:76836785>
     *  Time: 2017-12-28 14:28:21
     * */
    public function profile() {
          if(!is_login()){
                $this->error('未登录');
          }
        if (IS_POST) {
          $data = input('post.');
          // 提交数据
          $data['uid']=is_login();
          $result = $this->userModel->editData($data);

          if ($result) {
            logic('common/User')->updateLoginSession(is_login());
            $this->success('提交成功', url('profile'));
          } else {
            $this->error($this->userModel->getError());
          }
        }else {
          // 获取账号信息
          $user_info = get_user_info(is_login());
          unset($user_info['password']);
          unset($user_info['auth_group']['max']);
          $this->assign('user_info',$user_info);
          return $this->fetch();
        }
      
    }
}
