<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:81:"G:\phpstudy\www\jia_zhuang\public_html/../apps/user/view/admin/user\set_type.html";i:1614044012;s:74:"G:\phpstudy\www\jia_zhuang\public_html/../apps/admin/view/public/base.html";i:1563871977;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo (isset($meta_title) && ($meta_title !== '')?$meta_title:''); ?>|<?php echo config('web_site_title'); ?>后台管理</title>
    <link type="text/css" rel="stylesheet" href="/static/assets/css/bootstrap.min.css"/>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/static/assets/css/font-awesome.min.css">
  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
   <link rel="stylesheet" type="text/css" href="/static/admin/css/AdminLTE.min.css" /><link rel="stylesheet" type="text/css" href="/static/admin/css/_all-skins.min.css" /><link rel="stylesheet" type="text/css" href="/static/assets/css/base.css" /><link rel="stylesheet" type="text/css" href="/static/libs/iCheck/all.css" /><link rel="stylesheet" type="text/css" href="/static/libs/toastr/toastr.min.css" />
   <link type="text/css" href="/static/admin/css/style.css?v=<?php echo EACOOPHP_V; ?>" rel="stylesheet" >
    <script type="text/javascript" src="/static/assets/js/jquery-1.12.4.min.js"></script>
    <!-- <script type="text/javascript" src="/static/assets/js/jquery-3.2.1.min.js"></script> -->  
    <script type="text/javascript">
        var EacooPHP = window.EacooPHP = {
            "eacoophp_version":"<?php echo EACOOPHP_V; ?>",
            "url_model":<?php echo (isset($url_model) && ($url_model !== '')?$url_model:'1'); ?>,//1重写模式，2兼容模式
            "root":'',
            "root_domain": "<?php echo \think\Request::instance()->domain(); ?>/admin.php", //当前网站地址
            "static": "/static", //静态资源地址
            "public": "/static/assets", //项目公共目录地址
            "uploads_url" :'/uploads/',
            "upload_driver":"<?php echo config('attachment_options.driver'); ?>",
            "cdn_domain":"<?php echo get_cdn_domain(); ?>",
            "eacoo_api_url":"<?php echo config('eacoo_api_url'); ?>",
        }
    </script>

     <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition skin-blue" style="background-color:#ecf0f5;">
<div class="iframe-wrapper">
    <section class="content-header">
        <!-- 面包屑导航 -->
         <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> <a href="<?php echo url('admin/dashboard/index'); ?>" class="opentab" tab-title='首页' data-iframe="true" tab-name="navtab-collapse-1">首页</a></a></li>
          <?php if(!(empty($parent_menu_list) || (($parent_menu_list instanceof \think\Collection || $parent_menu_list instanceof \think\Paginator ) && $parent_menu_list->isEmpty()))): if(is_array($parent_menu_list) || $parent_menu_list instanceof \think\Collection || $parent_menu_list instanceof \think\Paginator): $i = 0; $__LIST__ = $parent_menu_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <li class="text-muted"><?php echo $vo['title']; ?></li>
            <?php endforeach; endif; else: echo "" ;endif; else: ?>
            <li class="active"><?php echo $meta_title; ?> <span class="color-warning eacoo-menu-collect" data-title="<?php echo $meta_title; ?>" data-url="<?php echo \think\Request::instance()->url(); ?>" title="标记为收藏" style="cursor: pointer;font-size: 13px;"> <i class="fa <?php if($is_menu_collected == '1'): ?>fa-star<?php else: ?>fa-star-o<?php endif; ?>"></i></span></li>
          <?php endif; ?>
          <div class="pull-right mr-10" style="margin-top: -10px;">
            <?php if(isset($page_config['self'])): ?><?php echo $page_config['self']; endif; if(isset($page_config['help'])): ?><a href="<?php echo (isset($page_config['help']) && ($page_config['help'] !== '')?$page_config['help']:''); ?>" class="color-6" title="帮助" target="_blank"><i class="fa fa-question-circle f15"></i></a><?php endif; if(isset($page_config['back'])): ?><button type="button" class="btn btn-box-tool f16" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i></button><?php endif; ?>
              <button type="button" class="btn btn-box-tool f16" onclick="javascript:location.reload();"><i class="fa fa-refresh"></i></button>
          </div>
        </ol>
     </section>
     <!--内容-->
     
