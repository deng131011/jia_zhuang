/**
 * 地区选择JS
 * */
function get_zone(shuxing,pid=0,zone_id='',level=1){
    var htmls = '';
    if(pid>0){
        var index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
    }
    $.post("/Admin/Ajax/get_zone",{pid:pid,zone_id:zone_id,level:level},function (dd) {
        layer.close(index);
        htmls = dd.data.html;
        $(shuxing).html(htmls);
    })
}

/**
 * 改变省获取市
 * */
$("#province_id").change(function () {
     var province_id = $(this).val();
     get_zone('#city_id',province_id,'',2); //市
});

/**
 * 改变市获取区县
 * */
$("#city_id").change(function () {
    var city_id = $(this).val();
    get_zone('#county_id',city_id,'',2); //市
});