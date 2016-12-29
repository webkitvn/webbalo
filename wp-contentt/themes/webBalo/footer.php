	<footer id="lienhe">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="big-title">
						Liên hệ đặt hàng
					</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-5">
					<div class="logo">
						<?php 
							the_custom_logo();
						?>
					</div>
					<div class="footer-col-content">
						<span class="icon">
							<i class="fa fa-home" aria-hidden="true"></i>
						</span>
						<div class="footer-address">
							<p>17 Đinh Bộ Lĩnh, P24, Q. Bình Thạnh hoặc Hẻm 72 Bạch Đằng </br> ( kế bên cổng sau bệnh viện Vì Dân)</p>
						</div>
					</div>
					<div class="footer-col-content">
						<span class="icon">
							<i class="fa fa-phone" aria-hidden="true"></i>
						</span>
						<div class="footer-phone">
							<p>Sỉ: 0909 33 78 33</p>
							<p>Lẻ: 093 83 73 915</p>
						</div>
					</div>
					<div class="footer-col-content">
						<span class="icon">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
						<div class="footer-email">
							<p><a href="mailto:giaoduong282@gmail.com">giaoduong282@gmail.com</a></p>
							<p><a href="mailto:hoanganhthongthin@gmail.com">hoanganhthongthin@gmail.com</a></p>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-3">
					<h3>Hỗ trợ khách hàng</h3>
					<ul>
						<li><a href="#">Hướng dẫn mua hàng</a></li>
						<li><a href="#">Chính sách giao hàng</a></li>
						<li><a href="#">Chính sách bảo hành</a></li>
						<li><a href="#">Hình thức thanh toán</a></li>
						<li><a href="#">Chính sách bảo hành</a></li>
						<li><a href="#">Đổi - Trả sản phẩm</a></li>
					</ul>
				</div>
				<div class="col-xs-12 col-md-4">
					<h3>Nhận thông tin khuyến mãi</h3>
					<p>Đăng kí để nhận thông tin khuyến mãi</p>
					<?php echo do_shortcode('[mc4wp_form id="119"]'); ?>
					<div class="social">
						<ul>
 							<li><a class="zalo" href="#" title="zalo">Zalo</a></li>
							<li><a class="viber" href="#"title="viber">Viber</a></li>
							<li><a class="fb" href="#" title="facebook">Facebook</a></li>
							<li><a class="insta" href="#" title="instagram">Instagram</a></li>
							<li><a class="yt" href="#" title="youtube">Youtube</a></li>
							<li><a class="gp" href="#" title="google plus">Google Plus</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<div class="footer-copyright">
		<p class="text-center">Copyright &copy; 2016 webbalo</p>
		<a href="#" class="backtotop text-center">
			<span class="icon"><i class="fa fa-chevron-up" aria-hidden="true"></i></span>
		</a>
	</div>
	
	<?php if(!is_user_logged_in()) : ?>
		<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<?php global $wp;
				$url_part = add_query_arg(array(),$wp->request); ?>
		    	<h2><?php echo _e("SIGN INTO YOUR ACCOUNT", "webbalo") ?></h2>
		      	<?php echo do_shortcode('[ultimatemember form_id=97]' ); ?>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<?php global $wp;
				$url_part = add_query_arg(array(),$wp->request); ?>
		    	<h2><?php echo _e("SIGN INTO YOUR ACCOUNT", "webbalo") ?></h2>
		      	<?php echo do_shortcode('[ultimatemember form_id=96]'); ?>
		    </div>
		  </div>
		</div>
	<?php endif; ?>

	<?php wp_footer() ?>

	<?php if(is_home()) : ?>
		<script>
			jQuery('#brand-carou').slick({
		  	  autoplay: true,
			  infinite: true,
			  speed: 300,
			  slidesToShow: 4,
			  slidesToScroll: 4,
			  prevArrow : '<button type="button" class="slick-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
			  nextArrow : '<button type="button" class="slick-next"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
			  responsive: [
			    {
			      breakpoint: 1024,
			      settings: {
			        slidesToShow: 3,
			        slidesToScroll: 3,
			        infinite: true,
			        dots: false
			      }
			    },
			    {
			      breakpoint: 600,
			      settings: {
			        slidesToShow: 2,
			        slidesToScroll: 2
			      }
			    },
			    {
			      breakpoint: 480,
			      settings: {
			        slidesToShow: 1,
			        slidesToScroll: 1
			      }
			    }
			    // You can unslick at a given breakpoint now by adding:
			    // settings: "unslick"
			    // instead of a settings object
			  ]
			});
		</script>
	<?php endif; ?>
</body>
</html>