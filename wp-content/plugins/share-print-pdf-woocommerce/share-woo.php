<?php
/*
Plugin Name: Share, Print and PDF Products for WooCommerce
Plugin URI: http://www.mihajlovicnenad.com/product-filter
Description: Share, Print and PDF Products for Woocommerce! It is going viral! - mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 1.2.0
Author URI: http://www.mihajlovicnenad.com
*/

	class WC_Share_Print_PDF {

		public static $id;
		public static $dir;
		public static $path;
		public static $url_path;
		public static $settings;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			if ( !class_exists( 'WooCommerce' ) ) {
				return false;
			}

			self::$dir = trailingslashit( dirname( __FILE__ ) );
			self::$path = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$url_path = plugins_url( trailingslashit( basename( self::$dir ) ) );

			self::$settings['wc_settings_spp_enable'] = get_option( 'wc_settings_spp_enable', 'override' );
			self::$settings['wc_settings_spp_action'] = get_option( 'wc_settings_spp_action', '' );
			self::$settings['wc_settings_spp_counts'] = get_option( 'wc_settings_spp_counts', 'no' );
			self::$settings['wc_settings_spp_shares'] = get_option( 'wc_settings_spp_shares', array() );
			self::$settings['wc_settings_spp_style'] = get_option( 'wc_settings_spp_style', 'line-icons' );
			self::$settings['wc_settings_spp_logo'] = get_option( 'wc_settings_spp_logo', '' );

			self::$settings['wc_settings_spp_header_after'] = get_option( 'wc_settings_spp_header_after', '' );
			self::$settings['wc_settings_spp_product_before'] = get_option( 'wc_settings_spp_product_before', '' );
			self::$settings['wc_settings_spp_product_after'] = get_option( 'wc_settings_spp_product_after', '' );

			if ( self::$settings['wc_settings_spp_enable'] == 'override' ) {
				add_filter( 'wc_get_template_part', __CLASS__ . '::add_filter', 10, 3 );
				add_filter( 'woocommerce_locate_template', __CLASS__ . '::add_loop_filter', 10, 3 );
			}
			else if ( self::$settings['wc_settings_spp_enable'] == 'action' && self::$settings['wc_settings_spp_action'] !== '' ) {

				$set = explode( ':', self::$settings['wc_settings_spp_action'] );

				$action = $set[0];
				$priority = isset( $curr_action[1] ) ? floatval( $curr_action[1] ) : 10;

				add_filter( $action, __CLASS__ . '::get_shares' , $priority );
			}

			add_action( 'wp_enqueue_scripts', __CLASS__ . '::scripts' );
			add_action( 'init', __CLASS__ . '::setup_shares', 999 );

			add_action( 'wp_ajax_nopriv_wcspp_quickview', __CLASS__ . '::wcspp_quickview' );
			add_action( 'wp_ajax_wcspp_quickview', __CLASS__ . '::wcspp_quickview' );

			add_action( 'wp', __CLASS__ . '::create_settings' );

			add_shortcode( 'shareprintpdf', __CLASS__ . '::shortcode' );

			add_action( 'init', __CLASS__ . '::text_domain', 1000 );

		}

		public static function create_settings() {
			self::$id = get_the_ID();
		}

		function text_domain() {
			$dir = trailingslashit( WP_LANG_DIR );
			load_plugin_textdomain( 'wcsppdf', false, $dir . 'plugins' );
		}

		public static function scripts() {

			wp_register_style( 'wcspp', self::$url_path .'lib/css/style.css', false, '1.2.0' );
			wp_enqueue_style( 'wcspp' );

			wp_register_script( 'wcspp', self::$url_path .'lib/js/scripts.js', array( 'jquery' ), '1.2.0', true );
			wp_enqueue_script( 'wcspp' );

			$args = array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'url' => self::$url_path,
				'style' => self::get_style(),
				'product_url' => get_the_permalink( self::$id ),
				'pdfmake' => self::$url_path .'lib/js/pdfmake.min.js',
				'pdffont' => self::$url_path .'lib/js/vfs_fonts.js',
				'showcounts' => self::$settings['wc_settings_spp_counts']
			);

			wp_localize_script( 'wcspp', 'wcspp', $args );

		}
		public static function add_filter( $template, $slug, $name ) {

			if ( in_array( $slug, array( 'single-productw/share.php' ) ) ) {

				if ( $name ) {
					$path = self::$path . WC()->template_path() . "{$slug}-{$name}.php";
				} else {
					$path = self::$path . WC()->template_path() . "{$slug}.php";
				}

				return file_exists( $path ) ? $path : $template;

			}
			else {
				return $template;
			}

		}

		public static function add_loop_filter( $template, $template_name, $template_path ) {

			if ( in_array( $template_name, array( 'single-product/share.php' ) ) ) {

				$path = self::$path . $template_path . $template_name;

				return file_exists( $path ) ? $path : $template;

			}
			else {
				return $template;
			}

		}

		public static function get_shares() {

			include( self::$dir . 'woocommerce/single-product/share.php' );

		}

		public static function setup_shares() {

			$shares = array(
				'facebook',
				'twitter',
				'google',
				'pin',
				'linked',
					'print',
				'pdf'
			);

			$disallowed = self::$settings['wc_settings_spp_shares'];

			$priority = 5;

			foreach( $shares as $share ) {

				if ( in_array( $share, self::$settings['wc_settings_spp_shares'] ) ) {
					continue;
				}

				switch( $share ) {
					case 'facebook' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_facebook', $priority );
					break;
					case 'twitter' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_twitter', $priority );
					break;
					case 'google' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_google', $priority );
					break;
					case 'pin' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_pin', $priority );
					break;
					case 'linked' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_linked', $priority );
					break;
					case 'print' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_print', $priority );
					break;
					case 'pdf' :
						add_action( 'wc_shareprintpdf_icons', __CLASS__ . '::get_icon_pdf', $priority );
					break;
					default :
					break;
				}
				

				$priority = $priority + 5;

			}

		}

		public static function get_icon_facebook() {

			$id = self::$id;
			$link = get_the_permalink( $id );
			$title = get_the_title( $id );

			$url = 'http://www.facebook.com/sharer.php?u=' . $link;

			$extras = ' data-href="' . $link . '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"';

			$icon = 'FB';
			$icon_path = self::$path . 'lib/images/facebook.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}

			$share = array(
				'type' => 'facebook',
				'count' => '...',
				'url' => $url,
				'content' => $icon,
				'extras' => $extras,
				'class' => ''
			);

			self::wrap_icon( $share );
		}

		public static function get_icon_twitter() {

			$id = self::$id;
			$link = get_the_permalink( $id );
			$title = get_the_title( $id );

			$url = 'http://twitter.com/home/?status=' . $title . ' - ' . wp_get_shortlink( $id );

			$extras = ' data-count-layout="horizontal" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"';

			$icon = 'TW';
			$icon_path = self::$path . 'lib/images/twitter.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}

			$share = array(
				'type' => 'twitter',
				'count' => false,
				'url' => $url,
				'content' => $icon,
				'extras' => $extras,
				'class' => 'wcspp-nocounts'
			);

			self::wrap_icon( $share );
		}

		public static function get_icon_google() {

			$id = self::$id;
			$link = get_the_permalink( $id );
			$title = get_the_title( $id );


			$url = 'https://plus.google.com/share?url=' . $link;

			$extras = ' data-href="' . $link .'" data-send="false" data-layout="button_count" data-width="60" data-show-faces="false" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"';

			$icon = 'G+';
			$icon_path = self::$path . 'lib/images/google.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}

			$share = array(
				'type' => 'google',
				'count' => self::get_plusones( $link ),
				'url' => $url,
				'content' => $icon,
				'extras' => $extras,
				'class' => ''
			);

			self::wrap_icon( $share );
		}

		function get_plusones( $link ) {

			$expire = 600;

			$url_code = md5( $link . $expire );
			$transient = '_wcspp_cnt_gplus_' . $url_code;
			$cached =  get_transient( $transient );

			if ( $cached !== false ) {
				return $cached;
			}
			else {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $link . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				$curl_results = curl_exec ($curl);
				curl_close ($curl);
				$json = json_decode($curl_results, true);

				$count = isset( $json[0]['result']['metadata']['globalCounts']['count'] ) ? intval( $json[0]['result']['metadata']['globalCounts']['count'] ) : '...';

				if ( $count !== '...' ) {
					set_transient( $transient, $count, $expire );
				}

				return $count;
			}
		}

		public static function get_icon_pin() {

			$id = self::$id;
			$link = get_the_permalink( $id );
			$title = get_the_title( $id );
			$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'large');
			$image = $large_image_url[0];

			$url = 'http://pinterest.com/pin/create/button/?url=' . $link . '&media=' . $image .'&description=' . $title;

			$extras = ' onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"';

			$icon = 'PIN';
			$icon_path = self::$path . 'lib/images/pin.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}

			$share = array(
				'type' => 'pin',
				'count' => self::get_pins( $link ),
				'url' => $url,
				'content' => $icon,
				'extras' => $extras,
				'class' => ''
			);

			self::wrap_icon( $share );
		}

		public static function get_pins( $link ) {

			$expire = 600;

			$url_code = md5( $link . $expire );
			$transient = '_wcspp_cnt_pins_' . $url_code;
			$cached =  get_transient( $transient );

			if ( $cached !== false ) {
				return $cached;
			}
			else {

				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, 'https://widgets.pinterest.com/v1/urls/count.json?url=' . $link );
				curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
				curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				$cont = curl_exec($ch);
				if( curl_error( $ch ) ) {
					return '...';
				}

				$json_string = substr( $cont, 13, -1 );
				$json = json_decode( $json_string, true );

				$count = isset( $json['count'] ) ? intval( $json['count'] ) : '...';

				if ( $count !== '...' ) {
					set_transient( $transient, $count, $expire );
				}

				return $count;

			}

		}

		public static function get_icon_linked() {

			$id = self::$id;
			$link = get_the_permalink( $id );
			$title = get_the_title( $id );

			$url = 'http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $link . '&amp;title=' . $title .'&amp;source=' . home_url( '/' );

			$extras = ' onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"';

			$icon = 'LNKD';
			$icon_path = self::$path . 'lib/images/linked.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}

			$share = array(
				'type' => 'linked',
				'count' => '...',
				'url' => $url,
				'content' => $icon,
				'extras' => $extras,
				'class' => ''
			);

			self::wrap_icon( $share );
		}

		public static function get_icon_print() {

			$icon = 'PRNT';
			$icon_path = self::$path . 'lib/images/print.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}

			$share = array(
				'type' => 'print',
				'count' => false,
				'url' => '#',
				'content' => $icon,
				'extras' => '',
				'class' => ''
			);

			self::wrap_icon( $share );
		}

		public static function get_icon_pdf() {

			$icon = 'PRNT';
			$icon_path = self::$path . 'lib/images/pdf.svg';
			if ( file_exists( $icon_path ) ) {
				$icon = file_get_contents( $icon_path );
			}


			$share = array(
				'type' => 'pdf',
				'count' => false,
				'url' => '#',
				'content' => $icon,
				'extras' => '',
				'class' => ''
			);

			self::wrap_icon( $share );
		}

		public static function wrap_icon( $share ) {
?>
			<li class="<?php echo 'wcspp-' . $share['type']; ?>">
				<a href="<?php echo $share['url']; ?>" class="<?php echo $share['class']; ?>"<?php echo $share['extras']; ?> target="_blank">
					<?php
						echo $share['content'];

						if ( self::$settings['wc_settings_spp_counts'] == 'no' ) {
							$share['count'] = false;
						}

						if ( $share['count'] !== false ) {
							echo '<span>' . $share['count'] . '</span>';
						}
					?>
				</a>
			</li>
<?php
		}

		public static function wcspp_quickview() {

			if ( isset( $_POST['product_id'] ) ) {

				$id = $_POST['product_id'];
				$type = $_POST['type'];

				$product = wc_get_product( $id );

				ob_start();
	?>
				<div class="wcspp-quickview">
				<?php

					$cats = strip_tags( $product->get_categories( ', ', '', '' ) );
					$tags = strip_tags( $product->get_tags( ', ', '', '' ) );

					$site_title = get_bloginfo( 'name' );
					$site_desc = get_bloginfo( 'description' );

					$product_title = get_the_title( $id );
					$product_price = $product->get_price_html();

					$product_sku = __( 'SKU', 'wcsppdf' ) . ': ' . $product->get_formatted_name();
					$product_link = __( 'Link', 'wcsppdf' ) . ': ' . get_the_permalink( $id );

					$product_image = $product->get_image( 'shop_catalog' );
					$product_description = get_post_field( 'post_excerpt', $id );

					$product_dimensions = $product->get_dimensions();
					$product_weight = $product->has_weight() ? $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) : '';

					$product_content = get_post_field( 'post_content', $id );

					$attachment_ids = $product->get_gallery_attachment_ids();
					$img = array( '', '', '', '' );
					$i = 0;
					foreach ( $attachment_ids as $attachment_id ) {
						$image = wp_get_attachment_image( $attachment_id, 'shop_thumbnail' );

						if ( !$image ) {
							continue;
						}

						$stripped = array();
						preg_match( '/src="([^"]*)"/i', $image, $stripped ) ;


						$img[$i] =  'data:image/x-icon;base64,' . base64_encode( file_get_contents ( $stripped[1] ) );

						if ( $i == 3 ) {
							break;
						}
						$i++;
					}

					$attributes = $product->get_attributes();
					$attribute_echo = '';
					$i=0;
					if ( !empty( $attributes ) ) {
						foreach( $attributes as $attribute ) {
							if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
								continue;
							}

							if ( $i !== 0 ) {
								$attribute_echo .= '
';
							}
							$attribute_echo .= wc_attribute_label( $attribute['name'] ) . ': ';
							if ( $attribute['is_taxonomy'] ) {
								$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
								$attribute_echo .= apply_filters( 'woocommerce_attribute', implode( ', ', $values ), $attribute, $values );
							} else {
								$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
								$attribute_echo .= apply_filters( 'woocommerce_attribute', implode( ', ', $values ), $attribute, $values );
							}
							$i++;
						}
					}

					$stripped = array();
					preg_match( '/src="([^"]*)"/i', $product_image, $stripped ) ;

					if ( $type == 'pdf' ) {

						$pdf_logo = '';
						if ( self::$settings['wc_settings_spp_logo'] !== '' ) {
							$pdf_logo = base64_encode( file_get_contents ( self::$settings['wc_settings_spp_logo'] ) );
						}

						$pdf_product_image = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=';
						if ( isset( $stripped[1] ) && $stripped[1] !== '' ) {
							$pdf_product_image = base64_encode( file_get_contents ( $stripped[1] ) );
						}

						$pdf_vars = array(
							'site_logo' => $pdf_logo,
							'site_title' => $site_title,
							'site_description' => $site_desc,
							'product_title' => $product_title,
							'product_price' => strip_tags( $product_price ),
							'product_meta' => $product_sku,
							'product_link' => $product_link,
							'product_categories' => ( !empty( $cats ) ? __( 'Categories', 'wcsppdf' ) . ': '. $cats . '' : '' ),
							'product_tags' => ( !empty( $tags ) ? __( 'Tags', 'wcsppdf' ) . ': '. $tags . '' : '' ),
							'product_image' => 'data:image/x-icon;base64,' . $pdf_product_image,
							'product_description' => strip_tags( $product_description ),
							'product_attributes' => $attribute_echo,
							'product_dimensions' => $product_dimensions !== '' ? __( 'Dimensions', 'wcsppdf' ) . ': ' . $product_dimensions : '',
							'product_weight' => $product_weight !== '' ? __( 'Weight', 'wcsppdf' ) . ': ' . $product_weight : '',
							'product_img0' => $img[0],
							'product_img1' => $img[1],
							'product_img2' => $img[2],
							'product_img3' => $img[3],
							'product_content' => strip_tags( $product_content ),
							'header_after' => wp_strip_all_tags( self::$settings['wc_settings_spp_header_after'] ),
							'product_before' => wp_strip_all_tags( self::$settings['wc_settings_spp_product_before'] ),
							'product_after' => wp_strip_all_tags( self::$settings['wc_settings_spp_product_after'] )
						);

						$pdf = ' data-wcspp-pdf="' . esc_attr( json_encode( $pdf_vars ) ) . '"';
					}
				?>
					<div class="wcspp-page-wrap" <?php echo isset( $pdf ) ? $pdf : ''; ?>>
						<a href="#" class="wcspp-go-<?php echo $type; ?>">
						<?php
							$icon = 'IC';
							$icon_path = self::$path . 'lib/images/' . $type . '.svg';
							if ( file_exists( $icon_path ) ) {
								$icon = file_get_contents( $icon_path );
							}
							if ( $type == 'print' ) {
								echo $icon . '<span>' . __( 'Print now!', 'wcsppdf' ) . '</span>';
							}
							else {
								echo $icon . '<span>' . __( 'Download now!', 'wcsppdf' ) . '</span>';
							}
							
						?>
						</a>
						<?php
							if ( self::$settings['wc_settings_spp_logo'] !== '' ) {
								echo '<img src="' . self::$settings['wc_settings_spp_logo'] . '" class="wcspp-logo" />';
							}
						?>
						<span class="wcspp-product-title"><?php echo $site_title; ?></span>
						<span class="wcspp-product-desc"><?php echo $site_desc; ?></span>
						<?php
							if ( self::$settings['wc_settings_spp_header_after'] !== '' ) {
								echo '<div class="wcspp-add">' . self::$settings['wc_settings_spp_header_after'] . '</div>';
							}
						?>
						<hr/>
						<?php
							if ( self::$settings['wc_settings_spp_product_before'] !== '' ) {
								echo '<div class="wcspp-add">' . self::$settings['wc_settings_spp_product_before'] . '</div>';
							}
						?>
						<h1>
							<span class="wcspp-title"><?php echo $product_title; ?></span>
							<span class="wcspp-price"><?php echo $product_price; ?></span>
						</h1>
						<div class="wcspp-meta">
							<p>
							<?php
								echo $product_sku . '<br/>';
								echo $product_link . '<br/>';
								if ( !empty( $cats ) ) {
									echo __( 'Categories', 'wcsppdf' ) . ': '. $cats . '<br/>';
								}
								if ( !empty( $tags ) ) {
									echo __( 'Tags', 'wcsppdf' ) . ': ' . $tags;
								}
							?>
							</p>
						</div>
						<div class="wcspp-main-image">
							<?php echo $product_image; ?>
						</div>
						<div class="wcspp-short-description">
							<p>
								<?php echo $product_description; ?>
							</p>
						<?php
							if ( !empty( $attributes ) ) {
								foreach( $attributes as $attribute ) {
									if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
										continue;
									}

									echo '<h3>';
									echo wc_attribute_label( $attribute['name'] ) . ': ';
									if ( $attribute['is_taxonomy'] ) {
										$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
										echo apply_filters( 'woocommerce_attribute', implode( ', ', $values ), $attribute, $values );
									} else {
										$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
										echo apply_filters( 'woocommerce_attribute', implode( ', ', $values ), $attribute, $values );
									}
									echo '</h3>';
								}
							}

							if ( $product->has_dimensions() ) {
								echo '<h3>' . __( 'Dimensions', 'wcsppdf' ) . ': ' . $product_dimensions . '</h3>';
							}

							if ( $product->has_weight() ) {
								echo '<h3>' . __( 'Weight', 'wcsppdf' ) . ': ' . $product_weight . '</h3>';
							}

						?>
						</div>
						<div class="wcspp-images">
						<?php
							foreach ( $attachment_ids as $attachment_id ) {
								$image = wp_get_attachment_image( $attachment_id, 'shop_thumbnail' );

								if ( !$image ) {
									continue;
								}
								echo $image;
							}
						?>
						</div>
						<div class="wcspp-content">
							<?php echo $product_content; ?>
						</div>
						<?php
							if ( self::$settings['wc_settings_spp_product_after'] !== '' ) {
								echo '<div class="wcspp-add">' . self::$settings['wc_settings_spp_product_after'] . '</div>';
							}
						?>
					</div>
					<span class="wcspp-quickview-close"><span class="wcspp-quickview-close-button"><?php _e( 'Click to close the preview!', 'wcsppdf' ); ?></span></span>
				</div>
<?php
				$out = ob_get_clean();

				die( $out );
				exit;
			}
			die(0);
			exit;
		}

		public static function get_style() {
			$css = file_get_contents( self::$dir . 'lib/css/print.css' );
			return $css;
		}

		public static function shortcode( $atts, $content = null ) {

			global $post;

			if ( $post->post_type == 'product') {
				ob_start();
				self::get_shares();
				return ob_get_clean();
			}

			return;

		}

	}

	add_action( 'init', array( 'WC_Share_Print_PDF', 'init' ), 998 );

	if ( is_admin() ) {
		include_once ( 'lib/spp-settings.php' );
	}

?>