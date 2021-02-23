
$(function () {
    window['adaptive'].desinWidth = 1920;//设计稿宽度
    window['adaptive'].baseFont = 14;//默认字体大小
    window['adaptive'].maxWidth = 1920;//最大宽度
    window['adaptive'].init();
    
	new WOW().init();
	
    //首页 banner
    var mySwiper = new Swiper('.banner .swiper-container', {
        autoplay: 4000,//可选选项，自动滑动
        effect : 'slide',
        autoplayDisableOnInteraction: false,
        loop: true,
        pagination : '.banner .swiper-pagination',
        paginationClickable :true,
    });
	
	$('.user_msgtext>textarea').focus(function(){
		$(this).next('i').css('width',"100%");
	})
	$('.user_msgtext>textarea').blur(function(){
		$(this).next('i').css('width',"0%");
	})

});


