<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"D:\www\jia_zhuang\public_html/../apps/admin\view\login\index.html";i:1613826836;}*/ ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>后台登录|后台管理</title>
    <link type="text/css" rel="stylesheet" href="/static/assets/css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="/static/admin/css/AdminLTE.min.css"/>
    <link type="text/css" rel="stylesheet" href="/static/admin/css/_all-skins.min.css"/>
    <link type="text/css" rel="stylesheet" href="/static/assets/css/base.css"/>
    <link type="text/css" rel="stylesheet" href="/static/libs/iCheck/all.css"/>
    <link type="text/css" rel="stylesheet" href="/static/admin/css/login.css"/>
    <style type="text/css">
    .login-page{background: #fff url("/static/admin/img/login-bg.jpg");}
    .login-box-msg{font-size: 20px; font-weight: bold;}
    </style>
    <script type="text/javascript" src="/static/assets/js/jquery-1.10.2.min.js"></script>
  </head>
  <body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">

    </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">家居后台管理</p>
    <form action="" method="post" class="form-horizontal">
    <div class="box-body">
      <div class="form-group has-feedback">
          <input type="text" name="username" id="username" class="form-control" placeholder="用户名">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
          <input type="password" name="password" id="password" class="form-control" placeholder="密码">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
       <div class="form-group">
          <div class="row">
            <div class="col-sm-8">
              <input type="text"  id="captcha" class=" form-control"  name="captcha" placeholder="验证码" size="10"/>
            </div>
            <div class="col-sm-4">
              <img class="right verify" src="<?php echo BASE_PATH; ?><?php echo url('login/verify_img'); ?>" id="code" width="117" height="45" onclick="changeVerify()"/>
          </div>
          </div>
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-sm-6">
              <label class="css-input switch switch-sm switch-primary">
                  <input type="checkbox" id="login-remember-me" name="remember-me" value="1"><span></span> 7天内自动登录?
              </label>
          </div>
          <div class="col-sm-6 hide">
              <div class="font-s13 text-right push-5-t">
                  <a href="">忘记密码?</a>
              </div>
          </div>
        </div>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="tc">
          <button type="button" class="btn btn-primary btn-block btn-flat login_btn" id="login">登录</button>
        </div>
        <!-- /.col -->
      </div>
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
	</body>
<script type="text/javascript" src="/static/libs/layer/layer.js"></script>
<script type="text/javascript" src="/static/libs/iCheck/icheck.min.js"></script>
<script type="text/javascript">
(function ($) {
  $('input').iCheck({
        checkboxClass:'icheckbox_minimal-blue',
        radioClass:'iradio_minimal-blue',
        increaseArea:'20%' // optional
      });
})(jQuery);

function changeVerify(){
    $('#code').attr('src',"<?php echo BASE_PATH; ?><?php echo url('login/verify_img',['v'=>rand_string(15)]); ?>");
}

function BindEnter(obj)
{
    if(obj.keyCode == 13)
      {
          $("#login").click()
      }
}

$("#login").click(function(){

    var username   = $("input[name='username']");
    var password   = $("input[name='password']");
    var captcha    = $("input[name='captcha']");

    if($('#login-remember-me').is(':checked')) {
        var rememberme = 1;
    }else {
        var rememberme = 0;
    }

    if(username.val()==''){
        username.focus();
        return false
    }
    if(captcha.val()==''){
        captcha.focus();
        return false
    }
    if(password.val()==''){
      password.focus();
      return false
    }

    $.ajax({
      type: 'POST',
      url: '<?php echo BASE_PATH; ?><?php echo url("login/index"); ?>',
      data: {
        username:username.val(),
        password:password.val(),
        captcha:captcha.val(),
        rememberme:rememberme,
      },
      beforeSend:function(){
        layer.load(2,{shade: [0.1,'#fff']});
      },

      success:function(data){
        layer.closeAll();
        if(data.code==1){
          window.location.href = data.url; 
        }else{
          layer.msg(data.msg, {icon:5});
          changeVerify();
        }

      }
  });

})

</script>
</html>
