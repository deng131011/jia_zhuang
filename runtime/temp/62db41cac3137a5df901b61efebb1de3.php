<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:84:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/builder/formbuilder.html";i:1562295570;s:46:"../apps/common/view/builder/Fields/hidden.html";i:1556705925;s:44:"../apps/common/view/builder/Fields/text.html";i:1556705925;s:48:"../apps/common/view/builder/Fields/password.html";i:1556705925;}*/ ?>

<div class="input-group">
	<span class="input-group-addon"><i class="fa fa-expeditedssl"></i></span>
    <input type="password" class="form-control" name="<?php echo $field['name']; ?>" value="<?php if(isset($field['value'])): ?><?php echo $field['value']; endif; ?>" <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:''); ?>>
</div>
