<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:75:"D:\www\jia_zhuang\public_html/../apps//common/view/builder/listbuilder.html";i:1607065218;s:38:"../apps/common/view/builder/style.html";i:1556705925;s:43:"../apps/common/view/builder/javascript.html";i:1556705925;}*/ ?>
<style type="text/css">
hr{margin-left: 20px;}
/* Builderform样式 */
.builder .form-horizontal .control-label{color: #333;}
label.checkbox-label{margin-bottom: 15px;}
.webuploader-pick{border: none!important;}

.help-block{font-size: 13px;line-height: 24px;font-weight: 500;color: #777;padding-left: 0;}
.help-block:hover{color: #555;}
.help-block i.fa{font-size: 15px;}

.citys_field{margin-bottom: 10px;}
.citys_field select{border-radius: 0;box-shadow: none;border-color: #d2d6de;height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.42857143;color: #555;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s}
/*上传个人图像*/
.upload-avatar-box{display: block;margin-top: 20px;width: 135px;height: 130px;line-height: 120px;}
.upload-avatar-box img{max-height: 120px;max-width: 125px;}
/*多图上传*/
.gallery-box-bg{background-color: #f0f0f0;padding: 10px 5px;margin-left:3%!important;}

.uploader-list .col-md-3{padding-left:5px;padding-right: 5px;}
.uploader-list .thumbnail{width: 100%;height: 135px;margin-bottom: 15px;}
.uploader-list .thumbnail img{max-width: 100%;height: 100%;}
/* Builderlist样式 */
#selectForm{display: inline;}
.builder-toolbar .form-group { margin-top: 0px; }
.icon-menu{top:33px;}

.builder_sub_title{font-size:13px;display: inline-block;border-radius:3px;color: #666;margin-top:10px;line-height: 26px;}
/*table*/
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{font-size: 13px;}
</style>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="/static/libs/bootstrap-table/bootstrap-table.min.css">
<section class="content pt-5">
      <div class="box box-solid eacoo-box">
        <?php if(isset($show_box_header)): if($show_box_header == '1'): if(!(empty($tips) || (($tips instanceof \think\Collection || $tips instanceof \think\Paginator ) && $tips->isEmpty()))): ?><div class="box-header with-border">
                <!-- <h3 class="box-title"><?php echo $meta_title; ?></h3> -->
                <p class="f12 color-6 pt-5">提示：<?php echo (isset($tips) && ($tips !== '')?$tips:""); ?></p>

              </div>
              <?php endif; endif; endif; ?>
        <!-- Tab导航 -->
        <?php if(!(empty($tab_nav) || (($tab_nav instanceof \think\Collection || $tab_nav instanceof \think\Paginator ) && $tab_nav->isEmpty()))): ?>
            <div class="box-body pb-0">
                <div class="eacoo-tabs">
                    <div class="">
                        <ul class="nav nav-tabs">
                            <?php if(is_array($tab_nav['tab_list']) || $tab_nav['tab_list'] instanceof \think\Collection || $tab_nav['tab_list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $tab_nav['tab_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tab): $mod = ($i % 2 );++$i;?>
                                <li class="<?php if($tab_nav['current'] == $key) echo 'active'; ?>"><a href="<?php echo $tab['href']; ?>" <?php echo (isset($tab['extra_attr']) && ($tab['extra_attr'] !== '')?$tab['extra_attr']:''); ?>><?php echo $tab['title']; ?></a></li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

          <div class="box-body">
                <div class="builder listbuilder-box">

                    <!-- 顶部工具栏按钮 -->
                    <!-- 数据列表 -->
                    <div class="builder-container">
                        <div class="row">

                            <div class="builder-table col-sm-12">
                                <div id="builder-toolbar" class="toolbar">

                                    <!-- 工具栏按钮 -->
                                    <?php if(!(empty($top_button_list) || (($top_button_list instanceof \think\Collection || $top_button_list instanceof \think\Paginator ) && $top_button_list->isEmpty()))): ?>
                                        <!--<div class="form-group">-->
                                        <?php if(is_array($top_button_list) || $top_button_list instanceof \think\Collection || $top_button_list instanceof \think\Paginator): $i = 0; $__LIST__ = $top_button_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?>
                                            <a <?php if(isset($row['attribute'])): ?><?php echo $row['attribute']; endif; ?>><?php if(!(empty($row['icon']) || (($row['icon'] instanceof \think\Collection || $row['icon'] instanceof \think\Paginator ) && $row['icon']->isEmpty()))): ?><i class="<?php echo (isset($row['icon']) && ($row['icon'] !== '')?$row['icon']:''); ?>"></i><?php endif; ?> <?php echo $row['title']; ?></a>&nbsp;
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                       <!-- </div>-->
                                    <?php endif; if($search['type'] == 'custom'): ?>
                                    <!-- 搜索框 -->
                                        <div class="col-xs-12 col-sm-3 clearfix fr pr0">
                                            <form class="form form-inline" method="get" action="<?php echo $search['url']; ?>">
                                                <div class="form-group">
                                                    <div class="input-group search-form">
                                                        <input type="text" name="keyword" class="form-control search-input pull-right" value="<?php echo (\think\Request::instance()->param('keyword') ?: ''); ?>" placeholder="<?php echo $search['title']; ?>">
                                                        <span class="input-group-btn"><button type="button" class="btn btn-success search-btn"><i class="fa fa-search"></i></button></span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <table id="builder-table" class="table table-responsive table-bordered table-hover dataTable"
                                        data-pagination="true"
                                        data-toolbar="#builder-toolbar"
                                        data-query-params="queryParams"
                                        data-show-refresh="true"
                                        data-show-toggle="true"
                                        data-show-columns="true"
                                        width="100%">
                                       <thead>
                                        <tr>
                                            <th width="50" class="checkbox-toggle"
                                                data-field="state"
                                                data-checkbox="true">
                                            </th>
                                            <?php if(is_array($table_columns) || $table_columns instanceof \think\Collection || $table_columns instanceof \think\Paginator): $i = 0; $__LIST__ = $table_columns;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$column): $mod = ($i % 2 );++$i;?>
                                                <th data-field="<?php echo $column['name']; ?>" <?php echo (isset($column['extra_attr']) && ($column['extra_attr'] !== '')?$column['extra_attr']:""); ?> data-sortable="true"><?php echo $column['title']; ?></th>

                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </tr>
                                    </thead>

                                    </tbody>


                                </table>

                            </div>
                            </div>
                    </div>

                    <!-- 额外功能代码 -->
                    <?php echo $extra_html; ?>
                </div>
          </div>
      </div>

</section>
<!-- Latest compiled and minified JavaScript -->
<script src="/static/libs/bootstrap-table/bootstrap-table.min.js"></script>

<!-- Latest compiled and minified Locales -->
<script src="/static/libs/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>

<script type="text/javascript">
    var $table = $('#builder-table');
    $table.bootstrapTable({
        url:'<?php echo (isset($action_url) && ($action_url !== '')?$action_url:''); ?>',
        //checkboxHeader:false,
        //method: 'get',  //请求方式（*）
        cache: false,  //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
        striped: false,  //表格显示条纹
        showExport: true,     //是否显示导出
        exportDataType: "basic", //basic', 'all', 'selected'.
       //exportTypes:[ 'json','xml','csv', 'txt', 'sql', 'doc', 'excel','png'],  //导出文件类型
        exportTypes:[ 'excel'],  //导出文件类型
        selectItemName:'ids[]',
        <?php if($search['type'] == 'basic'): ?>
            search: true,  //是否启用查询
            searchOnEnterKey:true,
        <?php endif; ?>
        clickToSelect: true, //是否启用点击选中行
        pagination: true, //启动分页
        //dataType : "json",
        pageSize: <?php echo (isset($table_data_page['page_size']) && ($table_data_page['page_size'] !== '')?$table_data_page['page_size']:'30'); ?>,  //每页显示的记录数
        pageNumber:1, //初始化加载第一页，默认第一页
        //pageList: [5, 10, 12, 20, 25,50],  //记录数可选列表
        sidePagination: "server", //表示服务端请求
        showPaginationSwitch: false,       //是否显示选择分页数按钮
        uniqueId:'id',
        idField:'<?php echo $table_primary_key; ?>',
        //设置为undefined可以获取pageNumber，pageSize，searchText，sortName，sortOrder
        //设置为limit可以获取limit, offset, search, sort, order
        queryParamsType : "undefined",
        queryParams: function queryParams(params) {   //设置查询参数

          var param = {
              keyword:params.searchText,
              paged: params.pageNumber,
              page_size: params.pageSize,
              sort_name:params.sortName,
              //orderNum : $("#orderNum").val()
          };
          if (params.sortName!=undefined) {
            param.order = params.sortName+" "+params.sortOrder;
          }
          $('#eacoo-toolbar').find('input[name],select[name]').each(function () {
                param[$(this).attr('name')] = $(this).val();
            });
          //console.log(param);
          //console.log(params);
          return param;
        },
        // onLoadSuccess: function(res){  //加载成功时执行
        //     console.log(res);
        //   layer.msg("加载成功");
        // },
        onLoadError: function(res){  //加载失败时执行
          layer.msg("加载数据失败", {time : 1500, icon : 2});
        }

    })

    $(function () {

        //添加复选框
        $(".builder-table .checkbox-toggle>.th-inner").html('<input name="btSelectAll" type="checkbox"/>');
    });
</script>
<script type="text/javascript">
    $(function() {
        //给数组增加查找指定的元素索引方法
        Array.prototype.indexOf = function(val) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] == val) return i;
            }
            return -1;
        };

        //给数组增加删除方法
        Array.prototype.remove = function(val) {
            var index = this.indexOf(val);
            if (index > -1) {
                this.splice(index, 1);
            }
        };
        //筛选框变化触发表单提交
        $('[data-role="select_text"]').change(function(){
            $('#selectForm').submit();
        });
    });
    //筛选
    $(document).on('submit', '.form-dont-clear-url-param', function(e){
        e.preventDefault();

        var seperator = "&";
        var form = $(this).serialize();
        var action = $(this).attr('action');
        if(action == ''){
            action = location.href;
        }
        var new_location = action +'?'+ seperator + form;
        location.href = new_location;

        return false;
    });
</script>
