<nav class="wcspp-navigation <?php echo ( WC_Share_Print_PDF::$settings['wc_settings_spp_counts'] == 'no' ? 'wcspp-nocounts' : 'wcspp-counts' ) . ' wcspp-style-' . get_option( 'wc_settings_spp_style', 'line-icons' ); ?>" data-wcspp-id="<?php the_ID(); ?>">
	<?php
		$title = '<h2>' . __( 'Share Product', 'wcsppdf' ) . '</h2>';
		echo apply_filters( 'wc_shareprintpdf_title', $title );

		do_action( 'wc_shareprintpdf_before_shares' );
	?>
	<ul>
	<?php
		do_action( 'wc_shareprintpdf_icons');
	?>
	</ul>
	<?php
		do_action( 'wc_shareprintpdf_after_shares' );
	?>
</nav>