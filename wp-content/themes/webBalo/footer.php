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
				<div class="col-xs-12 col-md-4">
					<div class="footer-col-content text-center">
						<span class="icon">
							<i class="fa fa-home fa-3x" aria-hidden="true"></i>
						</span>
						<div class="footer-address">
							17 Đinh Bộ Lĩnh, P24, Q. Bình Thạnh hoặc Hẻm 72 Bạch Đằng ( kế bên cổng sau bệnh viện Vì Dân)
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="footer-col-content text-center">
						<span class="icon">
							<i class="fa fa-phone fa-3x" aria-hidden="true"></i>
						</span>
						<div class="footer-phone">Sỉ: 0909 33 78 33 | Lẻ: 093 83 73 915</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div class="footer-col-content text-center">
						<span class="icon">
							<i class="fa fa-envelope fa-3x" aria-hidden="true"></i>
						</span>
						<div class="footer-email">
							<p><a href="mailto:giaoduong282@gmail.com">giaoduong282@gmail.com</a></p>
							<p><a href="mailto:hoanganhthongthin@gmail.com">hoanganhthongthin@gmail.com</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<a href="#" class="backtotop text-center">
		<span class="icon"><i class="fa fa-chevron-up" aria-hidden="true"></i></span><span class="backtotop-text">Back to top</span>
	</a>
	<div class="footer-logo text-center">
		<a href="<?php echo bloginfo("home"); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/outletbalo_logo_black.png" alt=""></a>
	</div>
	<div class="footer-copyright">
		<p class="text-center">Copyright &copy; 2016 webbalo</p>
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