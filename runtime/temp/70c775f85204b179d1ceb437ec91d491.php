<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:74:"G:\phpstudy\www\jia_zhuang\public_html/../apps/admin\view\index\index.html";i:1556705925;s:61:"G:\phpstudy\www\jia_zhuang\apps\admin\view\public\header.html";i:1613786607;s:46:"../apps/admin/view/public/document_header.html";i:1564739010;s:59:"G:\phpstudy\www\jia_zhuang\apps\admin\view\public\left.html";i:1608099630;s:61:"G:\phpstudy\www\jia_zhuang\apps\admin\view\public\footer.html";i:1613786510;}*/ ?>
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

<body class="hold-transition skin-purple sidebar-mini fixed">
<!-- <body class="hold-transition skin-blue sidebar-mini fixed"> -->
<div class="wrapper">
    <header class="main-header">
      <!-- Logo -->
      <a href="<?php echo url('admin/index/index'); ?>" class="logo" style="font-size: 34px; font-weight: bold;font-family: cursive;" >
           <span class="logo-lg"><b></b></span>
      </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>



      <?php if(!IS_MOBILE){ ?>
        <script id="collect_top_menus" type="text/html">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-star" style="font-size: 20px!important;"></i></a>
            <ul class="dropdown-menu" role="menu">
                <% for (var i = 0; i < data.length; i ++) { %>
                <% var vo = data[i]; %>
                  <li><a href="<%=vo.url %>" class="opentab" tab-name="navtab-collapse-<%=vo.title %>" style="font-weight:bold!important;"><i class="fa fa-circle-o"></i> <%=vo.title %></a></li>
                <% } %>
                <% if (data.length==0) { %>
                    <li class="f13 ml-10 color-6">Tips: 点击<i class="fa fa-star-o"></i>可添加菜单收藏</li>
                <% } %>
            </ul>
        </script>
        <ul class="nav navbar-nav">


        </ul>

        <script id="header_modules_menus" type="text/html">

            <% for (var i = 0; i < data.length; i ++) { %>
            <% var vo = data[i]; %>
            <li <% if (vo.default_header_menu_module==1) { %> class="active"<% } %>><a href="#" data-dependtype="1" data-dependflag="<%=vo.name %>" style="font-weight:bold!important;"><i class="<%=vo.icon %>"></i> <%=vo.title %></a></li>
            <% } %>
        </script>
        <ul class="nav navbar-nav" id="header-modules-menus">

        </ul>
      <?php } ?>


      <div class="navbar-custom-menu">
          <div style="float: left; line-height: 50px; height: 50px; margin-right:50px;">
            <!--  <a href="<?php echo url('admin/Comment/newmsg'); ?>" class="btn btn-flat opentab" style="color: #fff; font-size: 16px;"><i class="fa fa-commenting-o" style="color: #fff;"></i> <?php echo $msgcount; ?></a>-->
          </div>
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->

          <!-- Notifications: style can be found in dropdown.less -->
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-user-circle"></i>
              <span class="hidden-xs"><?php echo $current_user['nickname']; ?></span>
            </a>

            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">

                <p>
                  <?php echo $current_user['nickname']; ?> - 
                  <small>注册时间：<?php echo $current_user['create_time']; ?></small>
                </p>
              </li>
              <!-- Menu Body -->
              <!--<li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">关注</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">朋友</a>
                  </div>
                </div>

              </li>-->
              <!-- Menu Footer-->

              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo url('admin/AdminUser/profile',array('uid'=>$current_user['uid'])); ?>" class="btn btn-default btn-flat opentab" tab-name="navtab-collapse-profile">个人资料</a>
                  <a href="<?php echo url('admin/AdminUser/resetPassword',['uid'=>$current_user['uid']]); ?>" class="btn btn-default btn-flat opentab" tab-name="navtab-collapse-resetPassword">修改密码</a>
                </div>
                <div class="pull-right">
                  <a href="javascript:void(0);" class="loginout btn btn-default btn-flat">退出</a>
                </div>
              </li>
            </ul>
          </li>
          <li data-toggle="tooltip" data-placement="bottom" title="" data-original-title="清除缓存">
            <a class="ajax-get" href="<?php echo url('admin/index/delcache'); ?>"><i class="fa f16 fa-trash-o"></i></a>
          </li>
         <!-- <li data-toggle="tooltip" data-placement="bottom" title="" data-original-title="打开前台">
            <a href="/" target="_blank"><i class="fa fa-desktop"></i></a>
          </li>-->
          <li>
            <a href="javascript:;" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <aside class="main-sidebar sidebar-wrapper">
    <section class="sidebar" id="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel" style="height: 40px;">
        <div class="pull-left image">

        </div>
        <div class="pull-left info">
          <p><i class="fa fa-user-circle img-circle" style="height: auto; margin-right:10px;"></i><a><?php echo $current_user['nickname']; ?></a></p>

        </div>
      </div>
      <ul class="sidebar-menu" id="sidebar-menus">
       
      </ul>
    </section>
  </aside>

  <script id="sidebar_menus" type="text/html">

    <% for (var i = 0; i < data.length; i ++) { %>
        <% var vo = data[i]; %>
        <% var child = data[i]._child; %>
        <li class="<% if(child){ %> treeview <% }else{ %>no_tree<% } %>">
          <a href="<% if(child){ %>#<% }else{ %> <%=vo.url %> <% } %>" <% if(!child){ %> class="opentab" tab-name="navtab-collapse-<%=vo.id %>" <% } %> >
              <i class="fa <% if(vo.icon){ %> {{vo.icon}} <% }else{ %>fa-circle-o <% } %>"></i>
                <span>{{vo.title}}</span>
                <% if(child){ %><i class="fa fa-angle-left pull-right"></i> <% } %>
            </a>
          <% if(child){ %>
          <ul class="treeview-menu">
            <% for (var j = 0; j < child.length; j ++) { %>
                <% var v = child[j]; %>
                <% var _child = child[j]._child; %>
                <% if(!_child){ %>
                  <li ><a href="{{v.url}}" class="opentab" tab-name="navtab-collapse-<%=v.id %>"><i class="fa <% if(v.icon){ %> {{v.icon}} <% }else{ %>fa-circle-o <% } %>"></i> <%=v.title %></a> </li>
                  <% }else{ %>
                   <li ><a href="#" ><i class="fa <% if(v.icon){ %> {{v.icon}} <% }else{ %>fa-circle-o <% } %>"></i> <%=v.title %> <i class="fa fa-angle-left pull-right"></i></a>
                     <ul class="treeview-menu">
                        <% for (var k = 0; k < _child.length; k ++) { %>
                            <% var _v = child[j]; %>
                            <li><a href="{{_v.url}}" class="opentab" tab-name="navtab-collapse-<%=v.id %>"><i class="fa f<% if(_v.icon){ %> {{_v.icon}} <% }else{ %>fa-circle-o <% } %>"></i> <%=_v.title %></a></li>
                        <% } %>
              
                   </ul>
                    </li>
                <% } %>
            <% } %>             
         </ul>
         <% } %>
      </li>
    <% } %>


    <% if (data.length==0) { %>
        <div class="no-data-div">
          <div class="no-data-icon">
            <i class="iconfont">&#xe612;</i>
          </div>
          <dl>
            <dt>暂无菜单</dt>
            <dd>数据出错</dd>
          </dl>
          
        </div>
    <% } %>
