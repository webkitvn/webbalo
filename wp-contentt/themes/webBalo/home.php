<?php get_header() ?>
	<section class="row-item" id="slider">
		<div class="container-fluid">
			<?php echo do_shortcode('[rev_slider alias="main-slider"]'); ?>
		</div>
	</section>
	<section id="blocks" class="row-item">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="big-title"><?php _e('Sản phẩm', 'webbalo') ?></h2>
				</div>
			</div>
			<div class="row">
				<?php 
					$arg = array(
						'post_type' => 'block',
						'posts_per_page' => 6,
						'order' => "ASC"
					);
					$query = new WP_Query($arg);
					while($query->have_posts()) : $query->the_post();
				?>
				<?php 
						$block_img = get_field('block_img');
						if($block_img) :
					?>
					<div class="col-xs-12 col-md-6">
						<div class="block-item">
							<img src="<?php echo $block_img['sizes']['block_size'] ?>" alt="<?php echo $block_img['alt'] ?>">
							<?php if(get_the_title()) : ?>
								<h4><?php the_title() ?></h4>
							<?php endif; ?>
							<a class="block-btn" href="<?php echo get_field('block_url') ?>">
								Chi tiết +
							</a>
						</div>
					</div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<section class="row-item" id="cacou">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="big-title"><?php _e("Sản phẩm mới", "webbalo") ?></h2>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div id="brand-carou" class="products">
						<?php
							$loop = new WP_Query(array(
								'post_type' => 'product',
								'posts_per_page' => 12
							))
						?>
						<?php if ( $loop->have_posts() ) {
								while ( $loop->have_posts() ) : $loop->the_post();  ?>
						<div class="item">
							<?php 
									wc_get_template_part( 'contentslide', 'product' );
								
							?>
						</div>
						<?php endwhile; } ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php get_footer() ?>