<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:84:"G:\phpstudy\www\jia_zhuang\public_html/../apps//common/view/builder/formbuilder.html";i:1562295570;s:44:"../apps/common/view/builder/Fields/text.html";i:1556705925;s:48:"../apps/common/view/builder/Fields/textarea.html";i:1556705925;s:46:"../apps/common/view/builder/Fields/select.html";i:1556705925;s:45:"../apps/common/view/builder/Fields/radio.html";i:1556705925;}*/ ?>
<!--
    如果选项的值是自定义数组(必须定义key为title的元素)需要解析，如果选项的值是常规字符串直接显示
    此处主要是用来给option定义更多的属性，比如data-ia=1，那么option应为
    $option = array('title' => 标题, 'data-id' => 1);
-->
<div class="field-type-radio oh mb-10 pl-5 pr-20 fl">
<?php $num = '0'; if(is_array($field['options']) || $field['options'] instanceof \think\Collection || $field['options'] instanceof \think\Paginator): if( count($field['options'])==0 ) : echo "" ;else: foreach($field['options'] as $option_key=>$option): $num++;if(is_array($option)): ?>
        <label for="<?php echo $field['name']; ?><?php echo $option_key; ?>" class="radio-label<?php echo $num; ?>"><input type="radio" id="<?php echo $field['name']; ?><?php echo $option_key; ?>" class="radio" name="<?php echo $field['name']; ?>" value="<?php echo $option_key; ?>" <?php if($field['value'] == $option_key): ?> checked<?php endif; ?> <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:""); if(is_array($option) || $option instanceof \think\Collection || $option instanceof \think\Paginator): if( count($option)==0 ) : echo "" ;else: foreach($option as $option_key2=>$option2): ?>
                <?php echo $option_key2; ?>="<?php echo $option2; ?>"
            <?php endforeach; endif; else: echo "" ;endif; ?>>
            <span class="check"></span>
       <?php echo $option['title']; ?></label>
    <?php else: ?>
        <div class="radio radio-primary fl mr-10">
            <label for="<?php echo $field['name']; ?><?php echo $option_key; ?>" class="radio-label<?php echo $num; ?>">
              <input type="radio" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?><?php echo $option_key; ?>" value="<?php echo $option_key; ?>" <?php if(isset($field['value'])): if($field['value'] == $option_key): ?> checked<?php endif; endif; ?> <?php echo (isset($field['extra_attr']) && ($field['extra_attr'] !== '')?$field['extra_attr']:""); ?>><span class="circle"></span><span class="check"></span>
             <?php echo $option; ?>
            </label>
          </div>

    <?php endif; endforeach; endif; else: echo "" ;endif; ?>

</div>
