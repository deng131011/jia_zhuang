<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:84:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/builder/listbuilder.html";i:1607065218;s:85:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/layout/iframe/search.html";i:1556705925;s:47:"../apps/common/view/builder/Fields/select3.html";i:1560306881;}*/ ?>
<select name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" class="form-control" <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:''); ?>>
    <?php if(isset($field['options']['none'])): if(!(empty($field['options']['none']) || (($field['options']['none'] instanceof \think\Collection || $field['options']['none'] instanceof \think\Paginator ) && $field['options']['none']->isEmpty()))): ?><option value=''><?php echo $field['options']['none']; ?></option><?php endif; unset($field['options']['none']); else: ?><option value=''>请选择 <?php echo (isset($field['title']) && ($field['title'] !== '')?$field['title']:""); ?></option><?php endif; if(is_array($field['options']) || $field['options'] instanceof \think\Collection || $field['options'] instanceof \think\Paginator): if( count($field['options'])==0 ) : echo "" ;else: foreach($field['options'] as $option_key=>$option): if(is_array($option)): ?>
            <option value="<?php echo $option['id']; ?>" <?php if(isset($field['value'])): if($field['value'] == $option_key): ?> selected<?php endif; endif; if(is_array($option) || $option instanceof \think\Collection || $option instanceof \think\Paginator): if( count($option)==0 ) : echo "" ;else: foreach($option as $option_key2=>$option2): ?>
                    <?php echo $option_key2; ?>="<?php echo $option2; ?>"
                <?php endforeach; endif; else: echo "" ;endif; ?>>
                <?php echo $option['title']; ?>
            </option>
        <?php else: ?>
            <option value="<?php echo $option_key; ?>" <?php if(isset($field['value'])): if($field['value'] == $option_key): ?> selected<?php endif; endif; ?>><?php echo $option; ?></option>
        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
</select>