<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:46:"../apps/common/view/builder/Fields/number.html";i:1556705925;}*/ ?>

<input type="number" class="form-control" name="<?php echo $field['name']; ?>" value="<?php if(isset($field['value'])): ?><?php echo $field['value']; endif; ?>" <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:''); ?>> 