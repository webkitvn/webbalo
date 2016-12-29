<?php
/*
Plugin Name: Improved Variable Product Attributes for WooCommerce
Plugin URI: http://www.mihajlovicnenad.com/improved-variable-product-attributes
Description: Improved Variable Product Attributes for WooCommerce! - mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 3.0.2
Author URI: http://www.mihajlovicnenad.com
*/

	class WC_Improved_Variable_Product_Attributes {

		public static $dir;
		public static $path;
		public static $url_path;
		public static $settings;
		public static $version;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			if ( !class_exists( 'Woocommerce' ) ) {
				return;
			}

			self::$version = '3.0.2';

			self::$dir = dirname( __FILE__ );
			self::$path = plugin_dir_path( __FILE__ );
			self::$url_path = plugins_url( basename( self::$dir ) );

			self::$settings['single_action'] = '';
			self::$settings['archive_action'] = '';

			self::$settings['wc_settings_ivpa_single_enable'] = get_option( 'wc_settings_ivpa_single_enable', 'yes' );
			self::$settings['wc_settings_ivpa_single_selectbox'] = get_option( 'wc_settings_ivpa_single_selectbox', 'yes' );
			self::$settings['wc_settings_ivpa_archive_enable'] = get_option( 'wc_settings_ivpa_archive_enable', 'no' );
			self::$settings['wc_settings_ivpa_archive_quantity'] = get_option( 'wc_settings_ivpa_archive_quantity', 'no' );
			self::$settings['wc_settings_ivpa_archive_mode'] = get_option( 'wc_settings_ivpa_archive_mode', 'ivpa_selection' );
			self::$settings['wc_settings_ivpa_single_ajax'] = get_option( 'wc_settings_ivpa_single_ajax', 'no' );
			self::$settings['wc_settings_ivpa_archive_image_size'] = get_option( 'wc_settings_ivpa_archive_image_size', 'full' );
			self::$settings['wc_settings_ivpa_outofstock_mode'] = get_option( 'wc_settings_ivpa_outofstock_mode', 'default' );
			self::$settings['wc_settings_ivpa_force_scripts'] = get_option( 'wc_settings_ivpa_force_scripts', 'no' );
			self::$settings['wc_settings_ivpa_use_caching'] = get_option( 'wc_settings_ivpa_use_caching', 'no' );
			self::$settings['wc_settings_ivpa_single_image_size'] = get_option( 'wc_settings_ivpa_single_image_size', 'full' );
			self::$settings['wc_settings_ivpa_disable_unclick'] = get_option( 'wc_settings_ivpa_disable_unclick', 'no' );
			self::$settings['wc_settings_ivpa_single_image'] = get_option( 'wc_settings_ivpa_single_image', 'yes' );
			self::$settings['wc_settings_ivpa_image_attributes'] = get_option( 'wc_settings_ivpa_image_attributes', array() );
			self::$settings['wc_settings_ivpa_step_selection'] = get_option( 'wc_settings_ivpa_step_selection', array() );

			self::$settings['wc_settings_ivpa_single_selector'] = get_option( 'wc_settings_ivpa_single_selector', '' );

			self::$settings['wc_settings_ivpa_archive_selector'] = get_option( 'wc_settings_ivpa_archive_selector', '' );
			if ( self::$settings['wc_settings_ivpa_archive_selector'] == '' ) {
				self::$settings['wc_settings_ivpa_archive_selector'] = '.type-product';
			}
			self::$settings['wc_settings_ivpa_addcart_selector'] = get_option( 'wc_settings_ivpa_addcart_selector', '' );
			if ( self::$settings['wc_settings_ivpa_addcart_selector'] == '' ) {
				self::$settings['wc_settings_ivpa_addcart_selector'] = '.add_to_cart_button.product_type_variable';
			}
			self::$settings['wc_settings_ivpa_price_selector'] = get_option( 'wc_settings_ivpa_price_selector', '' );
			if ( self::$settings['wc_settings_ivpa_price_selector'] == '' ) {
				self::$settings['wc_settings_ivpa_price_selector'] = '.price';
			}

			self::$settings['wc_settings_ivpa_single_action'] = get_option( 'wc_settings_ivpa_single_action', '' );
			if ( self::$settings['wc_settings_ivpa_single_action'] == '' ) {
				self::$settings['wc_settings_ivpa_single_action'] = 'woocommerce_before_add_to_cart_form';
			}

			if ( self::$settings['wc_settings_ivpa_single_enable'] == 'yes' ) {
				if ( strpos( self::$settings['wc_settings_ivpa_single_action'], ':' ) > 0 ) {
					$explode = explode( ':', self::$settings['wc_settings_ivpa_single_action'] );
					$curr_action = array(
						'action' => $explode[0],
						'priority' => intval( $explode[1] ) > -1 ? intval( $explode[1] ) : 10
					);
				}
				else {
					$curr_action = array(
						'action' => self::$settings['wc_settings_ivpa_single_action'],
						'priority' => 10
					);
				}

				self::$settings['single_action'] = $curr_action['action'];
				add_action( $curr_action['action'], array(&$this, 'ivpa_attributes'), $curr_action['priority'] );

			}

			self::$settings['wc_settings_ivpa_archive_action'] = get_option( 'wc_settings_ivpa_archive_action', '' );
			if ( self::$settings['wc_settings_ivpa_archive_action'] == '' ) {
				self::$settings['wc_settings_ivpa_archive_action'] = 'woocommerce_after_shop_loop_item:999';
			}

			if ( self::$settings['wc_settings_ivpa_archive_enable'] == 'yes' ) {
				if ( strpos( self::$settings['wc_settings_ivpa_archive_action'], ':' ) > 0 ) {
					$explode = explode( ':', self::$settings['wc_settings_ivpa_archive_action'] );
					$curr_action = array(
						'action' => $explode[0],
						'priority' => intval( $explode[1] ) > -1 ? intval( $explode[1] ) : 10
					);
				}
				else {
					$curr_action = array(
						'action' => self::$settings['wc_settings_ivpa_archive_action'],
						'priority' => 10
					);
				}

				self::$settings['archive_action'] = $curr_action['action'];
				add_action( $curr_action['action'], array(&$this, 'ivpa_attributes'), $curr_action['priority'] );

			}

			add_action( 'init', array(&$this, 'ivpa_textdomain'), 1000 );
			add_action( 'wp_enqueue_scripts', array(&$this, 'ivpa_scripts') );
			add_action( 'wp_footer', array( &$this, 'footer_actions' ) );

			if ( self::$settings['wc_settings_ivpa_archive_enable'] == 'yes' || self::$settings['wc_settings_ivpa_single_ajax'] == 'yes' ) {
				add_action( 'woocommerce_add_to_cart' , array(&$this, 'ivpa_repair_cart') );
				add_action( 'wp_ajax_nopriv_ivpa_add_to_cart_callback', array(&$this, 'ivpa_add_to_cart_callback') );
				add_action( 'wp_ajax_ivpa_add_to_cart_callback', array(&$this, 'ivpa_add_to_cart_callback') );
			}

			if ( self::$settings['wc_settings_ivpa_use_caching'] == 'yes' ) {
				add_action( 'save_post', array( &$this, 'delete_caches' ), 10, 3 );
			}

		}

		public static function ivpa_get_path() {
			return self::$path;
		}

		function delete_caches( $post_ID, $post, $update ) {

			$slug = 'product';

			if ( $slug != $post->post_type ) {
				return;
			}

			global $wpdb;

			$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID = {$post_ID} AND meta.meta_key LIKE '_ivpa_cached_%';" );

		}

		function ivpa_textdomain() {

			$domain = 'ivpawoo';
			$dir = untrailingslashit( WP_LANG_DIR );
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain,$dir . '/plugins/' . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			}
			else {
				load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
			}

		}

		function ivpa_scripts() {

			$curr_args = array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'settings' => array(
					'archive_selector' => self::$settings['wc_settings_ivpa_archive_selector'],
					'price_selector' => self::$settings['wc_settings_ivpa_price_selector']
				),
				'localization' => array(
					'select' => __( 'Select', 'ivpawoo' )
				)
			);

			if ( self::$settings['wc_settings_ivpa_single_ajax'] == 'yes' ) {
				wp_register_script( 'ivpa-variable-add-to-cart-ajax', self::$url_path . '/assets/js/variable_add_to_cart_ajax.js', array('jquery'), self::$version, true );
				wp_enqueue_script( 'ivpa-variable-add-to-cart-ajax' );
				wp_localize_script( 'ivpa-variable-add-to-cart-ajax', 'ivpa', $curr_args );
			}

			wp_enqueue_style( 'ivpa-style', self::$url_path . '/assets/css/style.css', false, self::$version );

			wp_enqueue_script( 'hoverIntent' );
			wp_register_script( 'ivpa-scripts', self::$url_path .'/assets/js/scripts.js', array( 'jquery' ), self::$version, true );
			wp_enqueue_script( 'ivpa-scripts' );

		}

		function footer_actions() {

			global $ivpa_global;

			if ( !isset( $ivpa_global['init'] ) && self::$settings['wc_settings_ivpa_force_scripts'] == 'no' ) {

				wp_dequeue_script( 'ivpa-variable-add-to-cart-ajax' );
				wp_dequeue_script( 'ivpa-scripts' );

			}
			else {

				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'outofstock' => self::$settings['wc_settings_ivpa_outofstock_mode'],
					'disableunclick' => self::$settings['wc_settings_ivpa_disable_unclick'],
					'imageswitch' => self::$settings['wc_settings_ivpa_single_image'],
					'imageattributes' => self::$settings['wc_settings_ivpa_image_attributes'],
					'stepped' => self::$settings['wc_settings_ivpa_step_selection'],
					'settings' => array(
						'single_selector' => self::$settings['wc_settings_ivpa_single_selector'],
						'archive_selector' => self::$settings['wc_settings_ivpa_archive_selector'],
						'addcart_selector' => self::$settings['wc_settings_ivpa_addcart_selector'],
						'price_selector' => self::$settings['wc_settings_ivpa_price_selector']
					),
					'localization' => array(
						'select' => __( 'Select', 'ivpawoo' ),
						'simple' => ( isset ( $ivpa_global['simple'] ) ? $ivpa_global['simple'] : __( 'Add to cart', 'ivpawoo' ) ),
						'variable' => ( isset ( $ivpa_global['variable'] ) ? $ivpa_global['variable'] : __( 'Select options', 'ivpawoo' ) )
					)
				);

				wp_localize_script( 'ivpa-scripts', 'ivpa', $curr_args );

				if ( is_product() ) {
					self::ivpa_single_styles();
				}
				else {
					if ( self::$settings['wc_settings_ivpa_force_scripts'] == 'yes' ) {
						self::ivpa_single_styles();
					}
					self::ivpa_archive_styles();
				}

			}

		}

		public static function ivpa_get_attributes() {
			$attributes = get_object_taxonomies( 'product' );
			$ready_attributes = array();

			if ( !empty( $attributes ) ) {

				foreach( $attributes as $k ) {

					if ( substr($k, 0, 3) == 'pa_' ) {
						$ready_attributes[] = $k;
					}

				}

			}

			return $ready_attributes;
		}

		function ivpa_single_styles() {

			if ( self::$settings['wc_settings_ivpa_single_selectbox'] == 'no' || self::$settings['wc_settings_ivpa_outofstock_mode'] !== 'default' ) {
?>
			<style type="text/css">
<?php
				if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'clickable' ) {
?>
					.ivpa_term.ivpa_outofstock {
						cursor:pointer!important;
					}
<?php
				}
				else if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'hidden' ) {
?>
					.ivpa_term.ivpa_outofstock {
						display:none!important;
					}
<?php
				}
				if ( self::$settings['wc_settings_ivpa_single_enable'] == 'yes' && self::$settings['wc_settings_ivpa_single_selectbox'] == 'no' ) {
?>
					body .variations_form .variations {
						display:block!important;
					}
<?php
				}
