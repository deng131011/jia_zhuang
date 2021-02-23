<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:84:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/builder/formbuilder.html";i:1562295570;s:38:"../apps/common/view/builder/style.html";i:1556705925;s:43:"../apps/common/view/builder/javascript.html";i:1556705925;}*/ ?>
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
                <div class="builder formbuilder-box">
                  <div class="row">

                    <div class="col-md-11" style="padding: 20px;margin-left:30px;border-radius:3px;">
                        <form action="<?php echo (isset($post_url) && ($post_url !== '')?$post_url:''); ?>" method="post" class="form-builder form-horizontal" data-validator-option="{theme:'bootstrap', timely:2, stopOnError:true}">
                        <fieldset>
                            <?php if(is_array($fieldList) || $fieldList instanceof \think\Collection || $fieldList instanceof \think\Paginator): $k = 0; $__LIST__ = $fieldList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($k % 2 );++$k;if(!in_array($field['type'],['group','section','self_html']) && !isset($field['FormBuilderExtend'])){ ?>
                                    <div class="form-group item_<?php echo (isset($field['name']) && ($field['name'] !== '')?$field['name']:''); ?> <?php echo (isset($field['extra_class']) && ($field['extra_class'] !== '')?$field['extra_class']:''); ?>">
                                        <label for="<?php echo (isset($field['name']) && ($field['name'] !== '')?$field['name']:''); ?>" class="col-md-2 control-label"><?php echo (isset($field['title']) && ($field['title'] !== '')?$field['title']:""); ?></label>
                                        <div class="<?php echo (isset($field['extra']['field_body_class']) && ($field['extra']['field_body_class'] !== '')?$field['extra']['field_body_class']:'col-md-4'); ?>" <?php echo (isset($field['extra']['field_body_extra']) && ($field['extra']['field_body_extra'] !== '')?$field['extra']['field_body_extra']:''); ?>>
                                        <?php echo action('common/BuilderForm/fieldType',['field'=>$field],'builder'); ?>
                                        </div>
                                        <?php if(!(empty($field['description']) || (($field['description'] instanceof \think\Collection || $field['description'] instanceof \think\Paginator ) && $field['description']->isEmpty()))): ?>
                                            <div class="<?php echo (isset($field['extra']['field_help_block_class']) && ($field['extra']['field_help_block_class'] !== '')?$field['extra']['field_help_block_class']:'col-md-5'); ?> help-block"><i class="fa fa-info-circle color-info1"></i> <?php echo (isset($field['description']) && ($field['description'] !== '')?$field['description']:""); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php }else{ ?>
                                    <?php echo action('common/BuilderForm/fieldType',['field'=>$field],'builder'); } endforeach; endif; else: echo "" ;endif; ?>
                            <hr/>
                            <div class="form-group">
                                <div class="col-md-10 col-xs-offset-2 mt-10 tc">
                                 <?php if(is_array($button_list) || $button_list instanceof \think\Collection || $button_list instanceof \think\Paginator): $i = 0; $__LIST__ = $button_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$button): $mod = ($i % 2 );++$i;?>
                                    <div class="col-md-3 col-xs-6">
                                        <button <?php echo $button['attr']; ?>><?php echo $button['title']; ?></button>
                                    </div>
                                 <?php endforeach; endif; else: echo "" ;endif; ?>

                              </div>
                            </div>

                            </fieldset>
                        </form>

                    </div>
                 </div>
             </div>
            
            <?php echo $extra_html; ?>

          </div>
      </div>

</section>

<?php if(isset($colorPicker)): if($colorPicker): ?>
        <script type="text/javascript" src="/static/admin/js/jquery.simple-color.js"></script>
        <script>
            $(function(){
                $('.simple_color_callback').simpleColor({
                    boxWidth:20,
                    cellWidth: 20,
                    cellHeight: 20,
                    chooserCSS:{ 'z-index': 500 },
                    displayCSS: { 'border': 0 ,
                        'width': '32px',
                        'height': '32px',
                        'margin-top': '-32px'
                    },
                    onSelect: function(hex, element) {
                        $('#tw_color').val('#'+hex);
                    }
                });
                $('.simple_color_callback').show();
                $('.simpleColorContainer').css('margin-left','105px');
                $('.simpleColorDisplay').css('border','1px solid #DFDFDF');
            });
            var setColorPicker=function(obj){
                var color=$(obj).val();
                $(obj).parents('.color-picker').find('.simpleColorDisplay').css('background',color);
            }
        </script>
    <?php endif; endif; if(isset($load_select2)): if($load_select2): ?>
    <link rel="stylesheet" type="text/css" href="/static/libs/select2/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/static/libs/select2/css/select2-bootstrap.min.css">
    <script type="text/javascript" src="/static/libs/select2/js/select2.min.js" charset="utf-8"></script>
    <?php endif; endif; if(isset($magnific_popup)): if($magnific_popup): ?>
        <link type="text/css" rel="stylesheet" href="/static/libs/magnific/magnific-popup.css"/>
        <script type="text/javascript" src="/static/libs/magnific/jquery.magnific-popup.min.js"></script>
    <?php endif; endif; ?>

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
