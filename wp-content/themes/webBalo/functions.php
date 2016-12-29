<?php

	function my_theme_stylesheets() {
		wp_enqueue_style( 'font-cafeta', get_template_directory_uri() . '/css/UTMCafeta.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'font-sanproBlack', get_template_directory_uri() . '/css/SourceSansProBlack_0.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'font-sanproRegular', get_template_directory_uri() . '/css/SourceSansProRegular_0.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'main', get_template_directory_uri() . '/css/main.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'device', get_template_directory_uri() . '/css/device.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'product', get_template_directory_uri() . '/css/product.css', array(), '1.0', 'all' );
				// if(is_single()){
		// 	wp_enqueue_style( 'single', get_template_directory_uri() . '/css/single.css', array(), '1.0', 'all' );
		// }
		if(is_home() or is_product() or is_page(23) or is_order_received_page()){
			wp_enqueue_script('slick-js', get_template_directory_uri().'/js/slick.min.js', array(), '1.0', false);
			wp_enqueue_style( 'slick-css', get_template_directory_uri() . '/css/slick.css', array(), '1.0', 'all' );
		}
		wp_enqueue_script('fontawesome', 'https://use.fontawesome.com/4c8792004d.js', '1.0', false);
		wp_enqueue_script('bootstrap-js', get_template_directory_uri().'/js/bootstrap.min.js', array(), '1.0', true);
		wp_enqueue_script('scrollSpeed', get_template_directory_uri().'/js/jQuery.scrollSpeed.js', array(), '1.0', true);
		wp_enqueue_script('main', get_template_directory_uri().'/js/main.js', array(), '1.0', true);
		
	}

	add_action( 'wp_enqueue_scripts', 'my_theme_stylesheets' );

	if ( function_exists( 'add_theme_support' ) ) { 
		add_theme_support( 'custom-logo' );
		add_theme_support( 'site-logo' );
		add_theme_support( 'post-thumbnails', array('page', 'post','product'));
		set_post_thumbnail_size(400, 400, true ); // default Post Thumbnail dimensions (cropped)

	}
	if ( function_exists( 'add_image_size' ) ) { 
		add_image_size('cate_banner', 1200, 350, true);
		add_image_size('block_size', 668, 312, true);
	}

	//excerpt length
	function custom_excerpt_length( $length ) {
		return 30;
	}
	add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

	function new_excerpt_more($more) {
		return '...';
	}
	add_filter('excerpt_more', 'new_excerpt_more');


	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

	//woocommerce support
	remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	// remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	function webbalo_wrapper_start(){
		echo '<div class="main products-wrapper">';
	}

	add_action('woocommerce_before_main_content', 'webbalo_wrapper_start', 10);

	function webbalo_wrapper_end(){
		echo '</div>';
	}

	add_action('woocommerce_after_main_content', 'webbalo_wrapper_end', 10);

	function webbalo_product_detail_btn(){
		echo '<a class="webbalo-btn" href="'.get_the_permalink().'">'.__('Chi tiết +', 'webbalo').'</a>';
	}
	add_action('woocommerce_after_shop_loop_item_title', 'webbalo_product_detail_btn', 15 );
	


	// Remove default WooCommerce breadcrumbs and add Yoast ones instead
	remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);
	// add_action( 'woocommerce_before_main_content','my_yoast_breadcrumb', 20, 0);
	// if (!function_exists('my_yoast_breadcrumb') ) {
	// 	function my_yoast_breadcrumb() {
	// 		yoast_breadcrumb('<p id="breadcrumbs">','</p>');
	// 	}
	// }
	add_theme_support('woocommerce');

	$args = array(
		'name'          => 'Middle-widget',
		'id'            => "middle-widget",
		'description'   => 'webbalo Middle widget',
		'class'         => '',
		'before_widget' => '<div id="middle-widget %1$s" class="widget %2$s">',
		'after_widget'  => "</div>\n",
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => "</h2>\n",
	);

	register_sidebar( $args );

	add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);

	function change_existing_currency_symbol( $currency_symbol, $currency ) {
	     switch( $currency ) {
	          case '₫': $currency_symbol = 'Đ$'; break;
	     }
	     return $currency_symbol;
	}

	// Hook in
	add_filter( 'woocommerce_checkout_fields' , 'webbalo_custom_override_checkout_fields' );
	add_filter( 'woocommerce_default_address_fields' , 'webbalo_custom_override_default_address_fields');
	// Our hooked in function - $fields is passed via the filter!
	function webbalo_custom_override_checkout_fields( $fields ) {
	    //unset($fields['order']['order_comments']);
	    unset($fields['billing']['billing_company']);
	    unset($fields['billing']['billing_country']);
	    unset($fields['billing']['billing_postcode']);
	    return $fields;
	}
	function webbalo_custom_override_default_address_fields( $address_fields ) 
	{
	    unset( $address_fields['postcode'] );
	    unset( $address_fields['country'] );
	    unset( $address_fields['address_2'] );
	    unset( $address_fields['company'] );
	    return $address_fields;
	}
	
	function register_my_menus() {
	  register_nav_menus(
	    array(
	      'main-menu' => __("Main Menu"),
	      //'secondary-menu' => __("Secondary Menu")
	    )
	  );
	}
	add_action( 'init', 'register_my_menus' );



	////CUSTOM POST TYPE FOR CUSTOM BLOCK /////
	add_action('init', 'block_register', 1);
		function block_register() {
	 
		$labels = array(
			'name' => _x('Custom block', 'Custom block'),
			'singular_name' => _x('Custom block', 'post type singular name'),
			'add_new' => _x('New block', 'New block'),
			'add_new_item' => __('Add new block'),
			'edit_item' => __('Edit'),
			'new_item' => __('Add new block'),
			'view_item' => __('View block'),
			'search_items' => __('Search block'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);
	 
		$args = array(
			'labels' => $labels,
			'public' => false,
			'has_archive' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'menu_position'=> 5,
			'query_var' => true,
			'menu_icon' => 'dashicons-text',
			'capability_type' => 'post',
			'hierarchical' => true,
			//'supports' => array('title','editor','thumbnail')
		  ); 
	 
		register_post_type( 'block' , $args );
	}

	function logout_redirect_home(){
		wp_safe_redirect(home_url());
		exit;
	}
	add_action('wp_logout', 'logout_redirect_home');