?>
			</style>
<?php
			}
		}

		function ivpa_archive_styles() {
			global $ivpa_global;

			if ( !isset( $ivpa_global['init'] ) ) {
				return;
			}

			$curr_language = self::ivpa_wpml_language();

			if ( $curr_language === false ) {
				$curr_customizations = get_option( 'wc_ivpa_attribute_customization', '' );
			}
			else {
				$curr_customizations = get_option( 'wc_ivpa_attribute_customization_' . $curr_language, '' );
			}

			if ( $curr_customizations == '' ) {
				$curr_customizations = array( 'ivpa_attr' => array() );
			}

			$curr_attributes = self::ivpa_get_attributes();

?>
	<style type="text/css">
<?php
			if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'clickable' ) {
?>
				.ivpa_term.ivpa_outofstock {
					cursor:pointer!important;
				}
<?php
			}
			else if ( self::$settings['wc_settings_ivpa_outofstock_mode'] == 'hidden' ) {
?>
				.ivpa_term.ivpa_outofstock {
					display:none!important;
				}
<?php
			}
			$ready_customization = array();
			$keep_customization = array();

			foreach( $curr_attributes as $k ) {
				if ( isset( $curr_customizations['ivpa_attr'] ) && is_array( $curr_customizations['ivpa_attr'] ) && in_array( $k, $curr_customizations['ivpa_attr'] ) ) {
					$ready_customization[array_search($k, $curr_customizations['ivpa_attr'])] = $k;
				}
				else {
					$keep_customization[$k] = $k;
				}
			}

			ksort( $ready_customization );

			$ready_customization = $ready_customization + $keep_customization;

			foreach ( $ready_customization as $k => $v ) {

				$v = sanitize_title( $v );

				$curr['style'] = ( isset($curr_customizations['ivpa_style'][$k]) ? $curr_customizations['ivpa_style'][$k] : 'ivpa_text' );
				$curr['title'] = ( isset($curr_customizations['ivpa_title'][$k]) ? $curr_customizations['ivpa_title'][$k] : '' );
				$curr['desc'] = ( isset($curr_customizations['ivpa_desc'][$k]) ? $curr_customizations['ivpa_desc'][$k] : '' );
				$curr['custom'] = ( isset($curr_customizations['ivpa_custom'][$k]) ? $curr_customizations['ivpa_custom'][$k] : array( 'style' => 'ivpa_background', 'normal' => '#bbbbbb', 'active' => '#333333', 'disabled' => '#e45050', 'outofstock' => '#e45050' ) );

				if ( $curr['style'] == 'ivpa_text' ) {
					switch ( $curr['custom']['style'] ) {

						case 'ivpa_border' :
					?>
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active {
								border-color:<?php echo $curr['custom']['normal']; ?>;
							}
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked,
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
								border-color:<?php echo $curr['custom']['active']; ?>;
							}
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_disabled {
								border-color:<?php echo $curr['custom']['disabled']; ?>;
							}
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_outofstock {
								border-color:<?php echo $curr['custom']['outofstock']; ?>;
							}
					<?php
						break;


						case 'ivpa_background' :
					?>
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active {
								background-color:<?php echo $curr['custom']['normal']; ?>;
							}
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked,
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
								background-color:<?php echo $curr['custom']['active']; ?>;
							}
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_disabled {
								background-color:<?php echo $curr['custom']['disabled']; ?>;
							}
							.ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_outofstock {
								background-color:<?php echo $curr['custom']['outofstock']; ?>;
							}
					<?php

						break;

					}

				}

			}