<style type="text/css">
    .layui-upload-file {
        display: none !important;
    }

    .circle_list {
        display: flex;
        margin-top: 20px;
    }

    .circle_list .circle-head {
        height: 34px;
        line-height: 34px;
        width: 200px;
        text-align: right;
        font-size: 13px;
        font-weight: bold;
        color: #616161;
        margin-right: 20px;
    }

    .circle_list .list-circle {
        width: calc(100% - 230px);
        display: flex;
        flex-wrap: wrap;
    }

    .circle_list .list-circle .select2 {
        width: 200px;
        height: 34px;
        margin-right: 20px;
    }

    .circle_list .list-circle .add_circle {
        width: 70px;
        height: 34px;
        border: none;
        border-radius: 3px;
        text-align: center;
        line-height: 34px;
        cursor: pointer;
        background: #3c8dbc;
        color: #fff;
    }

    .circle_list .list-circle ul {
        display: flex;
        flex-wrap: wrap;
    }

    .circle_list .list-circle li {
        padding-left: 10px;
        padding-right: 28px;
        position: relative;
        border: 1px #c2c2c2 solid;
        border-radius: 3px;
        line-height: 34px;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .circle_list .list-circle li .fa-remove {
        position: absolute;
        right: 5px;
        top: 7px;
        background: #d43f3a;
        border-radius: 3px;
        color: #fff;
        padding: 3px;
        cursor: pointer;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow,
    .select2-container .select2-selection--single {
        height: 34px;
    }

    .tp-title {
        display: flex;
        margin-top: 15px;
    }

    .tp-title input[type='text'] {
        display: block;
        width: calc(100% - 60px);
        height: 30px;
    }

    .tp-item {
    }

    .tp-item ul li {
        display: flex;
        margin-top: 15px;
        padding: 5px 0;
        margin-right: 0 !important;
    }

    .tp-item ul li .item-info {
        display: flex;
        margin-right: 15px;
    }

    .tp-item ul li .item-info img {
        display: block;
        width: 100px;
        height: 100px;
        margin-right: 10px;
        cursor: pointer;
    }

    .tp-item ul li .item-info .textarea textarea {
        display: block;
        width: 300px;
        height: 100px;
        resize: none;
    }

    .tp-title .title,
    .tp-item .item-li {
        width: 60px;
        line-height: 30px;
    }

    .action {
    }

    .action .add_circle {
        margin-bottom: 10px;
    }

    .chooseRadio {
        display: flex;
        height: 30px;
        align-items: center;
    }

    .chooseRadio .radioDiv {
        margin-right: 20px;
        padding-left: 20px;
        position: relative;
        height: 25px;
        line-height: 25px;
        cursor: pointer;
    }

    .chooseRadio .radioDiv:before {
        position: absolute;
        display: block;
        content: '';
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 1px #ddd solid;
        left: 0;
        top: 5px;
    }

    .chooseRadio .radioDiv:after {
        position: absolute;
        display: none;
        content: '';
        width: 7px;
        height: 7px;
        border-radius: 50%;
        left: 4px;
        top: 9px;
        background: #00a0e9;
        z-index: 2;
    }

    .chooseRadio .radioDiv.on:after {
        display: block;
    }

</style>
<section class="content pt-5">
    <div class="box box-solid eacoo-box">
        <div class="box-body">
            <div class="cf eacoo-tabs">
                <ul class="nav nav-tabs">
                    <li class="tab_list active"><a href="javascript:;">基本信息</a></li>
                </ul>

            </div>


        <div class="builder formbuilder-box panel-body bg-color-fff">

            <div class="row">
                <div class="col-md-11">
                  <form action="" method="post" class="form-builder form-horizontal">
                    <fieldset>
                      <input type="hidden" name="uid" value="<?php echo $info['uid']; ?>">
                        <div class="tab_content">

                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">用户身份：</label>
                                <div class="col-md-2">

                                        <select class="form-control" name="user_type">
                                            <option value="">请选择</option>
                                            <option value="2" <?php if($info['user_type']==2){echo 'selected';} ?>>大客户</option>
                                            <option value="3" <?php if($info['user_type']==3){echo 'selected';} ?>>代理商</option>
                                        </select>
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须选择，<font style="color:red;">不要随意更改</font></div>
                            </div>

                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">姓名：</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="username" placeholder="姓名" value="<?php echo (isset($info['username']) && ($info['username'] !== '')?$info['username']:''); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">公司名称：</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="company" placeholder="公司名称" value="<?php echo (isset($info['company']) && ($info['company'] !== '')?$info['company']:''); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>


                        <!--选择城市-->

                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">选择省市区：</label>
                                <div class="col-md-4">
                                    <div class="col-md-2" style="width:150px;padding-left:0;">
                                        <select class="form-control" name="province_id" id="province_id">
                                            <option value="">省</option>
                                            <?php if(is_array($province) || $province instanceof \think\Collection || $province instanceof \think\Paginator): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pro): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $pro['id']; ?>" <?php if(!empty($info['province_id']) and $info['province_id'] == $pro['id']): ?>selected<?php endif; ?>><?php echo $pro['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2" style="width:150px;padding-left:0;">
                                        <select class="form-control" name="city_id" id="city_id">
                                            <option value="">市</option>
                                            <?php if(is_array($city_list) || $city_list instanceof \think\Collection || $city_list instanceof \think\Paginator): $i = 0; $__LIST__ = $city_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cit): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $cit['id']; ?>" <?php if($info['city_id']==$cit['id']){echo 'selected';} ?>><?php echo $cit['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2" style="width:150px;padding-left:0;">
                                        <select class="form-control" name="county_id" id="county_id">
                                            <option value="">区县</option>

                                            <?php if(is_array($county_list) || $county_list instanceof \think\Collection || $county_list instanceof \think\Paginator): $i = 0; $__LIST__ = $county_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ty): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $ty['id']; ?>" <?php if($info['county_id']==$ty['id']){echo 'selected';} ?>><?php echo $ty['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须选择</div>
                            </div>



                        <!--选择城市-->
                        <div class="form-group item_title">
                            <label class="col-md-2 control-label">详细地址：</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="address" placeholder="详细地址" value="<?php echo (isset($info['address']) && ($info['address'] !== '')?$info['address']:''); ?>" >
                            </div>
                            <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                        </div>


							





                        </div>

                        <div class="form-group">
                            <div class="col-md-12 col-md-offset-2">
                                <div class="col-md-2"><button class="btn btn-block btn-primary submit ajax-post" type="submit" target-form="form-builder">确定</button></div>
                                <div class="col-md-2"><button class="btn btn-block btn-default return" onclick="javascript:history.back(-1);return false;">返回</button></div>
                            </div>
                        </div>


                         </fieldset>
                  </form>

                </div>    
           </div>
         </div>
        </div>
    </div>
