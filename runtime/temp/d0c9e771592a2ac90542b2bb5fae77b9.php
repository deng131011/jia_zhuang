<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:65:"D:\www\jia_zhuang\public_html/../apps/admin\view\coupon\edit.html";i:1613886821;s:65:"D:\www\jia_zhuang\public_html/../apps/admin/view/public/base.html";i:1563871977;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;}*/ ?>
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
     
<section class="content pt-5">
    <div class="box box-solid eacoo-box">
        <div class="box-body">

        <div class="builder formbuilder-box panel-body bg-color-fff">

            <div class="row">    

                <div class="col-md-11">
                  <form action="" method="post" class="form-builder form-horizontal">
                    <fieldset>

                      <input type="hidden" name="id" value="<?php echo $info['id']; ?>">

					    <div class="form-group item_title">
							<label for="title" class="col-md-2 control-label">优惠券名称：</label>
							<div class="col-md-4">
							  <input type="text" class="form-control" name="title" placeholder="优惠券名称" value="<?php echo (isset($info['title']) && ($info['title'] !== '')?$info['title']:''); ?>" >
							</div>
						  <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须填写</div>
					    </div>


                        <!---- 现金优惠券  --->
                        <input type="hidden" name="type" value="1" />


                        <div class="form-group item_title">
                            <label for="title" class="col-md-2 control-label">优惠券面额：</label>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="val" placeholder="优惠券面额" value="<?php echo (isset($info['val']) && ($info['val'] !== '')?$info['val']:''); ?>" >
                            </div>
                            <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须填写，例：3元填写3</div>
                        </div>





						<div class="form-group item_title">
                            <label class="col-md-2 control-label">领取途径：</label>
                            <div class="radio radio-primary fl mr-10">
							
							    <?php if(is_array($get_type) || $get_type instanceof \think\Collection || $get_type instanceof \think\Paginator): $kk = 0; $__LIST__ = $get_type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$gett): $mod = ($kk % 2 );++$kk;if($gett['open'] == 1): ?>
                                    <label class="radio-label">
                                        <input type="radio" name="get_type" value="<?php echo $gett['id']; ?>" <?php if($info['get_type']==$gett['id']&&!empty($info)){echo 'checked';}else if($kk==1){echo 'checked';} ?> > <?php echo $gett['title']; ?>
                                    </label>
                                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                
                            </div>
                            <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须选择</div>
                        </div>




                        <div class="form-group item_title day_a">
                            <label class="col-md-2 control-label">过期日期：</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control times" name="end_time" placeholder="" value="<?php echo (isset($info['end_time']) && ($info['end_time'] !== '')?$info['end_time']:''); ?>" readonly />
                            </div>
                            <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须填写</div>
                        </div>
						
						<div class="form-group item_title day_b" style="display: none;">
                            <label class="col-md-2 control-label">有效期：</label>
                           <div class="col-md-2">
                                <input type="number" class="form-control" name="days" placeholder="" value="<?php echo (isset($info['days']) && ($info['days'] !== '')?$info['days']:''); ?>" >
                            </div>
                            <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须填写，单位/天</div>
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

<script>
$('select[name="position_id"]').change(function () {
    var size = $(this).find('option:selected').attr('data-size');

    if(size!=''){
        $("#description").text('图片尺寸'+size);
    }
});

$('.radio-label').click(function () {
    var val = $(this).find("input[name='type']:checked").val()
    if(val==3){
	   $(".man_zhe").text('满减设置：');
	   $(".val_a").attr('placeholder','减');
	}else{
	   $(".man_zhe").text('满折设置：');
	   $(".val_a").attr('placeholder','折');
	}
});
$('.radio-label').click(function () {
    var val = $(this).find("input[name='get_type']:checked").val()
    if(val==1){
        $(".day_a").show();
        $(".day_b").hide();
    }else{
        $(".day_b").show();
        $(".day_a").hide();
    }
});

</script>
<link href="/static/libs/datetimepicker/datetimepicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/static/libs/datetimepicker/datetimepicker.min.js"></script>
<script type="text/javascript">
    $('.times').datetimepicker({
        format      : 'yyyy-mm-dd',
        autoclose   : true,
        todayBtn    : 'linked',
        language    : 'zh-CN',
        fontAwesome : true,
        minView : 2,
    });
</script>





</body>
</html>