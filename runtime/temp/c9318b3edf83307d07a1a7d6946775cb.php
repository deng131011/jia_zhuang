<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:84:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/builder/formbuilder.html";i:1562295570;s:46:"../apps/common/view/builder/Fields/hidden.html";i:1556705925;s:44:"../apps/common/view/builder/Fields/text.html";i:1556705925;s:57:"../apps/common/view/builder/Fields/multilayer_select.html";i:1556705925;s:46:"../apps/common/view/builder/Fields/select.html";i:1556705925;s:44:"../apps/common/view/builder/Fields/icon.html";i:1556705925;}*/ ?>
<?php 
  $iconpicker=1;
  $icon_id = $field['name'];
 ?>
<link type="text/css" rel="stylesheet" href="/static/libs/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css"/>
<style type="text/css">
  .iconpicker-popover.popover{width: 396px;}
</style>

<div class="input-group input" id="<?php echo $field['name']; ?>-picker" >
  
  <input type="text" class="form-control iconpicker" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" value="<?php if(isset($field['value'])): ?><?php echo $field['value']; endif; ?>" <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:''); ?>>
  <span class="input-group-btn">
      <button class="btn btn-raised btn-default" type="button" ><i class="fa fa-info-circle"></i> </button>
  </span>
</div>

<script type="text/javascript" src="/static/libs/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js?v=1.0.1"></script>
<script type="text/javascript">
  $('.iconpicker').iconpicker({
    title: '请选择一个图标',
    component: '.input-group-btn',
    placement:'right',
  });
</script>