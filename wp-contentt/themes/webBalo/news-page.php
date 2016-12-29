<?php 
	/*
		Template name: Tin tức
	*/
?>
<?php get_header() ?>
	<div class="container main-content-wrapper">
		<div class="row">
			<section class="col-xs-12 col-md-8 posts-wrapper">
				<div class="row">
				<?php
		          	if ( is_front_page() ) {
		                $paged = (get_query_var('page')) ? get_query_var('page') : 1;   
		            } else {
		                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
		            }
		           	query_posts('posts_per_page=10&post_type=post&paged='.$paged);
		          ?>
				<?php while(have_posts()) : the_post(); ?>
					<div class="col-xs-12 col-md-12">
						<article id="post_<?php the_ID() ?>" <?php post_class('post-item') ?> >
							<div class="article-wrapper">
								<a href="<?php the_permalink() ?>">
									<?php if(has_post_thumbnail()) the_post_thumbnail(); ?>
								</a>
								<div class="post-meta-wrapper">
									<p class="cats"><?php the_category(' '); ?></p>
									<a href="<?php the_permalink() ?>">
										<h3 class="entry-title"><?php the_title() ?></h3>
									</a>
									<p class="published updated"><time><?php the_time("d-m-Y") ?></time></p>
								</div>
							</div>
							<div class="entry-summary">
								<?php the_excerpt() ?>
							</div>
							<a href="<?php the_permalink() ?>" class="read-more">Xem chi tiết</a>
						</article>
					</div>
				<?php endwhile; ?>
				</div>
				<nav class="row">
		            <div class="pagination">
		              <?php wp_pagenavi();wp_reset_postdata(); ?>
		            </div>
		        </nav>
			</section>
			<aside class="col-xs-12 col-md-4">
				<?php if ( is_active_sidebar( 'news-sidebar' ) ) : ?>
					<?php dynamic_sidebar( 'news-sidebar' ); ?>
				<?php endif; ?>
			</aside>
		</div>
		
	</div>
<?php get_footer() ?>