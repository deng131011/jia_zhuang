<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:56:"../apps/common/view/builder/Fields/left_icon_number.html";i:1556705925;}*/ ?>
<div class="input-group">
	<span class="input-group-addon"><?php echo $field['options']['icon']; ?></span>
	<input type="number" class="form-control" name="<?php echo $field['name']; ?>" value="<?php echo (isset($field['value']) && ($field['value'] !== '')?$field['value']:''); ?>" <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:''); ?>>
</div>