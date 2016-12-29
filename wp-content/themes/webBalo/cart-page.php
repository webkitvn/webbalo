<?php 
	/*
		Template name: Cart Page
	*/
?>
<?php get_header() ?>
	<?php while(have_posts()) : the_post(); ?>
		<div class="page-content">
			<?php if(is_cart()) : ?>
				<div class="container">
					<div class="cart-process">
						<div class="p-cart active"><i class="fa fa-shopping-cart" aria-hidden="true"></i>XEM GIỎ HÀNG</div>
						<div class="p-checkout"><i class="fa fa-list" aria-hidden="true"></i>KIỂM TRA ĐƠN HÀNG</div>
						<div class="p-complete"><i class="fa fa-check" aria-hidden="true"></i>HOÀN TẤT ĐƠN HÀNG</div>
					</div>
				</div>
			<?php elseif(is_checkout()) : ?>
				<?php if(!is_order_received_page()) : ?>
				<div class="container">
					<div class="cart-process">
						<div class="p-cart"><i class="fa fa-shopping-cart" aria-hidden="true"></i>XEM GIỎ HÀNG</div>
						<div class="p-checkout active"><i class="fa fa-list" aria-hidden="true"></i>KIỂM TRA ĐƠN HÀNG</div>
						<div class="p-complete"><i class="fa fa-check" aria-hidden="true"></i>HOÀN TẤT ĐƠN HÀNG</div>
					</div>
				</div>
				<?php else : ?>
				<div class="container">
					<div class="cart-process">
						<div class="p-cart"><i class="fa fa-shopping-cart" aria-hidden="true"></i>XEM GIỎ HÀNG</div>
						<div class="p-checkout"><i class="fa fa-list" aria-hidden="true"></i>KIỂM TRA ĐƠN HÀNG</div>
						<div class="p-complete active"><i class="fa fa-check" aria-hidden="true"></i>HOÀN TẤT ĐƠN HÀNG</div>
					</div>
				</div>
				<?php endif; ?>
			<?php endif; ?>
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<div>
							<?php the_content() ?>
						</div>
					</div>
				</div>
			</div>
			<?php include_once('new-products.php') ?>
		</div>
	<?php endwhile; ?>
<?php get_footer() ?>