</script>

<style type="text/css">
    .toast-top-right{top: 56px!important;}
</style>
<div class="content-wrapper">
	<!-- 多标签后台 -->
    <nav class="navbar navbar-default eacoo-tab-nav" role="navigation">
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="#" id="tab-left"><i class="fa fa-caret-left"></i></a></li>
            </ul>
            <div class="eacoo-tab-wrap clearfix">
                <ul class="nav navbar-nav nav-close eacoo-tab">
                    <li class="active" >
                        <a href="#navtab-collapse-1" role="tab" data-toggle="tab"><i class="fa fa-dashboard"></i> <span>首页</span></a>
                    </li>
                </ul>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" id="tab-right"><i class="fa fa-caret-right"></i></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">操作 <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="close-all">关闭所有</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
     <div class="tab-content eacoo-tab-content">

		<div role="tabpanel" class="dashboard-container tab-pane fade in active" id="navtab-collapse-1">
	   		<iframe name="navtab-collapse-1" src="<?php echo url('admin/dashboard/index'); ?>" class="iframe"></iframe>
	  	</div><!--dashboard end-->

	</div>
</div>

<!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" id="control-sidebar-tab-content">

      <!-- /.tab-pane -->
    </div>
  </aside>

<!-- /.content-wrapper -->
  <footer class="main-footer">
    
    <strong> 感谢使用 <a href="<?php echo config('eacoo_api_url'); ?>" target="_blank">家装</a> </strong>管理系统
   <!-- <div class="pull-right hidden-xs">

        <?php if(isset($eacoo_version)): ?><a class="text-danger mr-10 " target="_blank" href="https://forum.eacoophp.com/t/updating">发现了最新版本:V<?php echo (isset($eacoo_version['version']) && ($eacoo_version['version'] !== '')?$eacoo_version['version']:''); ?></a><?php endif; ?>
      <b>Version</b> <?php echo EACOOPHP_V; if($accredit_status != 'yes'): ?><a class="text-warning" href="<?php echo config('eacoo_api_url'); ?>/pricing">[未授权]</a><?php endif; ?>
    </div>-->
  </footer>
 <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<div id="loading" style="top: 150px;">
    <div class="lbk"></div>
    <div class="lcont"><img src="/static/assets/img/loading.gif" alt="loading...">正在加载...</div>
