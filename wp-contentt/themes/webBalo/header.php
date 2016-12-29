<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head() ?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_uri() ?>">
</head>
<body <?php body_class() ?>>
	<header id="header">
		<div class="container-fluid">
			<div class="col-xs-12 col-md-4">
				<div class="top-menu">
					<ul class="menu">
						<?php if(!is_user_logged_in()) : ?>
							<li><a href="#" data-toggle="modal" data-target="#loginModal">Đăng nhập</a></li>
							<li><a href="#" data-toggle="modal" data-target="#registerModal">Đăng kí</a></li>
						<?php else : 
							global $current_user;
      						get_currentuserinfo();
						?>
							<li>Xin chào, <a href="<?php echo get_page_link(25); ?>"><?php echo $current_user->user_login ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">Giỏ hàng (<?php echo WC()->cart->get_cart_contents_count(); ?>)</a></li>
					</ul>
				</div>
			</div>
			<div class="col-xs-12 col-md-4">
				<div class="logo">
					<?php 
						the_custom_logo();
					?>
				</div>
			</div>
			<div class="col-xs-12 col-md-4">
				<div class="menu-striger">
					<a class="striger-btn" id="striger-btn" href="#">
						<i class="fa fa-bars fa-2x" aria-hidden="true"></i>
					</a>
				</div>
			</div>
		</div>
	</header>
	<div id="menu">
		<?php wp_nav_menu( array('menu' => 'Main Menu', 'theme_location' => 'main-menu' )); ?>
		<div class="promotion">
			<h5>Nhận thông tin khuyến mãi</h5>
			<span>Đăng kí để nhận thông tin khuyến mãi hấp dẫn</span>
			<form action="#">
				<input type="text" required="" placeholder="Enter your email address">
			</form>
		</div>
	</div>