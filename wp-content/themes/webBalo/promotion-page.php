<?php 
	/*
		Template name: Khuyến mãi
	*/
?>
<?php get_header() ?>
 <div class="container-fluid">
     <div class="row">
         <div class="col-xs-12">
             <?php 
        		$args = array(
        	        'taxonomy'     => 'product_cat',
        	        'orderby'      => 'id',
        	        'hide_empty'   => 0,
        	        'parent' => 14
        	  	);
        	  	$all_categories = get_categories( $args );	
        	?>
        	<?php foreach($all_categories as $category) : ?>
        		<?php 
        			$image = get_field('img_banner', $category->taxonomy.'_'.$category->term_id);
        			if($image) :
        		?>
        		<div class="promo-banner">
        			<a href="<?php echo get_term_link($category->term_id) ?>"><img src="<?php echo $image['url'] ?>" alt="<?php echo $category->name ?>"></a>
        		</div>
        		<?php endif; ?>
        	<?php endforeach; ?>
         </div>
     </div>
 </div>
 <div class="container-fluid">
     <div class="row">
         <div class="col-xs-12">
             <div class="sub-form">
                 <h3>NHẬN TIN KHUYỄN MÃI, MẪU MỚI, HÀNG SALE OFF</h3>
                 <p class="description">Còn chờ gì nữa, hãy cùng truy cập ngay vào balohanghieu.com để cập nhật ngay những thông
tin khuyến mãi mới nhất trên website.</p>
                 <?php echo do_shortcode('[mc4wp_form id="119"]'); ?>
             </div>
         </div>
     </div>
 </div>
<?php get_footer() ?>