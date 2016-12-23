<?php
/*
Plugin Name: Improved Sale Badges for WooCommerce
Plugin URI: http://www.mihajlovicnenad.com/improved-sale-badges
Description: Improved Sale Badges for WooCommerce! - mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 2.4.0
Author URI: http://www.mihajlovicnenad.com
Text Domain: isbwoo
*/

	class WC_Improved_Sale_Badges {

		public static $path;
		public static $url_path;
		public static $settings;
		public static $version;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {
			self::$version = '2.4.0';

			if ( !class_exists( 'Woocommerce' ) ) {
				return;
			}

			global $isb_set;
			$isb_set['style'] = ( isset( $_POST['isb_style'] ) ? $_POST['isb_style'] : get_option( 'wc_settings_isb_style', 'isb_style_basic' ) );
			$isb_set['color'] = ( isset( $_POST['isb_color'] ) ? $_POST['isb_color'] : get_option( 'wc_settings_isb_color', 'isb_red' ) );
			$isb_set['position'] = ( isset( $_POST['isb_position'] ) ? $_POST['isb_position'] : get_option( 'wc_settings_isb_position', 'isb_right' ) );
			$isb_set['single'] = get_option( 'wc_settings_isb_overrides', 'no' );
			$isb_set['override_templates'] = get_option( 'wc_settings_isb_template_overrides', 'yes' );
			$isb_set['override_archive'] = get_option( 'wc_settings_isb_archive_action', '' );
			$isb_set['override_single'] = get_option( 'wc_settings_isb_single_action', '' );
			$isb_set['special'] = '';
			$isb_set['special_text'] = '';
			self::$settings = $isb_set;

			self::$path = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$url_path = plugins_url( __FILE__ );

			add_action( 'init', array( &$this, 'isb_textdomain' ), 1000 );
			add_action( 'wp_enqueue_scripts', array( &$this, 'isb_scripts' ) );
			add_action( 'wp_footer', array( &$this, 'check_scripts' ) );

			if ( $isb_set['override_templates'] == 'yes' ) {
				add_filter( 'wc_get_template_part', array( &$this, 'isb_add_filter' ), 10, 3 );
				add_filter( 'woocommerce_locate_template', array(&$this, 'isb_add_loop_filter'), 10, 3 );
			}

			if ( $isb_set['override_archive'] !== '' ) {
				$curr_action = array();
				$curr_action = explode( ':', $isb_set['override_archive'] );
				$curr_action[1] = isset( $curr_action[1] ) ? intval( $curr_action[1] ) : 10;
				add_action( $curr_action[0], array( &$this, 'isb_get_loop_badge' ), $curr_action[1] );
			}

			if ( $isb_set['override_single'] !== '' ) {
				$curr_action = array();
				$curr_action = explode( ':', $isb_set['override_single'] );
				$curr_action[1] = isset( $curr_action[1] ) ? intval( $curr_action[1] ) : 10;
				add_action( $curr_action[0], array( &$this, 'isb_get_single_badge' ), $curr_action[1] );
			}

			add_action( 'isb_get_loop_badge', array( &$this, 'isb_get_loop_badge' ), 10 );
			add_action( 'isb_get_single_badge', array( &$this, 'isb_get_single_badge' ), 10 );

		}

		public static function isb_get_path() {
			return plugin_dir_path( __FILE__ );
		}

		function isb_textdomain() {
			$dir = trailingslashit( WP_LANG_DIR );
			load_plugin_textdomain( 'isbwoo', false, $dir . 'plugins' );
		}

		function isb_scripts() {
			wp_enqueue_style( 'isb-style', plugins_url( 'assets/css/style.css', __FILE__ ), self::$version );
			wp_register_script( 'isb-scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array( 'jquery' ), self::$version, true );
			wp_enqueue_script( 'isb-scripts' );
		}

		function check_scripts() {

			global $isb_set;

			if ( !isset( $isb_set['load_js'] ) ) {
				wp_dequeue_script( 'isb-scripts' );
			}
			else if ( wp_script_is( 'isb-scripts', 'enqueued' ) ) {

				$curr_args = array(
					'localization' => array(
						'd' => __( 'd', 'isbwoo' ),
						'days' => __( 'days', 'isbwoo' )
					)
				);

			}

		}

		function isb_add_filter ( $template, $slug, $name ) {

			if ( in_array( $slug, array( 'loop/sale-flash.php', 'single-product/sale-flash.php' ) ) ) {

				if ( $slug == 'loop/sale-flash.php' && self::$settings['override_archive'] !== '' ) {
					return $template;
				}

				if ( $slug == 'single-product/sale-flash.php' && self::$settings['override_single'] !== '' ) {
					return $template;
				}

				if ( $name ) {
					$path = plugin_dir_path( __FILE__ ) . WC()->template_path() . "{$slug}-{$name}.php";
				} else {
					$path = plugin_dir_path( __FILE__ ) . WC()->template_path() . "{$slug}.php";
				}

				return file_exists( $path ) ? $path : $template;

			}

			return $template;

		}

		function isb_add_loop_filter ( $template, $template_name, $template_path ) {

			if ( in_array( $template_name, array( 'loop/sale-flash.php', 'single-product/sale-flash.php' ) ) ) {

				if ( $template_name == 'loop/sale-flash.php' && self::$settings['override_archive'] !== '' ) {
					return $template;
				}

				if ( $template_name == 'single-product/sale-flash.php' && self::$settings['override_single'] !== '' ) {
					return $template;
				}

				$path = plugin_dir_path( __FILE__ ) . $template_path . $template_name;
				return file_exists( $path ) ? $path : $template;

			}

			return $template;

		}

		function isb_get_loop_badge() {

			$include = WC_Improved_Sale_Badges::isb_get_path() . ( 'woocommerce/loop/sale-flash.php');

			if ( file_exists( $include ) ) {
				ob_start();

				include( $include );
				$out = ob_get_clean();

				echo $out;
			}

		}

		function isb_get_single_badge() {

			$include = WC_Improved_Sale_Badges::isb_get_path() . ( 'woocommerce/single-product/sale-flash.php');

			if ( file_exists( $include ) ) {
				ob_start();

				include( $include );
				$out = ob_get_clean();

				echo $out;
			}

		}

	}


	add_action( 'init', array( 'WC_Improved_Sale_Badges', 'init' ), 998 );

	if ( is_admin() ) {

		include_once( plugin_dir_path( __FILE__ ) . 'includes/isb-settings.php');

		$purchase_code = get_option( 'wc_settings_isb_update_code', '' );

		if ( $purchase_code ) {
			require 'includes/update/plugin-update-checker.php';
			$pf_check = PucFactory::buildUpdateChecker(
				'http://mihajlovicnenad.com/envato/get_json.php?p=9678382&k=' . $purchase_code,
				__FILE__
			);
		}

	}

?>