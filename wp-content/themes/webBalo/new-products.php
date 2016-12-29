	<section class="row-item" id="cacou">
		<div class="container">
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
								'posts_per_page' => 12,
								'meta_query'     => array(
						        'relation' => 'OR',
						        array( // Simple products type
						            'key'           => '_sale_price',
						            'value'         => 0,
						            'compare'       => '=',
						            'type'          => 'numeric'
						        ),
						        array( // Variable products type
						            'key'           => '_min_variation_sale_price',
						            'value'         => 0,
						            'compare'       => '=',
						            'type'          => 'numeric'
						        )
						    )
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