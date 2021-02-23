<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:84:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/builder/listbuilder.html";i:1607065218;s:85:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/layout/iframe/search.html";i:1556705925;}*/ ?>
<section class="row-content pt-5" >
    <div class="box box-default">
      <div class="box-header with-border pb-0">
        <h3 class="box-title">高级查询</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div>
        <!-- /.box-tools -->
      </div>
       <div class="box-body" id="eacoo-toolbar">
        <div class="form-inline" role="form">
            <?php if(is_array($searchFields) || $searchFields instanceof \think\Collection || $searchFields instanceof \think\Paginator): $k = 0; $__LIST__ = $searchFields;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($k % 2 );++$k;?>
                <div class="form-group mt-10">
                    <?php echo action('common/BuilderForm/fieldType',['field'=>$field],'builder'); ?>
                </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            <div class="form-group mt-10">
                <button type="button" class="btn btn-success" id="search">查询</button>
            </div>
        </div>

       </div>
             
    </div>
</section> 