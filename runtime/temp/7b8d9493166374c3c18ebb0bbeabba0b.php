<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:78:"G:\phpstudy\www\jia_zhuang\public_html/../apps/admin\view\product\gg_edit.html";i:1613806856;s:74:"G:\phpstudy\www\jia_zhuang\public_html/../apps/admin/view/public/base.html";i:1563871977;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;s:58:"G:\phpstudy\www\jia_zhuang\apps\admin\view\ad\picture.html";i:1592297814;s:59:"G:\phpstudy\www\jia_zhuang\apps\admin\view\public\file.html";i:1604025202;}*/ ?>
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
            <div class="cf eacoo-tabs">
                <ul class="nav nav-tabs">
                    <li class="tab_list active"><a href="javascript:;">基本信息</a></li>
                 <!--   <li class="tab_list"><a href="javascript:;">其他信息</a></li>-->
                </ul>

            </div>


        <div class="builder formbuilder-box panel-body bg-color-fff">

            <div class="row">
                <div class="col-md-11">
                  <form action="<?php echo url(''); ?>" method="post" class="form-builder form-horizontal">
                    <fieldset>
                      <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                      <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                        <div class="tab_content">
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">所属商品：</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" value="<?php echo modelField($product_id,'product','title'); ?>" disabled >
                                </div>

                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">规格分类：</label>
                                <div class="col-md-2">
                                    <select class="form-control" name="typeid" id="typeid">
                                        <option value="">请选择</option>
                                        <?php if(is_array($typelist) || $typelist instanceof \think\Collection || $typelist instanceof \think\Paginator): $i = 0; $__LIST__ = $typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pro): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $pro['id']; ?>" data-type="<?php echo $pro['type']; ?>" <?php if($info['typeid']==$pro['id']){echo 'selected';} ?>><?php echo $pro['title']; if($pro['type'] == 1): ?>（影响价格）<?php endif; ?> </option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>

                                <!--- 影响价格规格 --->
                                <input type="hidden" name="is_price" id="is_price" value="<?php echo (isset($info['is_price']) && ($info['is_price'] !== '')?$info['is_price']:0); ?>" />

                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须选择</div>
                            </div>


					    <div class="form-group item_title">
							<label class="col-md-2 control-label">规格名称：</label>
							<div class="col-md-4">
							  <input type="text" class="form-control" name="gg_title" placeholder="规格名称" value="<?php echo (isset($info['gg_title']) && ($info['gg_title'] !== '')?$info['gg_title']:''); ?>" >
							</div>
						  <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须填写</div>
					    </div>
                            <div class="hide_div" style="<?php if($info['is_price']==1){echo 'display: block;';}else{echo 'display: none;';} ?>">
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">成本价格：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="cben_price" placeholder="普通价格" value="<?php echo (isset($info['cben_price']) && ($info['cben_price'] !== '')?$info['cben_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">商品原价：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="old_price" placeholder="普通价格" value="<?php echo (isset($info['old_price']) && ($info['old_price'] !== '')?$info['old_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">普通价格：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="new_price" placeholder="普通价格" value="<?php echo (isset($info['new_price']) && ($info['new_price'] !== '')?$info['new_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">大客户价格：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="bigdl_price" placeholder="大客户价格" value="<?php echo (isset($info['bigdl_price']) && ($info['bigdl_price'] !== '')?$info['bigdl_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">代理商价格：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="dls_price" placeholder="小代理价格" value="<?php echo (isset($info['dls_price']) && ($info['dls_price'] !== '')?$info['dls_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填</div>
                            </div>

                            </div>
                            <!--<div class="form-group item_title">
                                <label class="col-md-2 control-label">库存：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="stock" placeholder="排序" value="<?php echo (isset($info['stock']) && ($info['stock'] !== '')?$info['stock']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 影响价格的规格才填写库存</div>
                            </div>-->
                        <div class="form-group item_title">
                            <label class="col-md-2 control-label">排序：</label>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="sort" placeholder="排序" value="<?php echo (isset($info['sort']) && ($info['sort'] !== '')?$info['sort']:''); ?>" >
                            </div>
                            <div class="col-md-5 help-block"> </div>
                        </div>

                            <div class="form-group item_image">
                                <label for="image" class="col-md-2 control-label">缩略图(影响价格的才传)：</label>
                                

<div class="col-md-6" style="padding-bottom: 5px;padding-left: 5px;">
	<div class="controls" style="padding-bottom: 5px;padding-left: 5px;">
		<input class="attach" type="hidden" id="icon" name="icon" value="<?php echo (isset($info['icon']) && ($info['icon'] !== '')?$info['icon']:''); ?>"/>
		<div>
			<span class="btn btn-info ml-10 mt-10 btn-sm" data-url="<?php echo url('admin/Upload/attachmentLayer',['input_id_name'=>'icon','path_type'=>'picture','gettype'=>'single']); ?>" onclick="openAttachmentLayer(this);"><i class="fa fa-file-image-o"></i> 选择图片</span>
		</div>
		<div class="upload-img-box tc popup-gallery fl img-thumbnail pr ">
			<div class="each">
				<?php if(empty($info['icon']) || (($info['icon'] instanceof \think\Collection || $info['icon'] instanceof \think\Paginator ) && $info['icon']->isEmpty())): ?>
				<img src="<?php  echo get_image(0); ?>">
				<?php else: ?>
				<i onclick="admin_image.removeImage($(this),'<?php echo $info["icon"]; ?>')" class="fa fa-times-circle remove-attachment"></i>
				<a href="<?php echo get_image($info['icon']); ?>" target="_blank" title="点击查看大图">
					<img src="<?php echo get_image($info['icon']); ?>">
				</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="help-block col-md-6 pl-10 fn" id="description"></div>
	</div>
</div>

                            </div>

                        </div>

                        <div class="form-group">
                            <div class="col-md-12 col-md-offset-2">
                                <div class="col-md-3"><button class="btn btn-block btn-primary submit ajax-post" type="submit" target-form="form-builder">确定</button></div>
                                <div class="col-md-3"><button class="btn btn-block btn-default return" onclick="javascript:history.back(-1);return false;">返回</button></div>
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




<link href="/static/home/layui/css/layui.css" type="text/css" rel="stylesheet">
<script type="text/javascript" charset="utf-8" src="/static/home/layui/layui.all.js"></script>
<script>
    //上传附件
    function uploadcommer(ider,cont,file_type) {

        layui.use('upload', function(){
            var upload = layui.upload;
            //执行实例
            var uploadInst = upload.render({
                elem:ider //绑定元素
                ,url: '/Admin/upload/upload' //上传接口
                ,accep:'file'
                ,exts:'mp4|mp3|doc'
                ,data:{type:'file',file_type:file_type}
                ,before: function(obj){
                    layer.load(); //上传loading
                }
                ,done: function(res){
                    layer.closeAll('loading'); //关闭loading
                    if(res.code==1){
                        $(cont).val(res.data.path);
                    }else{
                        layer.msg(res.msg);
                    }
                }
            });
        });
    }


</script>
<script>
    uploadcommer('#upload_file_1',"#video_url",1);
</script>
<script>

    var big_scale = "<?php echo \think\Config::get('big_customer_scale'); ?>";
    var dls_scale = "<?php echo \think\Config::get('dls_price_scale'); ?>";


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

//切换分类
$('#typeid').change(function(){
    var type = $(this).find('option:selected').attr('data-type');
    if(type==1){
        $(".hide_div").show();
        $("#is_price").val(1);
    }else{
        $(".hide_div").hide();
        $("#is_price").val(0);
    }
});

$("input[name='cben_price']").blur(function () {
    var cben_price = $(this).val();
    if(big_scale=='' || big_scale<=0){
        var bigdl_price = Number(cben_price);
    }else{
        var bigdl_price = Number(cben_price) + Number((Number(cben_price)*big_scale/100).toFixed(2));
    }
    $("input[name='bigdl_price']").val(bigdl_price);

    if(dls_scale=='' || dls_scale<=0){
        var dls_price = Number(cben_price);
    }else{
        var dls_price = Number(cben_price) + Number((Number(cben_price)*dls_scale/100).toFixed(2));
       
    }
    $("input[name='dls_price']").val(dls_price);
});


</script>





</body>
</html>