</section>

     
</div>
<script type="text/javascript" src="/static/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/assets/js/jquery.cookie.js"></script>

<!-- Slimscroll -->
<script src="/static/libs/slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script type="text/javascript" src="/static/libs/fastclick/fastclick.min.js"></script>
<script type="text/javascript" src="/static/admin/js/app.min.js" ></script>
<!-- <script  type="text/javascript" src="/static/assets/js/jquery.nestable.js"></script> -->
<script  type="text/javascript" src="/static/libs/nice-validator/jquery.validator.min.js?local=zh-CN"></script>
<!-- <script type="text/javascript" src="/static/assets/js/think.js"></script> -->
<script type="text/javascript" src="/static/libs/layer/layer.js"></script>
<script type="text/javascript" src="/static/libs/iCheck/icheck.min.js"></script>

<script type="text/javascript" src="/static/libs/toastr/toastr.min.js"></script>

<script type="text/javascript" src="/static/admin/js/common.js?v=<?php echo EACOOPHP_V; ?>" ></script>
<script type="text/javascript" src="/static/admin/js/eacoo.js?v=<?php echo EACOOPHP_V; ?>" ></script>
<script type="text/javascript" src="/static/admin/js/base.js?v=<?php echo EACOOPHP_V; ?>" ></script>

<script src="/static/admin/js/zone_select.js"></script>
<script>
$('select[name="position_id"]').change(function () {
    var size = $(this).find('option:selected').attr('data-size');

    if(size!=''){
        $("#description").text('图片尺寸'+size);
    }
});


$('.tab_list').click(function () {
    var index = $(this).index();
    $(this).addClass('active').siblings().removeClass('active');
    $('.tab_content').eq(index).show().siblings('.tab_content').hide();
});



</script>





</body>
</html>