<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"G:\phpstudy\www\jia_zhuang\public_html/../apps/common\view\widget\ueditor.html";i:1565768359;}*/ ?>


<?php  if(\think\Hook::get('adminEditor') && MODULE_NAME == 'admin'){ ?>
    <label class="textarea">
        <textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>"><?php echo (isset($value) && ($value !== '')?$value:''); ?></textarea>
        <?php echo hook('adminEditor', array('id'=>$id,'value'=>$value)); ?>
    </label>

<?php }elseif(\think\Hook::get('editor')){ ?>

    <label class="textarea">
        <textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>"><?php echo (isset($value) && ($value !== '')?$value:''); ?></textarea>
        <?php echo hook('editor', array('id'=>$id,'value'=>$value)); ?>
    </label>

<?php }else{ if($is_load_script): ?>
        <script type="text/javascript" charset="utf-8" src="/static/libs/ueditor/ueditor.config.js"></script>
        <script type="text/javascript" charset="utf-8" src="/static/libs/ueditor/ueditor.all.min.js"></script>
    <?php endif; ?>

    <script type="text/plain" name="<?php echo $name; ?>" id="<?php echo $id; ?>" style="width:<?php echo (isset($width) && ($width !== '')?$width:'100%'); ?>;height:<?php echo (isset($height) && ($height !== '')?$height:'300px'); ?>;<?php echo (isset($style) && ($style !== '')?$style:''); ?>"><?php echo (isset($value) && ($value !== '')?$value:''); ?></script>


    <script>
        var  ue_<?php echo $id; ?>;
        $(function () {

            var menus = [[
                'fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'directionalityltr', 'directionalityrtl', 'indent', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                'link', 'unlink','|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'simpleupload', 'emotion', 'attachment', 'map', 'insertcode',  '|',
                'horizontal',  'spechars',  '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                 'preview',
            ]];

            //var config = {<?php echo $menus; ?>,'topOffset':$('#nav_bar').height()+$('#sub_nav').height()+5};
            var config = {'toolbars':menus,'topOffset':$('#nav_bar').height()+$('#sub_nav').height()+5};
           // console.log(config);
            ue_<?php echo $id; ?> = UE.getEditor('<?php echo $id; ?>', config);
        })

    </script>
<?php } ?>