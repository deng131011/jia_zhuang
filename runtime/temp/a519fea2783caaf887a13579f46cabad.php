<?php if (!defined('THINK_PATH')) exit(); /*a:7:{s:66:"D:\www\jia_zhuang\public_html/../apps/admin\view\product\edit.html";i:1613830207;s:65:"D:\www\jia_zhuang\public_html/../apps/admin/view/public/base.html";i:1563871977;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;s:49:"D:\www\jia_zhuang\apps\admin\view\ad\picture.html";i:1592297814;s:53:"D:\www\jia_zhuang\apps\admin\view\ad\picture_arr.html";i:1562823362;s:57:"D:\www\jia_zhuang\apps\admin\view\public\picture_arr.html";i:1612151029;s:50:"D:\www\jia_zhuang\apps\admin\view\public\file.html";i:1604025202;}*/ ?>
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


<style>
    .add_list{background: #f3a800; color: #fff; padding:3px 15px; cursor: pointer; border-radius: 3px; margin-left:20px;}
    .gg_list{margin-top: 5px; float: left;}
    .comm_add .text2{width:200px; margin-left:10px;}
    .gg_input{float: left; width:40%; margin-left: 5px;}
    .gg_input2{width:20%;}
    .btn_delete{display: inline-block; float: left;margin-top:5px; margin-left: 5px;}
</style>



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

                        <div class="tab_content">
					    <div class="form-group item_title">
							<label class="col-md-2 control-label">商品名称：</label>
							<div class="col-md-4">
							  <input type="text" class="form-control" name="title" placeholder="商品名称" value="<?php echo (isset($info['title']) && ($info['title'] !== '')?$info['title']:''); ?>" >
							</div>
						  <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须填写</div>
					    </div>

                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">产品分类：</label>
                                <div class="col-md-4">
                                    <div style="width:45%;float: left">
                                        <select class="form-control" name="type1" id="type1">
                                            <option value="">一级分类</option>
                                            <?php if(is_array($typelist) || $typelist instanceof \think\Collection || $typelist instanceof \think\Paginator): $i = 0; $__LIST__ = $typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pro): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $pro['id']; ?>" <?php if($info['type1']==$pro['id']){echo 'selected';} ?>><?php echo $pro['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>

                                    <div style="width:45% ; float: left; margin-left:10px;">
                                        <select class="form-control" name="type2" id="type2">
                                            <option value="">二级分类</option>
                                            <?php if(!(empty($two_typelist) || (($two_typelist instanceof \think\Collection || $two_typelist instanceof \think\Paginator ) && $two_typelist->isEmpty()))): if(is_array($two_typelist) || $two_typelist instanceof \think\Collection || $two_typelist instanceof \think\Paginator): $i = 0; $__LIST__ = $two_typelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ty): $mod = ($i % 2 );++$i;?>
                                            <option value="<?php echo $ty['id']; ?>" <?php if($info['type2']==$ty['id']){echo 'selected';} ?>><?php echo $ty['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必须选择</div>
                            </div>

                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">展示原价：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="old_price" placeholder="展示原价" value="<?php echo (isset($info['old_price']) && ($info['old_price'] !== '')?$info['old_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填，只供展示</div>
                            </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">展示现价：</label>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="show_price" placeholder="展示现价" value="<?php echo (isset($info['show_price']) && ($info['show_price'] !== '')?$info['show_price']:0); ?>" >
                                </div>
                                <div class="col-md-5 help-block"><i class="fa fa-info-circle color-info1"></i> 必填，只供展示</div>
                            </div>

                        <div class="form-group item_title">
                            <label class="col-md-2 control-label">排序：</label>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="sort" placeholder="排序" value="<?php echo (isset($info['sort']) && ($info['sort'] !== '')?$info['sort']:''); ?>" >
                            </div>
                            <div class="col-md-5 help-block"> </div>
                        </div>

                        <div class="form-group item_title">
                            <label class="col-md-2 control-label">上架状态：</label>
                            <div class="radio radio-primary fl mr-10">
                                <label class="radio-label">
                                    <input type="radio" name="status" value="1" <?php if($info['status']==1 || empty($info)){echo 'checked';} ?> > 已上架
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="status" value="2" <?php if($info['status']==2){echo 'checked';} ?> > 已下架
                                </label>

                            </div>
                        </div>
                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">推荐状态：</label>
                                <div class="radio radio-primary fl mr-10">
                                    <label class="radio-label">
                                        <input type="checkbox" name="flag[]" value="1" <?php if(in_array(1,explode(',',$info['flag']))){echo 'checked';} ?> > 爆款推荐
                                    </label>
                                   <label class="radio-label">
                                        <input type="checkbox" name="flag[]" value="2" <?php if(in_array(2,explode(',',$info['flag']))){echo 'checked';} ?> > 产品推荐
                                    </label>

                                </div>
                            </div>

                            <div class="form-group item_title">
                                <label class="col-md-2 control-label">基础信息：</label>
                                <div class="radio radio-primary fl mr-10">
                                    <span class="add_list btn_add_list">＋添加</span>
                                    <span class="help-block" style="display: inline">（依次为 ‘ 标题-对应值 ’，例：产地 - 广东）</span>
                                </div>
                                <div style="width: 100%; float: left;margin-top: 10px;">
                                    <label class="col-md-2 control-label"></label>
                                    <?php $data_json = !empty($info['data_json']) ? json_decode($info['data_json'],true) : []; ?>
                                    <div class="col-md-6" id="controls_price">
                                        <?php if(is_array($data_json) || $data_json instanceof \think\Collection || $data_json instanceof \think\Paginator): $i = 0; $__LIST__ = $data_json;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dea): $mod = ($i % 2 );++$i;?>
                                        <div class="gg_list">
                                            <input type="text" class="form-control gg_input" name="data_title[]" placeholder="标题" value="<?php echo $dea['data_title']; ?>" >
                                            <input type="text" class="form-control gg_input" name="data_val[]" placeholder="值" value="<?php echo $dea['data_val']; ?>" >
                                            <a href="javascript:;" class="btn_delete">删除</a>
                                        </div>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group item_image">
                                <label for="image" class="col-md-2 control-label">商品缩略图：</label>
                                

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
                            <div class="form-group item_image">
                                <label for="image" class="col-md-2 control-label">商品轮播图：</label>
                                <div class="col-md-8" style="padding-bottom: 5px;padding-left: 5px;">
                                
<?php 
if (strpos($field['name'],'[')) {
$field['id']=str_replace(']','',str_replace('[','',$field['name']));
}else{
$field['id']=$field['name'];
}
$path_type=isset($field['options']['path_type'])? $field['options']['path_type'] : 'picture';
 ?>
<style>
    .uploader-list .thumbnail img {max-width: 100%; height: 100%; }
    .uploader-list .thumbnail {width: 100%;height: 135px;margin-bottom: 15px;}
</style>
<div class="row controls">
    <div class="col-md-2" style="padding-bottom: 5px;">
        <span class="btn btn-info ml-10 btn-sm" data-url="<?php echo url('admin/Upload/attachmentLayer',['input_id_name'=>$field['id'],'path_type'=>$path_type,'gettype'=>'multiple']); ?>" data-gettype="multiple" onclick="openAttachmentLayer(this);"><i class="fa fa-photo"></i> 选择多图</span>
        <input class="attach" type="hidden" id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo (isset($field['value']) && ($field['value'] !== '')?$field['value']:''); ?>"/>
    </div>
    <?php if(!(empty($field['description']) || (($field['description'] instanceof \think\Collection || $field['description'] instanceof \think\Paginator ) && $field['description']->isEmpty()))): ?><div class="help-block col-md-8 fn" style="color:#E74C3C;padding-left: 20px;"><?php echo $field['description']; ?></div><?php endif; ?>
    <div id="<?php echo $field['id']; ?>-gallery-box" class="uploader-list col-md-12 img-box <?php if(!(empty($field['value']) || (($field['value'] instanceof \think\Collection || $field['value'] instanceof \think\Paginator ) && $field['value']->isEmpty()))): ?>gallery-box-bg<?php endif; ?>">
    <?php if(!(empty($field['value']) || (($field['value'] instanceof \think\Collection || $field['value'] instanceof \think\Paginator ) && $field['value']->isEmpty()))): 
    if (is_array($field['value'])) {
    $images = $field['value'];
    } else {
    $images = explode(',',$field['value']);
    }

     if(is_array($images) || $images instanceof \think\Collection || $images instanceof \think\Paginator): if( count($images)==0 ) : echo "" ;else: foreach($images as $key=>$img): if(!(empty($img) || (($img instanceof \think\Collection || $img instanceof \think\Paginator ) && $img->isEmpty()))): ?>
    <div class="col-md-3">
        <div class="thumbnail">
            <i class="fa fa-times-circle remove-attachment"></i>
            <img class="img" src="<?php echo get_image($img); ?>" data-id="<?php echo $img; ?>">
        </div>
    </div>
    <?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
</div>

</div>
<script>
    $(function () {
        // 删除图片
        $('body').on('click', '#<?php echo $field['id']; ?>-gallery-box .remove-attachment', function() {
            var ready_for_remove_id = $(this).closest('.thumbnail').find('img').attr('data-id'); //获取待删除的图片ID
            if (!ready_for_remove_id) {
                updateAlert('错误', 'danger');
            }
            var current_picture_ids = $('#<?php echo $field['id']; ?>').val().split(","); //获取当前图集以逗号分隔的ID并转换成数组
            current_picture_ids.splice($.inArray(ready_for_remove_id,current_picture_ids),1); //从数组中删除待删除的图片ID
            $('#<?php echo $field['id']; ?>').val(current_picture_ids.join(',')) //删除后覆盖原input的值
            $(this).closest('.col-md-3').remove(); //删除图片预览图
        });

    })
</script>
                                </div>
                            </div>

                            <div class="form-group item_image">
                                <label for="image" class="col-md-2 control-label">详情图集：</label>
                                <div class="col-md-8" style="padding-bottom: 5px;padding-left: 5px;">
                                    
<?php 
$field = $fieldarrb;
if (strpos($field['name'],'[')) {
$field['id']=str_replace(']','',str_replace('[','',$field['name']));
}else{
$field['id']=$field['name'];
}
$path_type=isset($field['options']['path_type'])? $field['options']['path_type'] : 'picture';
 ?>
<style>
    .uploader-list .thumbnail img {max-width: 100%; height: 100%; }
    .uploader-list .thumbnail {width: 100%;height: 135px;margin-bottom: 15px;}
</style>
<div class="row controls">
    <div class="col-md-2" style="padding-bottom: 5px;">
        <span class="btn btn-info ml-10 btn-sm" data-url="<?php echo url('admin/Upload/attachmentLayer',['input_id_name'=>$field['id'],'path_type'=>$path_type,'gettype'=>'multiple']); ?>" data-gettype="multiple" onclick="openAttachmentLayer(this);"><i class="fa fa-photo"></i> 选择多图</span>
        <input class="attach" type="hidden" id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo (isset($field['value']) && ($field['value'] !== '')?$field['value']:''); ?>"/>
    </div>
    <?php if(!(empty($field['description']) || (($field['description'] instanceof \think\Collection || $field['description'] instanceof \think\Paginator ) && $field['description']->isEmpty()))): ?><div class="help-block col-md-8 fn" style="color:#E74C3C;padding-left: 20px;"><?php echo $field['description']; ?></div><?php endif; ?>
    <div id="<?php echo $field['id']; ?>-gallery-box" class="uploader-list col-md-12 img-box <?php if(!(empty($field['value']) || (($field['value'] instanceof \think\Collection || $field['value'] instanceof \think\Paginator ) && $field['value']->isEmpty()))): ?>gallery-box-bg<?php endif; ?>">
    <?php if(!(empty($field['value']) || (($field['value'] instanceof \think\Collection || $field['value'] instanceof \think\Paginator ) && $field['value']->isEmpty()))): 
    if (is_array($field['value'])) {
    $images = $field['value'];
    } else {
    $images = explode(',',$field['value']);
    }

     if(is_array($images) || $images instanceof \think\Collection || $images instanceof \think\Paginator): if( count($images)==0 ) : echo "" ;else: foreach($images as $key=>$img): if(!(empty($img) || (($img instanceof \think\Collection || $img instanceof \think\Paginator ) && $img->isEmpty()))): ?>
    <div class="col-md-3">
        <div class="thumbnail">
            <i class="fa fa-times-circle remove-attachment"></i>
            <img class="img" src="<?php echo get_image($img); ?>" data-id="<?php echo $img; ?>">
        </div>
    </div>
    <?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
</div>

</div>
<script>
    $(function () {
        // 删除图片
        $('body').on('click', '#<?php echo $field['id']; ?>-gallery-box .remove-attachment', function() {
            var ready_for_remove_id = $(this).closest('.thumbnail').find('img').attr('data-id'); //获取待删除的图片ID
            if (!ready_for_remove_id) {
                updateAlert('错误', 'danger');
            }
            var current_picture_ids = $('#<?php echo $field['id']; ?>').val().split(","); //获取当前图集以逗号分隔的ID并转换成数组
            current_picture_ids.splice($.inArray(ready_for_remove_id,current_picture_ids),1); //从数组中删除待删除的图片ID
            $('#<?php echo $field['id']; ?>').val(current_picture_ids.join(',')) //删除后覆盖原input的值
            $(this).closest('.col-md-3').remove(); //删除图片预览图
        });

    })
</script>
                                </div>
                            </div>

                        </div>


                        <div class="tab_content" style="display: none;">

                            <!--<div class="form-group item_title">
                                <label class="col-md-2 control-label">商品详情：</label>
                                <div class="col-md-8">
                                    <?php echo widget('common/editor/ueditor',[['id'=>'content','name'=>'content','value'=>$info['content']]]); ?>
                                </div>
                            </div>-->


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
$('#type1').change(function(){
    var vals = $(this).val();
    if(vals>0){
        selectTypeTwo(vals,3,"#type2",'<option value="">二级分类</option>');
    }
});

function selectTypeTwo(parent_id,level,select_list,str){
    $.post("/Admin/Product/son_type",{parent_id:parent_id,level:level}, function(result){
        $(select_list).html(str+result['msg'])
    });
}

</script>

<script type="text/javascript">
    //规格名称添加
    $(".btn_add_list").click(function () {
        $html = '<div class="gg_list"><input type="text" class="form-control gg_input" name="data_title[]" placeholder="标题" value="" ><input type="text" class="form-control gg_input" name="data_val[]" placeholder="值" value="" ><a href="javascript:;" class="btn_delete">删除</a></div>';
        $("#controls_price").append($html);
    });
    $("#controls_price").on('click','.btn_delete',function () {
        $(this).parent('.gg_list').remove();
    })
</script>



</body>
</html>