<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:74:"G:\phpstudy\www\jia_zhuang\public_html/../apps/admin\view\spike\lists.html";i:1573785646;s:74:"G:\phpstudy\www\jia_zhuang\public_html/../apps/admin/view/public/base.html";i:1563871977;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;s:67:"G:\phpstudy\www\jia_zhuang\apps\admin\view\public\between_time.html";i:1563865529;}*/ ?>
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
     
<link rel="stylesheet" href="/static/libs/bootstrap-table/bootstrap-table.min.css">

<!----- 搜索 ----->
<section class="row-content pt-5">
    <div class="box box-default">
        <div class="box-header with-border pb-0">
            <h3 class="box-title">高级查询</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <form id="search_form" action="<?php echo url(''); ?>" method="get">
        <div class="box-body" id="eacoo-toolbar">
            <div class="form-inline" role="form">

                <div class="form-group mt-10">
                    <input type="text"  class="form-control" name="keywords" value="<?php echo $get['keywords']; ?>" placeholder="内容关键词">
                </div>
                <input type="hidden" name="excel" id="excel" value="" />
                <div class="form-group mt-10">
                    <button type="button" class="btn btn-success search-btn" id="search">查询</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</section>

<section class="content pt-5">
        <div class="box-body" style="background: #fff;">
            <div class="builder listbuilder-box">
                <!-- 顶部工具栏按钮 -->
                <!-- 数据列表 -->
                <div class="builder-container">
                    <div class="row">

                        <div class="builder-table col-sm-12">
                        <div class="bootstrap-table">
                                <div class="fixed-table-toolbar"><div class="bs-bars pull-left">
                                    <div id="builder-toolbar" class="toolbar">
                                    <!-- 工具栏按钮 -->
                                    <!--<div class="form-group">-->
                                        <a title="启用" target-form="ids" icon="fa fa-play" class="btn btn-success ajax-table-btn confirm btn-sm" href="<?php echo url('setstatus',array('status'=>'resume','model'=>'SpikeProduct')); ?>" data-my="1" primary-key="id"><i class="fa fa-play"></i> 启用</a>&nbsp;
                                        <a title="禁用" target-form="ids" icon="fa fa-pause" class="btn btn-warning ajax-table-btn confirm btn-sm" confirm-info="您确定要执行禁用操作吗？" href="<?php echo url('setstatus',array('status'=>'forbid','model'=>'SpikeProduct')); ?>" data-my="1" primary-key="id"><i class="fa fa-pause"></i> 禁用</a>&nbsp;
                                        <a title="删除" target-form="ids" icon="fa fa-remove" class="btn btn-danger ajax-table-btn confirm btn-sm" confirm-info="您确定要执行删除操作吗？" href="<?php echo url('setstatus',array('status'=>'delete','model'=>'SpikeProduct')); ?>" data-my="1" primary-key="id"><i class="fa fa-remove"></i> 删除</a>
                                    <!-- </div>-->


                                    </div>
                                </div>

                                <div class="fixed-table-container fixed_table_list" style="padding-bottom: 0px;">
                                    <div class="fixed-table-header" ></div>
                                    <table id="builder-table" class="table table-responsive table-bordered table-hover dataTable" data-pagination="true" data-toolbar="#builder-toolbar" data-query-params="queryParams" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" width="100%">
                                        <thead>
                                        <tr>
                                            <th class="bs-checkbox checkbox-toggle" style="width: 36px;" data-field="state">
                                                <input name="btSelectAll" type="checkbox" >
                                                <div class="fht-cell"></div>
                                            </th>
                                            <th style="">
                                                <div class="th-inner sortable both">商品名称</div>
                                            </th>
                                            <th style="" >
                                                <div class="th-inner sortable both">秒杀价格</div>
                                            </th>
                                            <th style="" >
                                                <div class="th-inner sortable both">设置数量</div>
                                            </th>
                                            <th style="" >
                                                <div class="th-inner sortable both">剩余数量</div>
                                            </th>
                                            <th style="" >
                                                <div class="th-inner sortable both">禁用状态</div>
                                            </th>
                                            <th style="" >
                                                <div class="th-inner sortable both">操作</div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <tr data-index="<?php echo $key; ?>" data-uniqueid="<?php echo $vo['id']; ?>">
                                            <td class="bs-checkbox">
                                                <input data-index="<?php echo $key; ?>" name="ids[]" type="checkbox" value="<?php echo $vo['id']; ?>">
                                            </td>
                                            <td style=""><?php echo $vo['title']; ?></td>
                                            <td style="">￥<?php echo $vo['price']; ?></td>
                                            <td style=""><?php echo $vo['ms_stock']; ?></td>
                                            <td style=""><?php echo $vo['mssy_stock']; ?></td>
                                            <td style=""><?php echo forbidenStatus($vo['status']); ?></td>
                                            <td style="">
                                                <?php if($vo['status'] == 1): ?>
                                                <a class="btn btn-warning btn-xs ajax-get confirm" href="<?php echo url('setstatus',array('status'=>'forbid','model'=>'SpikeProduct','ids'=>$vo['id'])); ?>">禁用</a>
                                                <?php else: ?>
                                                <a class="btn btn-success btn-xs ajax-get confirm" href="<?php echo url('setstatus',array('status'=>'resume','model'=>'SpikeProduct','ids'=>$vo['id'])); ?>">启用</a>
                                                <?php endif; ?>
                                                <a class="btn btn-success btn-xs" href="<?php echo url('edit_pro',array('id'=>$vo['id'])); ?>">编辑</a>
                                                <a title="删除" icon="fa fa-remove" class="btn btn-danger btn-xs ajax-get confirm" confirm-info="您确定要执行删除操作吗？" href="<?php echo url('delOrder',array('status'=>'delete','ids'=>$vo['id'])); ?>" style="margin-right:6px;"><i class="fa fa-remove"></i> </a>
                                            </td>

                                        </tr>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </tbody>
                                    </table>

                                </div>
                                <div class="fixed-table-pagination" style="display: block;">
                                    <div class="pull-left pagination-detail">
                                        <span class="pagination-info">总共 <?php echo $list->total(); ?> 条记录</span>
                                    </div>
                                    <div class="pull-right pagination">
                                        <?php echo $list->render(); ?>
                                    </div>
                                </div>
                        </div>
                    </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
</section>


<link href="/static/libs/daterangepicker/daterangepicker-bs3.css" type="text/css" rel="stylesheet">
<script src="/static/libs/daterangepicker/moment.min.js"></script>
<script src="/static/libs/daterangepicker/daterangepicker.js"></script>

<script type="text/javascript">
    $(function(){
        $('#<?php echo $field['id']; ?>').daterangepicker({
            format:"YYYY-MM-DD",
            singleDatePicker: false,
            showDropdowns: true,
            minDate:'2000-01-01',
            maxDate:'2030-01-01',
            startDate:'2019-01-01',
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                '今天': [moment(), moment()],
                '昨天': [moment().subtract('days', 1), moment().subtract('days', 1)],
                '最近7天': [moment().subtract('days', 6), moment()],
                '最近30天': [moment().subtract('days', 29), moment()],
                '上一个月': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            opens: 'right',
            locale : {
                applyLabel : '确定',
                cancelLabel : '取消',
                fromLabel : '起始时间',
                toLabel : '结束时间',
                customRangeLabel : '自定义',
                daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
                firstDay : 1
            }
        });
    });
</script>

<script>
    var $table = $('#builder-table');

    //导出excel
    $(".btn_excel").click(function () {
        $("#excel").val('excel');
        $("#search_form").submit();

    });


</script>


     
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

</body>
</html>