?>
	</style>
<?php

		}

		function utf8_urldecode($str) {
			$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
			return html_entity_decode($str,null,'UTF-8');
		}

		function ivpa_attributes() {
			global $product, $ivpa_global;

			if ( $product->is_type( 'variable' ) ) {

				if ( !isset( $ivpa_global['init'] ) ){
					$ivpa_global['init'] = true;

					$ivpa_product = new WC_Product_Simple( $product->id );
					$ivpa_global['simple'] = esc_html( $ivpa_product->add_to_cart_text() );

					$ivpa_product = new WC_Product_Variable( $product->id );
					$ivpa_global['variable'] = esc_html( $ivpa_product->add_to_cart_text() );

				}

				$curr_action_filter = current_filter();

				if ( $curr_action_filter == self::$settings['archive_action'] ) {
					$curr_is_loop = 'loop';
				}
				else if ( $curr_action_filter == self::$settings['single_action'] ) {
					$curr_is_loop = 'single';
				}
				else {
					$curr_is_loop = 'loop';
				}

				$cached_html = '';
				if ( self::$settings['wc_settings_ivpa_use_caching'] == 'yes' ) {
					$cached_html = get_post_meta( get_the_ID(), '_ivpa_cached_' . $curr_is_loop . '_' . get_locale(), true );
				}

				if ( !empty( $cached_html ) ) {

					$available_variations = get_post_meta( get_the_ID(), '_ivpa_cached_data', true );

					echo str_replace( '%%%JSON_REPLACE_IVPA%%%', esc_attr( json_encode( $available_variations ) ), $cached_html );

				}
				else {

					ob_start();

					$available_attributes = $product->get_attributes();

					$available_variations = array();

					foreach ( $product->get_children() as $child_id ) {
						$variation = $product->get_child( $child_id );

						if ( empty( $variation->variation_id ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
							continue;
						}

						if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $product->id, $variation ) && ! $variation->variation_is_visible() ) {
							continue;
						}

						if ( has_post_thumbnail( $variation->get_variation_id() ) ) {
							$attachment_id   = get_post_thumbnail_id( $variation->get_variation_id() );
							$attachment      = wp_get_attachment_image_src( $attachment_id, ( $curr_is_loop == 'loop' ? self::$settings['wc_settings_ivpa_archive_image_size'] : self::$settings['wc_settings_ivpa_single_image_size'] ) );
							$variation_image = $attachment[0];
						}
						else {
							$variation_image = '';
						}

						$available_variations[] = array(
							'variation_id'          => $variation->variation_id,
							'attributes'            => $variation->get_variation_attributes(),
							'price_html'            => apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === "" || $product->get_variation_price( 'min' ) !== $product->get_variation_price( 'max' ), $product, $variation ) ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
							'is_in_stock'           => $variation->is_in_stock(),
							'ivpa_image'            => $variation_image,
						);

					}

					$curr_attributes = $product->get_variation_attributes();

					$selected_attributes = $product->get_variation_default_attributes();

					$curr_language = self::ivpa_wpml_language();

					if ( $curr_language === false ) {
						$curr_customizations = get_option( 'wc_ivpa_attribute_customization', '' );
					}
					else {
						$curr_customizations = get_option( 'wc_ivpa_attribute_customization_' . $curr_language, '' );
					}

					if ( $curr_customizations == '' ) {
						$curr_customizations = array( 'ivpa_attr' => array() );
					}

					if ( $curr_is_loop == 'single' ) {
						$curr_add_elements = 'id="ivpa-content" class="ivpa-register' . ( self::$settings['wc_settings_ivpa_step_selection'] == 'yes' ? ' ivpa-stepped' : '' ) . '"';

						$curr_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), self::$settings['wc_settings_ivpa_single_image_size'] );
						$curr_image = $curr_thumb[0];

						$curr_add_elements .= ' data-image="' . $curr_image . '"';
					}
					else {
						$curr_add_elements = 'class="ivpa-content ivpa-register' . ( self::$settings['wc_settings_ivpa_step_selection'] == 'yes' ? ' ivpa-stepped' : '' ) . '"';
						$curr_add_elements .= ' data-url="' . get_permalink() . '"';
						if ( has_post_thumbnail() ) {
							$curr_thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), self::$settings['wc_settings_ivpa_archive_image_size'] );
							$curr_image = $curr_thumb[0];

							$curr_add_elements .= ' data-image="' . $curr_image . '"';
							$curr_add_elements .= ' data-id="' . get_the_ID() . '"';
						}
					}

				?>
					<div <?php echo $curr_add_elements; ?> data-variations="<?php echo '%%%JSON_REPLACE_IVPA%%%'; ?>"<?php echo ( self::ivpa_wpml_language() !== false ? ' data-lang="' . ICL_LANGUAGE_CODE . '"' : '' ); ?>>
						<div class="ivpa-hidden-price">
						<?php
							$price_html = $product->get_price_html();
							wc_get_template( 'loop/price.php' );
						?>
						</div>
				<?php
					$ready_customization = array();
					$keep_customization = array();

					foreach ( $available_attributes as $k => $v ) {
						if ( $v['is_variation'] == '1' ) {

							if ( isset($curr_customizations['ivpa_attr']) && is_array($curr_customizations['ivpa_attr']) && in_array(self::utf8_urldecode( $k ), $curr_customizations['ivpa_attr']) ) {
								$ready_customization[array_search(self::utf8_urldecode( $k ), $curr_customizations['ivpa_attr'])] = $k;
							}
							else {
								$keep_customization[$k] = $k;
							}
						}
					}

					ksort( $ready_customization );

					$ready_customization = $ready_customization + $keep_customization;

					foreach ( $ready_customization as $k => $v ) {

						if ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' && isset( $curr_customizations['ivpa_archive_include'][$k] ) && $curr_customizations['ivpa_archive_include'][$k] == 'no' ) {
							continue;
						}

						$curr_term_sanitized = self::utf8_urldecode( $v );
						$v = sanitize_title( $v );

						if ( !isset( $curr_attributes[$curr_term_sanitized] ) || !is_array( $curr_attributes[$curr_term_sanitized] ) ) {
							continue;
						}

						$curr['style'] = ( isset($curr_customizations['ivpa_style'][$k]) ? $curr_customizations['ivpa_style'][$k] : 'ivpa_text' );
						$curr['title'] = ( isset($curr_customizations['ivpa_title'][$k]) ? $curr_customizations['ivpa_title'][$k] : '' );
						$curr['desc'] = ( isset($curr_customizations['ivpa_desc'][$k]) ? $curr_customizations['ivpa_desc'][$k] : '' );
						$curr['custom'] = ( isset($curr_customizations['ivpa_custom'][$k]) ? $curr_customizations['ivpa_custom'][$k] : array( 'style' => 'ivpa_background', 'normal' => '#bbbbbb', 'active' => '#333333', 'disabled' => '#e45050', 'outofstock' => '#e45050' ) );
						$curr['ivpa_tooltip'] =( isset($curr_customizations['ivpa_tooltip'][$k]) ? $curr_customizations['ivpa_tooltip'][$k] : array() );

						if ( taxonomy_exists( $curr_term_sanitized ) ) {
							$curr['terms'] = wc_get_product_terms( $product->id, $curr_term_sanitized, array( 'fields' => 'all') );

						}
						else {
							if ( isset( $available_attributes[$v] ) ) {
								$custom_vals = array_map( 'trim', explode( WC_DELIMITER, $available_attributes[$v]['value'] ) );
								foreach ( $custom_vals as $cv ) {
									$curr['terms'][$cv] = new stdClass();
									$curr['terms'][$cv]->name = ucfirst( $cv );
									$curr['terms'][$cv]->slug = $cv;
								}
							}
						}

						if ( $curr_is_loop == 'single' && $curr['style'] == 'ivpa_text' ) {
							switch ( $curr['custom']['style'] ) {

								case 'ivpa_border' :
							?>
								<style type="text/css">
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active {
										border-color:<?php echo $curr['custom']['normal']; ?>;
									}
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked,
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
										border-color:<?php echo $curr['custom']['active']; ?>;
									}
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_disabled {
										border-color:<?php echo $curr['custom']['disabled']; ?>;
									}
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text.ivpa_border .ivpa_term.ivpa_active.ivpa_outofstock {
										border-color:<?php echo $curr['custom']['outofstock']; ?>;
									}
								</style>
							<?php
								break;


								case 'ivpa_background' :
							?>
								<style type="text/css">
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active {
										background-color:<?php echo $curr['custom']['normal']; ?>;
									}
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked,
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_clicked.ivpa_outofstock {
										background-color:<?php echo $curr['custom']['active']; ?>;
									}
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_disabled {
										background-color:<?php echo $curr['custom']['disabled']; ?>;
									}
									#ivpa-content .ivpa_attribute[data-attribute="<?php echo $v; ?>"].ivpa_text .ivpa_term.ivpa_active.ivpa_outofstock {
										background-color:<?php echo $curr['custom']['outofstock']; ?>;
									}
								</style>
							<?php

								break;

							}
						}

						if ( $curr['style'] == 'ivpa_selectbox' ) {
						?>
							<strong class="ivpa_title ivpa_selectbox_title">
								<?php
									if ( $curr['title'] == '' ) {
										echo wc_attribute_label( $curr_term_sanitized );
									}
									else {
										echo $curr['title'];
									}
								?>
							</strong>
						<?php
						}
					?>
						<div class="ivpa_attribute <?php echo $curr['style'] . ' ' . ( $curr['style'] == 'ivpa_text' ? $curr['custom']['style'] : '' ) . ' ' . ( $curr_is_loop == 'loop' ? self::$settings['wc_settings_ivpa_archive_mode'] : '' ); ?>" data-attribute="<?php echo $v; ?>">
						<?php

							if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $v ) ] ) ) {
								$selected_attr = $_REQUEST[ 'attribute_' . sanitize_title( $v ) ];
							}
							else if ( isset( $selected_attributes[ sanitize_title( $v ) ] ) ) {
								$selected_attr = $selected_attributes[ sanitize_title( $v ) ];
							}
							else {
								$selected_attr = '';
							}

							if ( $curr_is_loop == 'single' || $curr['style'] == 'ivpa_selectbox' ) {
						?>
							<strong class="ivpa_title">
								<?php
									if ( $curr['style'] == 'ivpa_selectbox' ) {
										if ( $selected_attr == '' ) {
											if ( $curr_is_loop == 'single' ) {
												_e( 'Select', 'ivpawoo' );
											}
											else {
												if ( $curr['title'] == '' ) {
													echo wc_attribute_label( $curr_term_sanitized );
												}
												else {
													echo $curr['title'];
												}
											}
										}
										else {
											$term = get_term_by( 'slug', $selected_attr, sanitize_title( $v ) );
											if ( !empty( $term ) ) {
												echo $term->name;
											}
										}
									}
									else {
										if ( $curr['title'] == '' ) {
											echo wc_attribute_label( $curr_term_sanitized );
										}
										else {
											echo $curr['title'];
										}
									}
								?>
							</strong>
						<?php
							}

							$curr_add_class = '';

							switch ( $curr['style'] ) {

								case 'ivpa_text' :

									foreach ( $curr['terms'] as $l => $b ) {

										$curr_slug = self::ivpa_wpml_get_current_slug( $b, $curr_term_sanitized );

										if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
											continue;

										if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
											$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
										}

									?>
										<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>" >
											<?php
												echo $b->name;
												if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
											?>
												<span class="ivpa_tooltip"><span><?php echo $curr['ivpa_tooltip'][$b->slug]; ?></span></span>
											<?php
												}
											?>
										</span>
									<?php
									}

								break;


								case 'ivpa_color' :

									foreach ( $curr['terms'] as $l => $b ) {

										if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
											continue;

										if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
											$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
										}

									?>
										<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>">
											<span style="background-color:<?php echo $curr['custom'][$b->slug]; ?>"></span>
										<?php
											if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
										?>
											<span class="ivpa_tooltip"><span><?php echo $curr['ivpa_tooltip'][$b->slug]; ?></span></span>
										<?php
											}
										?>
										</span>
									<?php
									}

								break;


								case 'ivpa_image' :

									foreach ( $curr['terms'] as $l => $b ) {

										if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
											continue;

										if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
											$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
										}

									?>
										<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>">
											<img src="<?php echo $curr['custom'][$b->slug]; ?>" alt="<?php echo $b->name; ?>" />
										<?php
											if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
										?>
											<span class="ivpa_tooltip"><span><?php echo $curr['ivpa_tooltip'][$b->slug]; ?></span></span>
										<?php
											}
										?>
										</span>
									<?php
									}

								break;

								case 'ivpa_html' :

									foreach ( $curr['terms'] as $l => $b ) {

										if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
											continue;

										if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
											$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
										}

									?>
										<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>">
											<?php echo $curr['custom'][$b->slug]; ?>
										<?php
											if ( $curr_is_loop == 'single' && isset( $curr['ivpa_tooltip'] ) && isset( $curr['ivpa_tooltip'][$b->slug] ) && $curr['ivpa_tooltip'][$b->slug] !== '' ) {
										?>
											<span class="ivpa_tooltip"><span><?php echo $curr['ivpa_tooltip'][$b->slug]; ?></span></span>
										<?php
											}
										?>
										</span>
									<?php
									}

								break;

								case 'ivpa_selectbox' :

									foreach ( $curr['terms'] as $l => $b ) {

										if ( !empty( $curr_attributes[$curr_term_sanitized] ) && !in_array( $b->slug, $curr_attributes[$curr_term_sanitized] ) )
											continue;

										if ( ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_mode'] == 'ivpa_showonly' ) === false ) {
											$curr_add_class = ( sanitize_title( $b->slug ) == sanitize_title( $selected_attr ) ? ' ivpa_clicked' : '' );
										}

									?>
										<span class="ivpa_term ivpa_active<?php echo $curr_add_class; ?>" data-term="<?php echo $b->slug; ?>">
											<?php echo $b->name; ?>
										</span>
									<?php
									}

								break;

								default :
								break;

							}

							if ( $curr_is_loop == 'single' && $curr['desc'] !== '' && $curr['style'] !== 'ivpa_selectbox' ) {
						?>
							<em class="ivpa_desc"><?php echo $curr['desc']; ?></em>
						<?php
							}
						?>
						</div>
					<?php
						if ( $curr_is_loop == 'single' && $curr['desc'] !== '' && $curr['style'] == 'ivpa_selectbox' ) {
					?>
						<em class="ivpa_desc"><?php echo $curr['desc']; ?></em>
					<?php
						}
					}
						if ( $curr_is_loop == 'single' ) {
						?>
							<a class="ivpa_reset_variations" href="#reset"><?php _e( 'Clear selection', 'ivpawoo' ); ?></a>
						<?php
						}

						if ( $curr_is_loop == 'loop' && self::$settings['wc_settings_ivpa_archive_quantity'] == 'yes' ) {
						?>
							<div class="ivpa_quantity">
								<small><?php _e( 'Qty:', 'ivpawoo' ); ?></small>
								<input type="number" class="ivpa_qty" value="1" min="1" />
							</div>
						<?php
						}

					?>
					</div>
				<?php

					$html = trim( ob_get_clean() );

					echo str_replace( '%%%JSON_REPLACE_IVPA%%%', esc_attr( json_encode( $available_variations ) ), $html );

					if ( self::$settings['wc_settings_ivpa_use_caching'] == 'yes' ) {
						update_post_meta( get_the_ID(), '_ivpa_cached_' . $curr_is_loop . '_' . get_locale(), $html );
						update_post_meta( get_the_ID(), '_ivpa_cached_data', $available_variations );
					}

				}

			}

		}

		function ivpa_add_to_cart_callback() {

			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
			$variation_id = $_POST['variation_id'];

			if ( is_array($_POST['variation']) ) {
				foreach ( $_POST['variation'] as $k => $v ) {
					$variation[$k] = self::utf8_urldecode($v);
				}
			}
			else {
				$variation = array();
			}

			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		
			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
					wc_add_to_cart_message( $product_id );
				}
				$data = WC_AJAX::get_refreshed_fragments();
			} else {

					WC_AJAX::json_headers();

					$data = array(
						'error' => true,
						'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
						);

					$data = json_encode( $data );
			}

			die($data);
			exit();

		}

		function ivpa_repair_cart(){
			if ( defined( 'DOING_AJAX' ) ) {
				wc_setcookie( 'woocommerce_items_in_cart', 1 );
				wc_setcookie( 'woocommerce_cart_hash', md5( json_encode( WC()->cart->get_cart() ) ) );
				do_action( 'woocommerce_set_cart_cookies', true );
			}
		}

		public static function ivpa_wpml_language() {

			if( class_exists( 'SitePress' ) ) {
				global $sitepress;

				if ( method_exists( $sitepress, 'get_default_language' ) ) {

					$default_language = $sitepress->get_default_language();
					$current_language = $sitepress->get_current_language();

					if ( $default_language != $current_language ) {
						return sanitize_title( $current_language );
					}
				}
			}
			return false;

		}

		public static function ivpa_wpml_get_current_slug( $curr_term, $attr ) {

			if( function_exists( 'icl_object_id' ) ) {

				global $sitepress;

				if ( method_exists( $sitepress, 'get_default_language' ) ) {

					$default_language = $sitepress->get_default_language();
					$current_language = $sitepress->get_current_language();

					if ( $default_language != $current_language ) {

						$term_id = icl_object_id( $curr_term->term_id, $attr, false, $default_language );
						$term = get_term( $term_id, $attr );

						return $term->slug;

					}

				}

			}

			return $curr_term->slug;

		}

		public static function prdctfltr_wpml_include_terms( $curr_include, $attr ) {

			if( function_exists( 'icl_object_id' ) ) {
				global $sitepress;

				if ( method_exists( $sitepress, 'get_default_language' ) ) {

					$translated_include = array();

					foreach( $curr_include as $curr ) {
						$current_term = get_term_by( 'slug', $curr, $attr );

						if($current_term) {
							$default_language = $sitepress->get_default_language();
							$current_language = $sitepress->get_current_language();

							$term_id = $current_term->term_id;
							if ( $default_language != $current_language ) {
								$term_id = icl_object_id( $term_id, $attr, false, $default_language );
							}

							$term = get_term( $term_id, $attr );
							$translated_include[] = $term->slug;

						}
					}

					return $translated_include;
				}
			}

			return $curr_include;

		}

	}

	add_action( 'init', array( 'WC_Improved_Variable_Product_Attributes', 'init' ), 998 );

	if ( is_admin() ) {

		include( plugin_dir_path( __FILE__ ) . 'includes/ivpa-settings.php' );

		$purchase_code = get_option( 'wc_settings_ivpa_purchase_code', '' );

		if ( $purchase_code ) {
			require 'includes/update/plugin-update-checker.php';
			$pf_check = PucFactory::buildUpdateChecker(
				'http://mihajlovicnenad.com/envato/get_json.php?p=9981757&k=' . $purchase_code,
				__FILE__
			);
		}

	}

?>