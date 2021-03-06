<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:64:"D:\www\jia_zhuang\public_html/../apps/home/view/index/index.html";i:1611799830;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
		<title>首页</title>
		<link rel="stylesheet" type="text/css" href="/static/home/pc/css/DefaultStyle.css"/>
		<link rel="stylesheet" type="text/css" href="/static/home/pc/css/animate.min.css"/>
		<link rel="stylesheet" type="text/css" href="/static/home/pc/css/swiper.min.css"/>
		<link rel="stylesheet" type="text/css" href="/static/home/pc/css/index.css"/>
		<script src="/static/home/pc/js/jquery-1.9.1.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/home/pc/js/swiper.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/home/pc/js/wow.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/home/pc/js/mobile.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/home/pc/js/index.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body>
		<div class="wrap">
			<!-- header -->
			<div class="header">
				<!--<img src="/static/home/pc/image/logo.png" >-->
				<div class="text" style="font-size: 30px; box-shadow: 0 0 10px 5px rgba(0,0,0,0.05);border-radius: 10px;padding: 10px 30px;">玩转梅河口</div>
			</div>
			<!-- header end-->
			
			<!-- banner -->
			<div class="banner">
			    <div class="swiper-container">
			        <div class="swiper-wrapper">
			            <div class="swiper-slide"><img src="/static/home/pc/image/banner.jpg" ></div>
			        </div>
			        <div class="swiper-pagination"></div>
			    </div>
			</div>
			<!-- banner end -->
			
			<!-- 关于宫相商城 -->
			<div class="section_about">
				<h2>关于玩转梅河口</h2>
				<i></i>
				<div style="width: 60%; margin: 0 auto;">
					<p class="wow fadeIn" data-wow-delay='0.1s' data-wow-offset="30">玩转梅河口是一款集当地美食商家、景区及娱乐为一体的软件。</p>
					<p class="wow fadeIn" data-wow-delay='0.2s' data-wow-offset="30">设置了美食商家展示、景区介绍、酒店查询等功能，用户可在软件上进行查看预订和购买。</p>
				</div>

                <img src="/static/home/pc/image/content_img.png"  class="wow bounceIn">
			</div>
			<!-- 关于宫相商城 end-->
			
			
			<!-- 我们的服务 -->
			<div class="section_service">
				<h2>我们的服务</h2>
				<i></i>
				<div class="section_service_menu">
					<img src="/static/home/pc/image/book_img.png" class="wow bounceInLeft">
					<div class="serive_info">
						<p class="wow fadeInRight" data-wow-delay='0.1s'>1）梅河口专用，商家、景区、娱乐更全面，更贴近当地用户。</p>
						<p class="wow fadeInRight" data-wow-delay='0.3s'>2）让外地用户也能快速全面了解梅河口美食、美景、娱乐，享受深度的游玩。</p>
					</div>
				</div>
			</div>
			<!-- 我们的服务 end -->
			
			<!-- 给我们留言 -->
			<div class="section_message">
				<h2>给我们留言</h2>
				<i></i>
				<div class="section_message_menu">
					<div class="message_tag">
						<h1>玩转梅河口</h1>
						<h5>WANZHUAN MEIHEKOU</h5>
						<div class="qrcode_address_info">
							<div class="qrcode">
								<img src="/static/home/pc/image/QR_code.png" >
								<p>扫描下载官方APP</p>
							</div>
							<div class="address">
								<!--<p>电话：15357591000</p>-->
								<p>公司：吉林省杨雨清影视文化传媒有限公司</p>
								<p>邮编：135000</p>
								<p>邮箱：wanzhuan2021@126.com</p>
							</div>
						</div>
					</div>
					<div class="message_send">
					    <form id="form_5">
						<div class="user_msginfo">
							<input type="text" name="names" placeholder="您的称呼" class="user_nickname"/>
							<input type="text" name="mobile" placeholder="您的电话" class="user_phone"/>
							<input type="text" name="email" placeholder="您的邮箱" class="user_email"/>		
						</div>
						<div class="user_msgtext">
							<textarea name="content" placeholder="内容" class="user_text"></textarea>
							<i></i>
						</div>
						<button type="button" class="send_textbtn">发送内容</button>
						</form>
					</div>
				</div>
			</div>
			<!-- 给我们留言 end -->
			
			<!-- footer -->
			<div class="footer">
				<p class="Copyright">Copyright © 吉林省杨雨清影视文化传媒有限公司      <a href="http://www.beian.miit.gov.cn" target="_blank" style="color:#697589;"></a></p>
				<div class="Customer_service">
					<!--<p>wanzhuan2021@126.com</p>-->
					<img src="/static/home/pc/image/copyright.png" >
				</div>
			</div>
			<!-- footer end-->
			
		</div>
		
		<script type="text/javascript">
			// var user_nickname = $('.user_nickname').val();
			// var user_phone = $('.user_phone').val();
			// var user_email = $('.user_email').val();
			// var user_text = $('.user_text').val();
			$('.send_textbtn').click(function(){
				if($('.user_nickname').val() == ''){
					alert("用户名未输入")
				}else if($('.user_phone').val() == ''){
					alert("号码未输入")
				}else if(!(/^1[3456789]\d{9}$/.test($('.user_phone').val()))){
                    alert("手机号码有误，请重填");  
				}else if($('.user_email').val() == ''){
					alert("邮箱未输入")
				}else if(!(/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/.test($('.user_email').val()))){
					alert("邮箱有误，请重填");
				}else if($('.user_text').val() == ''){
					alert("内容未输入")
				}else{
					$.post("<?php echo url('Index/fankui'); ?>",$("#form_5").serialize(),function (dd) {
						if(dd.code==200){
							alert(dd.msg);
							setTimeout(function () {
							   // parent.layer.close(index); //执行关闭
								$('#form_5')[0].reset() ;
							},1000);
						}else{
							alert(dd.msg);
						}
					})
				}
			})
		</script>
	</body>
</html>
