<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

	<?php 
		$category = get_queried_object();
		$args = array(
	        'taxonomy'     => 'product_cat',
	        'orderby'      => 'name',
	        'hide_empty'   => 1,
	        'parent' => $category->term_id
	  	);
	  	$all_categories = get_categories( $args );	
	?>
	<?php if(is_product_category()) : ?>
		<?php 
			$image = get_field('img_banner', $category->taxonomy.'_'.$category->term_id);
			if($image) :
		?>
		<div class="cate-banner">
			<img src="<?php echo $image['sizes']['cate_banner'] ?>" alt="<?php echo $category->name ?>">
		</div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="container main-content-wrapper">
		<div class="row">
			<div class="col-xs-12 col-md-12">
		<?php
			/**
			 * woocommerce_before_main_content hook.
			 *
			 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked woocommerce_breadcrumb - 20
			 */
			do_action( 'woocommerce_before_main_content' );
		?>
		
		
			<?php
				/**
				 * woocommerce_archive_description hook.
				 *
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				//do_action( 'woocommerce_archive_description' );
			?>
		
		
			<div class="container-fluid product-lists">
				<div class="row">
					<div class="col-xs-12 col-md-2">
						<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		
							<?php if(is_product_category()) : ?><h1 class="page-title"><?php woocommerce_page_title(); ?></h1><?php endif; ?>
				
						<?php endif; ?>
					</div>
					<div class="col-xs-12 col-md-10">
						<div class="result-ordering">
							<?php dynamic_sidebar( 'middle-widget' ); ?>
								<?php
									/**
									 * woocommerce_before_shop_loop hook.
									 *
									 * @hooked woocommerce_result_count - 20
									 * @hooked woocommerce_catalog_ordering - 30
									 */
									do_action( 'woocommerce_before_shop_loop' );
								?>
									
							<!--END result-ordering-->
		
						</div>
					</div>
				</div>
			<?php if ( have_posts() ) : ?>

				<div class="row">

				<?php woocommerce_product_loop_start(); ?>
		
					<?php woocommerce_product_subcategories(); ?>
		
					<?php while ( have_posts() ) : the_post(); ?>
		
						<?php wc_get_template_part( 'content', 'product' ); ?>
		
					<?php endwhile; // end of the loop. ?>
		
				<?php woocommerce_product_loop_end(); ?>

				</div><!--END products row-->
				<div class="row">
				<?php
					/**
					 * woocommerce_after_shop_loop hook.
					 *
					 * @hooked woocommerce_pagination - 10
					 */
					do_action( 'woocommerce_after_shop_loop' );
				?>
				</div>
		
			<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
		
				<div class="row"><?php wc_get_template( 'loop/no-products-found.php' ); ?></div>
		
			<?php endif; ?>
			</div><!--END product-lists-->
		
		<?php
			/**
			 * woocommerce_after_main_content hook.
			 *
			 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
			 */
			do_action( 'woocommerce_after_main_content' );
		?>
			</div>
		</div><!--end row-->
	</div><!--END main-content-wrapper-->

<?php get_footer( 'shop' ); ?>
