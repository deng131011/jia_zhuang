<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:47:"../apps/common/view/builder/Fields/ueditor.html";i:1565144108;}*/ ?>
<?php 
    if (strpos($field['name'],'[')) {
        $field['id']=str_replace(']','',str_replace('[','',$field['name']));
    }else{
        $field['id']=$field['name'];
    }
      $field = array_merge($field,$field['options']);
      unset($field['options']);
 ?>
<?php echo widget('common/editor/ueditor',[$field]); ?>