</div>

<script type="text/javascript" src="/static/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/assets/js/jquery.cookie.js"></script>

<!-- Slimscroll -->
<script src="/static/libs/slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script type="text/javascript" src="/static/libs/fastclick/fastclick.min.js"></script>
<script type="text/javascript" src="/static/admin/js/app.min.js" ></script>
<script type="text/javascript" src="/static/admin/js/layout.js" ></script>
<!-- <script type="text/javascript" src="/static/assets/js/think.js"></script> -->
<script type="text/javascript" src="/static/libs/layer/layer.js"></script>
<script type="text/javascript" src="/static/libs/artTemplate/template.js"></script>

<script type="text/javascript" src="/static/libs/toastr/toastr.min.js"></script>

<script type="text/javascript" src="/static/admin/js/common.js?v=<?php echo EACOOPHP_V; ?>" ></script>
<script type="text/javascript" src="/static/admin/js/eacoo.js?v=<?php echo EACOOPHP_V; ?>" ></script>

<script type="text/javascript">
$(function(){
  <?php if(isset($eacoo_version)): ?>
  //检测新版本

  <?php endif; ?>
  
});

</script>
  </body>
</html>
<script type="text/javascript">
    loadHeaderModulesMenus('admin',1);//加载顶部模块菜单
    loadTopMenus();//加载收藏菜单
    loadSidebarMenus();//加载菜单
    var eacoo_tab_content_height = $('.eacoo-tab-content').height();
    (function ($) {
        //加载上次页面窗口
        if (localStorage.getItem('latest_iframe_tab')) {
            refreshIframe();
        }
        $("#navtab-collapse-1 iframe").load(function(){
            //autoHeight(document.getElementById(tab_name));
            //var mainheight = $(this).contents().find("body").height()+30;
            var mainheight = eacoo_tab_content_height+20;
            $(this).height(mainheight);
        });
    })(jQuery);
    
</script>