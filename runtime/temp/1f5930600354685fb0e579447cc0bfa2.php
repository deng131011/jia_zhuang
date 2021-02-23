<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:77:"D:\www\jia_zhuang\public_html/../apps/admin\view\upload\attachment_layer.html";i:1612424591;s:55:"D:\www\jia_zhuang\apps\admin\view\public\layerbase.html";i:1556705925;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;}*/ ?>
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

<style type="text/css">
/* .media-modal>div.modal-dialog{width:880px;}
 #attachmentInfoModal>div.modal-dialog{width:380px!important;}*/
  .modal-body{padding-right: 0;}

.media-side{height: 100%;}
ul.media-cat-list {color: #666;}
ul.media-cat-list li{display: block;font-size: 15px;cursor: pointer;padding: 6px 15px;}
ul.media-cat-list li:hover,ul.media-cat-list li.active{background-color: #f0f0f0;}

.media-li{width:95%;height: 156px;padding: 2px;}

.attachment-widget .tab-content{min-height: 460px;}

.attachment-widget .nav-tabs>li.active{border-bottom: none;}
.attachment-widget .nav-tabs>li.active>a,.attachment-widget .nav-tabs>li.active>a:focus,.attachment-widget .nav-tabs>li.active>a:hover{border-left:1px solid #ddd;border-right:1px solid #ddd;}
.attachment-widget .nav-tabs-custom{box-shadow:none;}
.attachment-widget .nav-tabs-custom>.nav-tabs>li{margin-bottom:-1px;}

.attachment-widget .form-group{margin-top: 0;}
.attachment-content{overflow-y: scroll;height:430px;}
.attachment-content .cover {
    width: 95%;
    height:130px;
    margin-left: 1px;
    position: absolute;
    background-size: cover;
    background: url('/static/admin/img/icon_card_selected.png') no-repeat 0 0;
    vertical-align: middle;
    display:none;
    background-position: 50% 50%;-moz-opacity: .6;
    -khtml-opacity: .6;
    opacity: .6;
    background-color: #000;
    filter: alpha(opacity=60);
    margin-top: -155px;
}
.attachment-content .media-thumb{height: 130px;line-height: 130px;}

.showAttachmentInfo{position: absolute;bottom: 0;width: 128px;}
/*分页*/
#ajax-more-attachment li span{cursor: pointer;}
/*弹窗*/
.media-modal-pic{height: 160px;line-height: 160px;}

#url_preview_img{max-width: 460px;text-align: center;margin-top: 10px;margin:10px auto;}
#url_preview_img img{max-width:100%;}

span.show-media-info{width: 20px;text-align:center;}

.media-modal-foot{background-color:#f4f5f9;margin-bottom: -15px;margin-left: -15px;}

.pagination{margin: 10px 0;}
</style>

<style type="text/css">
	/*解决单选框换行问题*/
	.field-type-radio{float: none!important;display: block;}
</style>
<body class="hold-transition skin-blue">
<div class="layer-wrapper">
  <section class="content" style="margin: 0 10px;">
    <div class="row">
    
  <div class="attachment-widget">
      <div class="eacoo-tabs nav-tabs-custom">
        <ul class="nav nav-tabs" style="margin-top:0px;">
          <li class="active"><a href="#tab-media-content"  data-toggle="tab" aria-expanded="true">媒体文件</a></li>
          <li class=""><a href="#tab-media-url"  data-toggle="tab" aria-expanded="false">从URL插入</a></li>
          <li style="float: right!important;margin-right: 20px;"><span id="upload_attachment_btn" class="btn btn-info btn-sm"><i class="fa fa-cloud-upload"></i> 本地上传</span> </li>
        </ul>
    </div>
    <!--此页面主体内容-->   
  <div class="tab-content">
    <div class="tab-pane active" id="tab-media-content">
    <div class="row pl-75 mt-20 ml0 mr0 pl0">
      <div class="col-md-2 col-sm-2 pd0 media-side">
        <ul class="media-cat-list">
          <li <?php if(empty(\think\Request::instance()->param('cat')) || ((\think\Request::instance()->param('cat') instanceof \think\Collection || \think\Request::instance()->param('cat') instanceof \think\Paginator ) && \think\Request::instance()->param('cat')->isEmpty())): ?>class="active"<?php endif; ?>><a href="<?php echo url('admin/Upload/attachmentLayer',['input_id_name'=>$input_id_name,'path_type'=>$path_type,'gettype'=>$gettype]); ?>">全部<span class="color-8 f14"> (<?php echo $media_totalCount; ?>)</span></a></li>
          <?php if(is_array($media_cats) || $media_cats instanceof \think\Collection || $media_cats instanceof \think\Paginator): $i = 0; $__LIST__ = $media_cats;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?>
            <li <?php if(\think\Request::instance()->param('cat') == $row['term_id']): ?>class="active"<?php endif; ?> ><a href="<?php echo url('admin/Upload/attachmentLayer',['input_id_name'=>$input_id_name,'path_type'=>$path_type,'gettype'=>$gettype,'cat'=>$row['term_id']]); ?>"><?php echo $row['name']; ?><span class="color-8 f14"> (<?php echo $row['count']; ?>)</span></a></li>
          <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
      </div>
    <div class="col-md-10 col-sm-10 pd0">
      <!-- <div class="row ml-10 mr0 mt-0 mb-10" >
        <div class="col-xs-10 col-sm-10 button-list clearfix pl0">  
         <form id="selectForm" class="form" method="get" action="<?php echo url('index'); ?>">
            <div class="col-md-3 pl0">                                                
              <select class="form-control">
              <option value="0">选择类型</option>
                <?php if(is_array($mediaTypeList) || $mediaTypeList instanceof \think\Collection || $mediaTypeList instanceof \think\Paginator): $i = 0; $__LIST__ = $mediaTypeList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mediaType): $mod = ($i % 2 );++$i;?>
                      <option value="<?php echo $key; ?>" <?php if($key == \think\Request::instance()->get('media_type')): ?>selected<?php endif; ?>><?php echo $mediaType; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
              </select>
              </div>
              <div class="col-md-4">
               <select name="date_range" id="date_range" class="form-control">
                    <option value="0">选择日期</option>
                    <option >今天</option>
                    <option>最近3天</option>
                    <option>最近7天</option>
                    <option>最近30天</option>
                </select>
              </div>

             <span class="btn btn-info btn-sm" id="choiceForm">筛选</span>

          </form>
         </div>
        
         </div>
        <hr> -->
        <div>
          <ul class="attachment-content">
          <?php if(empty($data_list) || (($data_list instanceof \think\Collection || $data_list instanceof \think\Paginator ) && $data_list->isEmpty())): ?>
            <div class="tc mt80">空空如也！~_~</div>
          <?php else: if(is_array($data_list) || $data_list instanceof \think\Collection || $data_list instanceof \think\Paginator): $i = 0; $__LIST__ = $data_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?>
               <li class="col-sm-6 col-md-3 col-lg-3 mb-10" data-id="<?php echo $row['id']; ?>" data-src="<?php echo $row['path']; ?>">
                  <div class="box-style media-li">
                    <div class="tc media-thumb">     
                        <img src="<?php echo $row['thumb_src']; ?>" alt="<?php echo $row['alt']; ?>" style="width:100%;max-height:100%;">
                    </div>
                    <div class="f13 mt-10 showAttachmentInfo" data-id="<?php echo $row['id']; ?>">
                    <a href="" class="color-8" data-toggle="modal" data-target="#attachmentInfoModal">
                       <span class="w80 disline oh nowarp"><?php echo $row['name']; ?></span>
                          <span data-toggle="tooltip" data-placement="bottom" data-original-title="详情" class="right color-icon show-media-info">
                             <i class="fa fa-info color-6"></i>
                          </span>   
                       </a>
                    </div>
                  </div>
                  <div class="cover cancelSelectImage" data-id="<?php echo $row['id']; ?>"></div>
                </li>
            <?php endforeach; endif; else: echo "" ;endif; endif; ?>
          </ul> 
          </div> 
      </div>  

  </div>

  <div class="oh media-modal-foot">
      <div class="col-md-12 col-sm-12 tc"><?php echo $data_page; ?></div>
        <input type="hidden" id="input_name" value="<?php echo $input_id_name; ?>">
        <input type="hidden" id="attachment_ids" value="">
        <input type="hidden" id="attachment_srcs" value="">

    </div>
  </div>

  <!--从URL插入容器-->
  <div class="tab-pane" id="tab-media-url">
    <div class="row">
        <div class="col-md-8">
          <label for="onlinefile_url">附件网络地址：<span class="color-6 f12">(请输入图片地址)</span></label>
          <input type="text" class="form-control" name="onlinefile_url" id="onlinefile_url" placeholder="http://">
        </div>
    </div>
    <div class="oh row">
        <div id="url_preview_img">
          
        </div>
        <p class="text-danger f13 mt-20 ml-20">点击添加会自动添加到如图容器。</p>
        <div class="tc mt-30 tr mr-50">
          <span id="addonlinefile" class="btn btn-info">添加</span>
        </div>
    </div>

  </div>
</div><!--end tab-content-->

</div>

    </div>
  </section>
</div>
<script  type="text/javascript" src="/static/libs/nice-validator/jquery.validator.min.js?local=zh-CN"></script>
<!-- <script type="text/javascript" src="/static/assets/js/bootstrap.min.js"></script> -->
<script type="text/javascript" src="/static/libs/iCheck/icheck.min.js"></script>
<script type="text/javascript" src="/static/libs/layer/layer.js"></script>
<script type="text/javascript" src="/static/libs/toastr/toastr.min.js"></script>
<script type="text/javascript" src="/static/admin/js/common.js" ></script>
<script type="text/javascript" src="/static/admin/js/base.js" ></script>

<script type="text/javascript" src="/static/assets/js/bootstrap.min.js"></script>
<link href="/static/libs/webuploader/css/webuploader.css" type="text/css" rel="stylesheet">
<!-- webuploader -->
<script type="text/javascript" src="/static/libs/webuploader/js/webuploader.min.js"></script>
  <?php if($attachmentDaterangePicker): ?>
      <link href="/static/libs/daterangepicker/daterangepicker-bs3.css" type="text/css" rel="stylesheet">
      <script src="/static/libs/daterangepicker/moment.min.js"></script>
      <script src="/static/libs/daterangepicker/daterangepicker.js"></script>
          <script type="text/javascript">
              //Date range as a button
            $('#choice_date_range').daterangepicker(
                {
                  ranges: {
                    '今天': [moment(), moment()],
                    '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '最近7天': [moment().subtract(6, 'days'), moment()],
                    '最近30天': [moment().subtract(29,'days'), moment()],
                  },
                  startDate: moment().subtract(29, 'days'),
                  endDate: moment()
                },
                function (start, end) {
                  $('#choice_date_range').val(start.format('YYYY/MM/DD') + '—' + end.format('YYYY/MM/DD'));
                }
            );
      </script>
  <?php endif; ?>
  <script type="text/javascript">
  var gettype = "<?php echo (isset($gettype) && ($gettype !== '')?$gettype:"single"); ?>";//选择类型
     //选择当前图片
    function cancelSelectImage(obj,img_id){
        obj.style.display="none";
      }
     /*弹窗*/
    $(function () { 

      $('body').on('click','.showAttachmentInfo', function() {

          var $this = $(this);
            var attachmentId = $this.attr('data-id');
            parent.layer.open({
            type: 2,
            title: '附件信息',
            shadeClose: true,
            shade: 0.8,
            area: ['620px','460px'],
            content: url('admin/Attachment/info',['id='+attachmentId]), 
            btn: ['提交','关闭'],
            yes: function(index, layero){
                var p_layer = parent.layer;
                var alt = p_layer.getChildFrame('#alt', index).val();
                var term_id = p_layer.getChildFrame('#file-termid', index).val();
                $.post(url("admin/Attachment/edit"),{id:attachmentId,alt:alt,term_id:term_id},function(result){
                  if (result.code==1) {
                    parent.updateAlert(result.msg,'success');
                    p_layer.closeAll();
                  } else{
                    parent.updateAlert(result.msg,'success');
                    
                  }
              });
            }
        });
        })
      
        //选中当前图片
      $('body').on('click','.attachment-content li', function() {

          var $this              = $(this);
            var attachment_ids_obj = $("#attachment_ids");
            var attachment_ids_val = attachment_ids_obj.val();
            
            var id  = $this.attr('data-id');
            var src = $this.attr('data-src');
            if (gettype=='single') {
                $(".attachment-content").find('div.cover').hide();
                attachment_ids_obj.val(id);
                $("#attachment_srcs").val(src);
            } else if(gettype=='multiple'){
                attachment_ids_obj.val(attachment_ids_val+id+ ',');
            }
            /*处理开关选中start*/
             var current_attachment_ids = attachment_ids_val.split(","); //获取当前图集以逗号分隔的ID并转换成数组
             var id_position=$.inArray(id,current_attachment_ids);//获取当前对象id在图集id中的位置
             //alert(id_position);
             if (id_position>-1) {
                current_attachment_ids.splice(id_position,1); //从数组中删除待删除的图片ID
                attachment_ids_obj.val(current_attachment_ids.join(',')) //删除后覆盖原input的值
                $this.find('div.cover').hide();//同时选中取消
                if (gettype=='single') {
                    $("#attachment_srcs").val('');
                }
             } else{
                $this.find('div.cover').show();
             } 
              /*处理开关选中start*/
            
          })

        //图片外链URL编辑框下的预览图片
          // $('#onlinefile_url').each(function(){
          //   $(this).bind('change focus blur', function(){
          //     $value = $(this).val();
          //     if ($value!=='undefined') {
          //           $image = '<img src ="'+$value+'" />';
          //           var $image = $('#url_preview_img').html('').append($image).find('img');
          //           window.setTimeout(function(){
          //             if(parseInt($image.attr('width')) < 20){
          //               $('#url_preview_img').html('');
          //             }
          //           },500);
          //     };

          //   });
          // });
          //图片选择器URL添加按钮
           $("#addonlinefile").click(function () {
                 var $this = $(this);
                 var ids;
                 var src = $("#onlinefile_url").val();
                 $.post("<?php echo url('admin/Attachment/uploadOnlinefile'); ?>", {src:src}, function (res) {
                     console.log(res);
                     var id=res.id;
                     //javascript:window.parent.setAttachmentInputVal($this.data('id'),ids,src);

                     $('#attachmentModal').modal('hide')
                 })
           })
          

            //本地上传
            var uploader_attachment= WebUploader.create({
                // 选完文件后，是否自动上传。
                auto: true,
                duplicate: true,// 同一文件是否可以重复上传
                // swf文件路径
                swf: '/static/assets/libs/webuploader/Uploader.swf',
                // 文件接收服务端。
                server: "<?php echo url('admin/Upload/upload',['path_type'=>$path_type,'type'=>'picture']); ?>",
                //验证文件总数量, 超出则不允许加入队列
                fileNumLimit: 5,
                // 如果此选项为false, 则图片在上传前不进行压缩
                compress: false, 
                // 验证单个文件大小是否超出限制, 超出则不允许加入队列 
                fileSingleSizeLimit:<?php echo intval(config('attachment_options.image_max_size')) ? : 2*1024*1024; ?>,  
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.

                //选择文件的按钮
                pick: '#upload_attachment_btn',
                // 只允许选择图片文件
                accept:{title:'Images',extensions:'<?php echo config("attachment_options.image_exts"); ?>',mimeTypes:'image/*'}
            });
            uploader_attachment.on('fileQueued', function (file) {
                uploader_attachment.upload();
            });
            /*上传成功**/
            uploader_attachment.on('uploadSuccess', function (file, result) {
                if (result.code==1) {
                    data = result.data;
                    var append_string='<li class="col-sm-6 col-md-3 col-lg-3 mb-10" data-id="'+data.id+'" data-src="'+data.path+'"><div class="box-style media-li"><div class="tc"><img src="'+data.path+'" alt="" style="max-width:100%;max-height:100%;"></div><div class="f13 mt-5 showAttachmentInfo" data-id="'+data.id+'"><a href="" class="color-6" data-toggle="modal" data-target="#attachmentInfoModal"><span class="w80 disline oh nowarp">'+data.name+'</span><span data-toggle="tooltip" data-placement="bottom" data-original-title="新" class="right color-icon show-media-info"><i class="fa fa-info-circle color-success"></i></span></a></div></div><div class="cover cancelSelectImage" data-id="'+data.id+'" style="display: none;"></div></li>';
                    $(".attachment-content").prepend(append_string);

                    uploader_attachment.reset();
                } else {
                    updateAlert(result.msg);
                    setTimeout(function () {
                        $(this).removeClass('disabled').prop('disabled', false);
                    }, 1500);
                }
            });
      })

    </script>
  
<script type="text/javascript">
	$('input').iCheck({
              checkboxClass:'icheckbox_minimal-blue',
              radioClass:'iradio_minimal-blue',
              increaseArea:'20%' // optional
        });
</script>
  </body>
</html>