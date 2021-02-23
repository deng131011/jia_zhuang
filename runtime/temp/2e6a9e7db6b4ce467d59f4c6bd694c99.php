<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:44:"../apps/common/view/builder/Fields/text.html";i:1556705925;s:46:"../apps/common/view/builder/Fields/number.html";i:1556705925;s:45:"../apps/common/view/builder/Fields/radio.html";i:1556705925;s:47:"../apps/common/view/builder/Fields/picture.html";i:1592297888;}*/ ?>
<?php 

      if (strpos($field['name'],'[')) {
        $field['id']=str_replace(']','',str_replace('[','',$field['name']));
      } else{
        $field['id']=$field['name'];
      }
      $path_type = isset($field['options']['path_type'])? $field['options']['path_type']:'picture';
 ?>

<div class="controls" style="padding-bottom: 5px;padding-left: 5px;">
    
    <input class="attach" type="hidden" id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo (isset($field['value']) && ($field['value'] !== '')?$field['value']:''); ?>"/>
    <div>
        <span class="btn btn-info ml-10 mt-10 btn-sm" data-url="<?php echo url('admin/Upload/attachmentLayer',['input_id_name'=>$field['id'],'path_type'=>$path_type,'gettype'=>'single']); ?>" onclick="openAttachmentLayer(this);"><i class="fa fa-file-image-o"></i> 选择图片</span>
    </div>
    <div class="upload-img-box tc popup-gallery fl img-thumbnail pr ">
        <div class="each">

         
        <?php if(empty($field['value']) || (($field['value'] instanceof \think\Collection || $field['value'] instanceof \think\Paginator ) && $field['value']->isEmpty())): ?>
            <img src="<?php  echo get_image(0); ?>">
        <?php else: ?>

            <i onclick="admin_image.removeImage($(this),'<?php echo $field['value']; ?>')" class="fa fa-times-circle remove-attachment"></i>
            <a href="<?php echo get_image($field['value']); ?>" target="_blank" title="点击查看大图">
                <img src="<?php echo get_image($field['value']); ?>">
            </a>
             
        <?php endif; ?>
        </div>

    </div>
    <?php if(!(empty($field['description']) || (($field['description'] instanceof \think\Collection || $field['description'] instanceof \think\Paginator ) && $field['description']->isEmpty()))): ?><div class="help-block col-md-6 pl-10 fn"><?php echo $field['description']; ?></div><?php endif; ?>
    
</div>
