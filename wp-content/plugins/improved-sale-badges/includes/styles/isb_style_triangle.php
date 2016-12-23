<div class="isb_sale_badge <?php echo $isb_class; ?>" data-id="<?php echo $isb_price['id']; ?>">
	<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="66" height="66" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 66 66" xmlns:xlink="http://www.w3.org/1999/xlink">
		<g>
		<?php
			if ( $isb_set['position'] == 'isb_right' ) {
		?>
			<polygon class="isb_style_triangle_fill" points="2.0782,0 54.3615,66 60.2704,0 "/>
			<polygon class="<?php echo $isb_curr_set['color']; ?>" class="isb_style_triangle_fill" points="2.0782,0 66,63.9217 60.2704,0 "/>
		<?php
			}
			else {
		?>
			<polygon class="isb_style_triangle_fill" points="63.9218,0 11.6385,66 5.7296,0 "/>
			<polygon class="<?php echo $isb_curr_set['color']; ?>" points="63.9218,0 -0,63.9217 5.7296,0 "/>
		<?php
			}
		?>
		</g>
	</svg>
	<div class="isb_sale_percentage">
		<span class="isb_percentage">
			<?php echo $isb_price['percentage']; ?> 
		</span>
		<span class="isb_percentage_text">
			<?php _e('%', 'isbwoo'); ?>
		</span>
	</div>
	<div class="isb_money_saved">
		<span class="isb_saved_text">
			<?php
				if ( $isb_price['type'] == 'simple' || is_singular( 'product' ) && !isset( $isb_price['type_extend'] ) ) {
					_e('Save', 'isbwoo');
				}
				else {
					_e('Up to', 'isbwoo');
				}
			?> 
		</span>
		<span class="isb_saved">
			<?php echo strip_tags( wc_price( $isb_price['difference'] ) ); ?>
		</span>
	</div>
<?php
	if ( isset($isb_price['time']) ) {
?>
	<div class="isb_scheduled_sale isb_scheduled_<?php echo $isb_price['time_mode']; ?> <?php echo $isb_curr_set['color']; ?>">
		<span class="isb_scheduled_text">
			<?php
				if ( $isb_price['time_mode'] == 'start' ) {
					_e('Starts in', 'isbwoo');
				}
				else {
					_e('Ends in', 'isbwoo');
				}
			?> 
		</span>
		<span class="isb_scheduled_time isb_scheduled_compact">
			<?php echo $isb_price['time']; ?>
		</span>
	</div>
<?php
	}
?>
</div>