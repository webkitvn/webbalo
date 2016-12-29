<?php get_header() ?>
	<div class="container-fluid">
		<div id="home-slide">
			<?php echo do_shortcode('[rev_slider alias="home-slider"]') ?>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row promos">
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="promo-item promo-item-1" style="background-image: url('<?php echo get_template_directory_uri() ?>/img/1.jpg')">
					<!-- <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, cumque?</span> -->
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="promo-item promo-item-2" style="background-image: url('<?php echo get_template_directory_uri() ?>/img/2.jpg')">
					<!-- <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, cumque?</span> -->
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="promo-item promo-item-3" style="background-image: url('<?php echo get_template_directory_uri() ?>/img/3.jpg')">
					<!-- <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, cumque?</span> -->
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-3">
				<div class="promo-item promo-item-4" style="background-image: url('<?php echo get_template_directory_uri() ?>/img/4.jpg')">
					<!-- <span>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, cumque?</span> -->
				</div>
			</div>
		</div>
	</div>
<?php get_footer() ?>