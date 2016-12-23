<?php

	/*
	 * IVPA Settings
	 */
	class WC_Ivpa_Settings {

		public static function init() {
			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::ivpa_add_settings_tab', 50 );
			add_action( 'woocommerce_settings_tabs_ivpawoo', __CLASS__ . '::ivpa_settings_tab' );
			add_action( 'woocommerce_update_options_ivpawoo', __CLASS__ . '::ivpa_save_settings' );
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::ivpa_settings_scripts' );
			add_action( 'wp_ajax_ivpa_get_fields', __CLASS__ . '::ivpa_get_fields' );
			add_action( 'wp_ajax_ivpa_get_terms', __CLASS__ . '::ivpa_get_terms' );
		}

		public static function ivpa_settings_scripts( $settings_tabs ) {
			if ( isset($_GET['page'], $_GET['tab']) && ($_GET['page'] == 'wc-settings' || $_GET['page'] == 'woocommerce_settings') && $_GET['tab'] == 'ivpawoo' ) {
				wp_enqueue_style( 'ivpa-style', WC_Improved_Variable_Product_Attributes::$url_path . '/assets/css/admin.css', false, WC_Improved_Variable_Product_Attributes::$version );
				wp_enqueue_script( 'ivpa-admin', WC_Improved_Variable_Product_Attributes::$url_path . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), WC_Improved_Variable_Product_Attributes::$version, true );
				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
				);
				wp_localize_script( 'ivpa-admin', 'ivpa', $curr_args );

				if ( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}

				wp_enqueue_style('wp-color-picker');
				wp_enqueue_script('wp-color-picker');
			}
		}

		public static function ivpa_add_settings_tab( $settings_tabs ) {
			$settings_tabs['ivpawoo'] = __( 'Improved Variable Product Attributes', 'ivpawoo' );
			return $settings_tabs;
		}

		public static function ivpa_settings_tab() {
			woocommerce_admin_fields( self::ivpa_get_settings( 'get' ) );
		}

		public static function ivpa_save_settings() {

			if ( isset($_POST['ivpa_attr']) ) {

				$ivpa_attrs = array();

				for ( $i = 0; $i < count($_POST['ivpa_attr']); $i++ ) {

					if ( $_POST['ivpa_attr'][$i] !== '' ) {

						$ivpa_attrs['ivpa_attr'][$i] = $_POST['ivpa_attr'][$i];
						$ivpa_attrs['ivpa_title'][$i] = stripslashes($_POST['ivpa_title'][$i]);
						$ivpa_attrs['ivpa_desc'][$i] = stripslashes($_POST['ivpa_desc'][$i]);
						$ivpa_attrs['ivpa_style'][$i] = $_POST['ivpa_style'][$i];
						$ivpa_attrs['ivpa_archive_include'][$i] = isset( $_POST['ivpa_archive_include'][$i] ) ? 'yes' : 'no';

						switch ( $ivpa_attrs['ivpa_style'][$i] ) {

							case 'ivpa_text' :
								$ivpa_attrs['ivpa_custom'][$i]['style'] = $_POST['ivpa_term'][$i]['style'];
								$ivpa_attrs['ivpa_custom'][$i]['normal'] = $_POST['ivpa_term'][$i]['normal'];
								$ivpa_attrs['ivpa_custom'][$i]['active'] = $_POST['ivpa_term'][$i]['active'];
								$ivpa_attrs['ivpa_custom'][$i]['disabled'] = $_POST['ivpa_term'][$i]['disabled'];
								$ivpa_attrs['ivpa_custom'][$i]['outofstock'] = $_POST['ivpa_term'][$i]['outofstock'];
								foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
								}
							break;

							case 'ivpa_color' :
								foreach ( $_POST['ivpa_term'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_custom'][$i][$k] = $v;
								}
								foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
								}
							break;

							case 'ivpa_image' :
								foreach ( $_POST['ivpa_term'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_custom'][$i][$k] = $v;
								}
								foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
								}
							break;

							case 'ivpa_html' :
								foreach ( $_POST['ivpa_term'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_custom'][$i][$k] = stripslashes($v);
								}
								foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
									$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
								}
							break;

							default :
							break;

						}
					}
				}
			}
			else {
				$ivpa_attrs = array();
			}

			woocommerce_update_options( self::ivpa_get_settings( 'update' ) );

			$get_language = WC_Improved_Variable_Product_Attributes::ivpa_wpml_language();

			if ( $get_language === false ) {
				update_option('wc_ivpa_attribute_customization', $ivpa_attrs);
			}
			else {
				update_option('wc_ivpa_attribute_customization_' . $get_language, $ivpa_attrs);
			}

		}

		public static function ivpa_get_settings( $action = 'get' ) {

			$settings = array();

			if ( $action == 'get' ) {

		?>
			<div id="ivpa_manager" class="ivpa_manager">
				<h3><?php _e( 'Attribute Customization Manager', 'ivpawoo' ); ?></h3>
				<p><?php _e( 'Use the manager to customize your attributes! Click the Add Attribute Customization button to start customizing!', 'ivpawoo' ); ?></p>
				<div class="ivpa_fields">
					<a href="#" class="ivpa_add_customization button-primary"><?php _e( 'Add Attribute Customization', 'ivpawoo' ); ?></a>
				</div>
				<div class="ivpa_customizations">
			<?php

				$curr_language = WC_Improved_Variable_Product_Attributes::ivpa_wpml_language();

				if ( isset($_POST['ivpa_attr']) ) {

					$ivpa_attrs = array();

					for ( $i = 0; $i < count($_POST['ivpa_attr']); $i++ ) {

						if ( $_POST['ivpa_attr'][$i] !== '' ) {

							$ivpa_attrs['ivpa_attr'][$i] = $_POST['ivpa_attr'][$i];
							$ivpa_attrs['ivpa_title'][$i] = stripslashes($_POST['ivpa_title'][$i]);
							$ivpa_attrs['ivpa_desc'][$i] = stripslashes($_POST['ivpa_desc'][$i]);
							$ivpa_attrs['ivpa_style'][$i] = $_POST['ivpa_style'][$i];
							$ivpa_attrs['ivpa_archive_include'][$i] = isset( $_POST['ivpa_archive_include'][$i] ) ? 'yes' : 'no';

							switch ( $ivpa_attrs['ivpa_style'][$i] ) {

								case 'ivpa_text' :
									$ivpa_attrs['ivpa_custom'][$i]['style'] = $_POST['ivpa_term'][$i]['style'];
									$ivpa_attrs['ivpa_custom'][$i]['normal'] = $_POST['ivpa_term'][$i]['normal'];
									$ivpa_attrs['ivpa_custom'][$i]['active'] = $_POST['ivpa_term'][$i]['active'];
									$ivpa_attrs['ivpa_custom'][$i]['disabled'] = $_POST['ivpa_term'][$i]['disabled'];
									$ivpa_attrs['ivpa_custom'][$i]['outofstock'] = $_POST['ivpa_term'][$i]['outofstock'];
									foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
									}
								break;

								case 'ivpa_color' :
									foreach ( $_POST['ivpa_term'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_custom'][$i][$k] = $v;
									}
									foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
									}
								break;

								case 'ivpa_image' :
									foreach ( $_POST['ivpa_term'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_custom'][$i][$k] = $v;
									}
									foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
									}
								break;

								case 'ivpa_html' :
									foreach ( $_POST['ivpa_term'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_custom'][$i][$k] = stripslashes($v);
									}
									foreach ( $_POST['ivpa_tooltip'][$i] as $k => $v ) {
										$ivpa_attrs['ivpa_tooltip'][$i][$k] = $v;
									}
								break;

								default :
								break;

							}
						}
					}

					$curr_customizations = $ivpa_attrs;

				}
				else {
					if ( $curr_language === false ) {
						$curr_customizations = get_option( 'wc_ivpa_attribute_customization', '' );
					}
					else {
						$curr_customizations = get_option( 'wc_ivpa_attribute_customization_' . $curr_language, '' );
					}
				}

				if ( $curr_customizations == '' ) {
					$curr_customizations = array();
				}

				$attributes = WC_Improved_Variable_Product_Attributes::ivpa_get_attributes();

				$select_attributes = array();

				foreach( $attributes as $attribute ) {
					$select_attributes[$attribute] = wc_attribute_label( $attribute );
				}

				if ( !empty($curr_customizations) ) {

					for ( $i = 0; $i < count($curr_customizations['ivpa_attr']); $i++ ) {

						$html = '<div class="ivpa_element" data-id="' . $i . '"><div class="ivpa_manipulate"><a href="#" class="ivpa_attribute_title">' . wc_attribute_label( $curr_customizations['ivpa_attr'][$i] ) . '</a><a href="#" class="ivpa_remove"><i class="ivpa-remove"></i></a><a href="#" class="ivpa_reorder"><i class="ivpa-reorder"></i></a><a href="#" class="ivpa_slidedown"><i class="ivpa-slidedown"></i></a><div class="ivpa_clear"></div></div><div class="ivpa_holder">';

						$html .= '<label><span>' . __( 'Select Attribute', 'ivpawoo' ) . '</span> <select class="ivpa_attr_select ivpa_s_attribute" name="ivpa_attr[' . $i . ']">';

						$html .= '<option value="">' . __('Select Attribute', 'ivpawoo') . '</option>';

						foreach ( $attributes as $k => $v ) {
							if ( wp_count_terms( $v, array( 'hide_empty' => false ) ) < 1 ) {
								continue;
							}

							$curr_label = wc_attribute_label( $v );
							$html .= '<option value="' . $v . '"' . ( $curr_customizations['ivpa_attr'][$i] == $v ? ' selected="selected"' : '' ) . '>' . $curr_label . '</option>';
						}

						$html .= '</select></label>';

						$html .= '<label><span>' . __( 'Override Attribute Name', 'ivpawoo' ) . '</span> <input type="text" name="ivpa_title[' . $i . ']" value="' . $curr_customizations['ivpa_title'][$i] . '" /></label>';

						$html .= '<label><span>' . __( 'Add Attribute Description' ,'ivpawoo' ) . '</span> <textarea name="ivpa_desc[' . $i . ']">' . $curr_customizations['ivpa_desc'][$i] . '</textarea></label>';

						$html .= '<label><span>' . __( 'Select Attribute Style', 'ivpawoo' ) . '</span> <select class="ivpa_attr_select ivpa_s_style" name="ivpa_style[' . $i . ']">';

						$styles = array(
							'ivpa_text' => __( 'Plain Text', 'ivpawoo' ),
							'ivpa_color' => __( 'Color', 'ivpawoo' ),
							'ivpa_image' => __( 'Thumbnail', 'ivpawoo' ),
							'ivpa_selectbox' => __( 'Select Box', 'ivpawoo' ),
							'ivpa_html' => __( 'HTML', 'ivpawoo' )
						);

						foreach ( $styles as $k => $v ) {
							$html .= '<option value="' . $k . '"' . ( $curr_customizations['ivpa_style'][$i] == $k ? ' selected="selected"' : '' ) . '>' . $v . '</option>';
						}

						$html .= '</select></label>';

						$html .= '<label><input type="checkbox" name="ivpa_archive_include[' . $i . ']"' . ( isset( $curr_customizations['ivpa_archive_include'][$i] ) && $curr_customizations['ivpa_archive_include'][$i] == 'yes' ? ' checked="checked"' : '' ) . ' /> <span class="ivpa_checkbox_desc">' . __( 'Show on Shop/Archives (This only works if the Shop/Archive mode is set to Show Only)', 'ivpawoo' ) . '</span></label>';

						$html .= '<div class="ivpa_terms">';

						$curr_tax = $curr_customizations['ivpa_attr'][$i];
						$curr_style = $curr_customizations['ivpa_style'][$i];

						$catalog_attrs = get_terms( $curr_tax, array( 'hide_empty' => false ) );

						if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){

							ob_start();

							switch ( $curr_style ) {

								case 'ivpa_text' :

									?>
										<div class="ivpa_term_style">
											<span class="ivpa_option">
												<?php _e('CSS', 'ivpawoo'); ?>
												<select name="ivpa_term[<?php echo $i; ?>][style]">
											<?php
												$styles = array(
													'ivpa_border' => __( 'Border', 'ivpawoo' ),
													'ivpa_background' => __( 'Background', 'ivpawoo' ),
													'ivpa_round' => __( 'Round', 'ivpawoo' )
												);

												foreach ( $styles as $k => $v ) {
											?>
													<option value="<?php echo $k; ?>"<?php echo ( $curr_customizations['ivpa_custom'][$i]['style'] == $k ? ' selected="selected"' : '' ); ?>><?php echo $v; ?></option>
											<?php
												}
											?>
												</select>
											</span>
											<span class="ivpa_option">
												<?php _e('Normal', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[<?php echo $i; ?>][normal]" value="<?php echo $curr_customizations['ivpa_custom'][$i]['normal']; ?>"/>
											</span>
											<span class="ivpa_option">
												<?php _e('Active', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[<?php echo $i; ?>][active]" value="<?php echo $curr_customizations['ivpa_custom'][$i]['active']; ?>"/>
											</span>
											<span class="ivpa_option">
												<?php _e('Disabled', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[<?php echo $i; ?>][disabled]" value="<?php echo $curr_customizations['ivpa_custom'][$i]['disabled']; ?>"/>
											</span>
											<span class="ivpa_option">
												<?php _e('Out of stock', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[<?php echo $i; ?>][outofstock]" value="<?php echo $curr_customizations['ivpa_custom'][$i]['outofstock']; ?>"/>
											</span>

										</div>
									<?php

									foreach ( $catalog_attrs as $term ) {

									?>
										<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
											<span class="ivpa_option ivpa_option_plaintext">
												<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[<?php echo $i; ?>][<?php echo $term->slug; ?>]" value="<?php echo ( isset( $curr_customizations['ivpa_tooltip'][$i][$term->slug] ) ? $curr_customizations['ivpa_tooltip'][$i][$term->slug] : '' ); ?>"/>
											</span>
										</div>
									<?php
									}

								break;

								case 'ivpa_color' :

									foreach ( $catalog_attrs as $term ) {

									?>
										<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
											<span class="ivpa_option ivpa_option_color">
												<em><?php echo $term->name . ' ' . __('Color', 'ivpawoo'); ?></em> <input class="ivpa_color" type="text" name="ivpa_term[<?php echo $i; ?>][<?php echo $term->slug; ?>]" value="<?php echo $curr_customizations['ivpa_custom'][$i][$term->slug]; ?>" />
											</span>
											<span class="ivpa_option">
												<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[<?php echo $i; ?>][<?php echo $term->slug; ?>]" value="<?php echo ( isset( $curr_customizations['ivpa_tooltip'][$i][$term->slug] ) ? $curr_customizations['ivpa_tooltip'][$i][$term->slug] : '' ); ?>"/>
											</span>
										</div>
									<?php
									}

								break;

								case 'ivpa_image' :

									foreach ( $catalog_attrs as $term ) {

									?>
										<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
											<span class="ivpa_option">
												<em><?php echo $term->name . ' ' . __('Image URL', 'ivpawoo'); ?></em> <input type="text" name="ivpa_term[<?php echo $i; ?>][<?php echo $term->slug; ?>]" value="<?php echo $curr_customizations['ivpa_custom'][$i][$term->slug]; ?>"/>
											</span>
											<span class="ivpa_option ivpa_option_button">
												<em><?php _e( 'Add/Upload image', 'ivpawoo' ); ?></em> <a href="#" class="ivpa_upload_media button"><?php _e('Image Gallery', 'ivpawoo'); ?></a>
											</span>
											<span class="ivpa_option">
												<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[<?php echo $i; ?>][<?php echo $term->slug; ?>]" value="<?php echo ( isset( $curr_customizations['ivpa_tooltip'][$i][$term->slug] ) ? $curr_customizations['ivpa_tooltip'][$i][$term->slug] : '' ); ?>"/>
											</span>
										</div>
									<?php
									}

								break;

								case 'ivpa_html' :

									foreach ( $catalog_attrs as $term ) {

									?>
										<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
											<span class="ivpa_option ivpa_option_text">
												<em><?php echo $term->name . ' ' . __('HTML', 'ivpawoo'); ?></em> <textarea type="text" name="ivpa_term[<?php echo $i; ?>][<?php echo $term->slug; ?>]"><?php echo $curr_customizations['ivpa_custom'][$i][$term->slug]; ?></textarea>
											</span>
											<span class="ivpa_option">
												<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[<?php echo $i; ?>][<?php echo $term->slug; ?>]" value="<?php echo ( isset( $curr_customizations['ivpa_tooltip'][$i][$term->slug] ) ? $curr_customizations['ivpa_tooltip'][$i][$term->slug] : '' ); ?>"/>
											</span>
										</div>
									<?php
									}

								break;

								case 'ivpa_selectbox' :
								?>
									<div class="ivpa_selectbox"><i class="ivpa-warning"></i> <span><?php _e( 'This style has no extra settings!', 'ivpawoo' ); ?></span></div>
								<?php
								break;

								default :
								break;

							}

							$html .= ob_get_clean();

						}

						$html .= '</div>';

						$html .= '</div></div>';

						echo $html;

					}

				}
			?>
				</div>
			</div>
		<?php
			}

			$choices_image_size = array(
				'full' => 'full'
			);

			$image_sizes = get_intermediate_image_sizes();

			foreach ( $image_sizes as $image_size ) {
				$choices_image_size[$image_size] = $image_size;
			}

			$settings = array(
				'section_single_title' => array(
					'name' => __( 'Single Product Page Settings', 'ivpawoo' ),
					'type' => 'title',
					'desc' => __( 'General plugin settings when used in Single Product pages.', 'ivpawoo' )
				),
				'ivpa_single_enable' => array(
					'name' => __( 'Enable/Disable Attributes In Single Product Pages', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable attribute selection in single product pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_single_enable',
					'default' => 'yes'
				),
				'ivpa_single_selectbox' => array(
					'name' => __( 'Hide Default Select Boxes', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to hide default select boxes in single product pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_single_selectbox',
					'default' => 'yes'
				),
				'ivpa_single_image' => array(
					'name' => __( 'Use IVPA Image Switcher', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable IVPA image switcher in single product pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_single_image',
					'default' => 'yes'
				),
				'ivpa_single_ajax' => array(
					'name' => __( 'Enable/Disable AJAX Variation Add To Cart', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable AJAX add to cart in single product pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_single_ajax',
					'default' => 'no'
				),
				'ivpa_single_image_size' => array(
					'name' => __( 'Select Single Image Size', 'ivpawoo' ),
					'type' => 'select',
					'desc' => __( 'If the default setting in single products returns a false image upon selecting or deselecting please use this setting to override image size. Default: full (works almost anywhere), but usually: shop_single or set your own image size.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_single_image_size',
					'options' =>$choices_image_size,
					'default' => 'full',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_single_action' => array(
					'name' => __( 'Override Default Single Product Action', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Change default init action in single product pages. Use actions initiated in your content-single-product.php file. Please enter action in the following format action_name:priority.', 'ivpawoo' ) . ' ( default: woocommerce_before_add_to_cart_form )',
					'id'   => 'wc_settings_ivpa_single_action',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'section_single_end' => array(
					'type' => 'sectionend'
				),
				'section_archive_title' => array(
					'name' => __( 'Shop/Product Archive Settings', 'ivpawoo' ),
					'type' => 'title',
					'desc' => __( 'General plugin settings when used in Shop and Product Archive pages.', 'ivpawoo' )
				),
				'ivpa_archive_enable' => array(
					'name' => __( 'Enable/Disable Attributes In Archives', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable attribute selection in shop and product archive pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_archive_enable',
					'default' => 'no'
				),
				'ivpa_archive_quantity' => array(
					'name' => __( 'Show Quantities In Archives', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable product quantity in shop and product archive pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_archive_quantity',
					'default' => 'no'
				),
				'ivpa_archive_mode' => array(
					'name' => __( 'Select Archive Display Mode', 'ivpawoo' ),
					'type' => 'select',
					'desc' => __( 'Select style to use with the attributes in shop and product archive pages.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_archive_mode',
					'options' => array(
						'ivpa_showonly' => __( 'Show Only', 'ivpawoo' ),
						'ivpa_selection' => __( 'Enable Selection and Add to Cart', 'ivpawoo' )
					),
					'default' => 'ivpa_selection',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_archive_image_size' => array(
					'name' => __( 'Select Archive Image Size', 'ivpawoo' ),
					'type' => 'select',
					'desc' => __( 'If the default setting in archives returns a false image upon selecting or deselecting please use this setting to override image size. Default: full (works almost anywhere), but usually: shop_catalog, or set your own image size.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_archive_image_size',
					'options' =>$choices_image_size,
					'default' => 'full',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_archive_action' => array(
					'name' => __( 'Override Default Product Archive Action', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Change default init action in product archives. Use actions initiated in your content-product.php file. Please enter action in the following format action_name:priority.', 'ivpawoo' ) . ' ( default: woocommerce_after_shop_loop_item:999 )',
					'id'   => 'wc_settings_ivpa_archive_action',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'section_archive_end' => array(
					'type' => 'sectionend'
				),
				'section_selectors_title' => array(
					'name' => __( 'jQuery Selector Settings', 'ivpawoo' ),
					'type' => 'title',
					'desc' => __( 'Sometimes your theme will not have the default classes for these elements. If this is the case use these options to override default jQuery selectors.', 'ivpawoo' )
				),
				'ivpa_single_selector' => array(
					'name' => __( 'Single Product Image Selector', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Change default image wrapper selector in single product pages.', 'ivpawoo' ) . ' (default: .type-product .images )',
					'id'   => 'wc_settings_ivpa_single_selector',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_archive_selector' => array(
					'name' => __( 'Shop/Archive Product Selector', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Change default product selector in shop and product archives. Use the product class from your product archive pages.', 'ivpawoo' ) . ' (default: .type-product )',
					'id'   => 'wc_settings_ivpa_archive_selector',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_addcart_selector' => array(
					'name' => __( 'Shop/Archive Add To Cart Selector', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Change default add to cart selector in shop and product archives. Use the product class from your product archive pages.', 'ivpawoo' ) . ' (default: .add_to_cart_button.product_type_variable )',
					'id'   => 'wc_settings_ivpa_addcart_selector',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_price_selector' => array(
					'name' => __( 'Shop/Archive Price Selector', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Change default price selector in shop and product archives. Use the price class from your product archive pages.', 'ivpawoo' ) . ' (default: .price )',
					'id'   => 'wc_settings_ivpa_price_selector',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'section_selectors_end' => array(
					'type' => 'sectionend'
				),
				'section_outofstock_title' => array(
					'name' => __( 'Out Of Stock Display Settings', 'ivpawoo' ),
					'type' => 'title',
					'desc' => __( 'Setup your out of stock selectors appearance.', 'ivpawoo' )
				),
				'ivpa_outofstock_mode' => array(
					'name' => __( 'Select Out Of Stock Mode', 'ivpawoo' ),
					'type' => 'select',
					'desc' => __( 'Select how the Out of Stock selectors will appear.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_outofstock_mode',
					'options' => array(
						'default' => __( 'Shown but not clickable', 'ivpawoo' ),
						'clickable' => __( 'Shown and clickable', 'ivpawoo' ),
						'hidden' => __( 'Hidden from pages', 'ivpawoo' )
					),
					'default' => 'default',
					'css' => 'width:300px;margin-right:12px;'
				),
				'section_outofstock_end' => array(
					'type' => 'sectionend'
				),
				'section_advanced_title' => array(
					'name' => __( 'Advanced Settings', 'ivpawoo' ),
					'type' => 'title',
					'desc' => __( 'Miscellaneous advanced settings.', 'ivpawoo' )
				),
				'ivpa_image_attributes' => array(
					'name' => __( 'Image Changing Attributes', 'ivpawoo' ),
					'type' => 'multiselect',
					'desc' => __( 'Select attributes that will change the product image.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_image_attributes',
					'options' => $select_attributes,
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'ivpa_step_selection' => array(
					'name' => __( 'Step Attribute Selection', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable stepped attribute selection.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_step_selection',
					'default' => 'no'
				),
				'ivpa_disable_unclick' => array(
					'name' => __( 'Disable Attribute Deselection', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to disable attribute deselection in IVPA selectors.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_disable_unclick',
					'default' => 'no'
				),
				'ivpa_force_scripts' => array(
					'name' => __( 'Plugin Scripts', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to enable plugin scripts in all pages. This option fixes issues in Quick Views.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_force_scripts',
					'default' => 'no'
				),
				'ivpa_use_caching' => array(
					'name' => __( 'Use Caching', 'ivpawoo' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to use IVPA product cache for better performance.', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_use_caching',
					'default' => 'no'
				),
				'section_advanced_end' => array(
					'type' => 'sectionend'
				),
				'section_register_title' => array(
					'name' => __( 'Register and Automatic Updates', 'ivpawoo' ),
					'type' => 'title',
					'desc' => __( 'Register your plugin with the purchase code you have got from Codecanyon.net! Get automatic updates!', 'ivpawoo' )
				),
				'ivpa_purchase_code' => array(
					'name' => __( 'Register Improved Variable Product Attributes', 'ivpawoo' ),
					'type' => 'text',
					'desc' => __( 'Enter your purchase code to get instant updated even before the codecanyon.net releases!', 'ivpawoo' ),
					'id'   => 'wc_settings_ivpa_purchase_code',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'section_register_end' => array(
					'type' => 'sectionend'
				),
			);

			if ( $action == 'update' && WC_Improved_Variable_Product_Attributes::$settings['wc_settings_ivpa_use_caching'] == 'yes' ) {
				global $wpdb;
				$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE meta.meta_key LIKE '_ivpa_cached_%';" );
			}

			return apply_filters( 'wc_ivpa_settings', $settings );

		}

		public static function ivpa_get_fields() {
			$attributes = WC_Improved_Variable_Product_Attributes::ivpa_get_attributes();

			$html = '';

			$html .= '<label><span>' . __( 'Select Attribute', 'ivpawoo' ) . '</span> <select class="ivpa_attr_select ivpa_s_attribute" name="ivpa_attr[%%]">';

			$html .= '<option value="">' . __('Select Attribute', 'ivpawoo') . '</option>';

			foreach ( $attributes as $k => $v ) {
				if ( wp_count_terms( $v, array( 'hide_empty' => false ) ) < 1 ) {
					continue;
				}

				$curr_label = wc_attribute_label( $v );
				$html .= '<option value="' . $v . '">' . $curr_label . '</option>';
			}

			$html .= '</select></label>';

			$html .= '<label><span>' . __( 'Override Attribute Name', 'ivpawoo' ) . '</span> <input type="text" name="ivpa_title[%%]" /></label>';

			$html .= '<label><span>' . __( 'Add Attribute Description' ,'ivpawoo' ) . '</span> <textarea name="ivpa_desc[%%]"></textarea></label>';

			$html .= '<label><span>' . __( 'Select Attribute Style', 'ivpawoo' ) . '</span> <select class="ivpa_attr_select ivpa_s_style" name="ivpa_style[%%]">';

			$styles = array(
				'ivpa_text' => __( 'Plain Text', 'ivpawoo' ),
				'ivpa_color' => __( 'Color', 'ivpawoo' ),
				'ivpa_image' => __( 'Thumbnail', 'ivpawoo' ),
				'ivpa_selectbox' => __( 'Select Box', 'ivpawoo' ),
				'ivpa_html' => __( 'HTML', 'ivpawoo' )
			);

			$c=0;
			foreach ( $styles as $k => $v ) {
				$html .= '<option value="' . $k . '" ' . ($c==0?' selected="selected"':'') . '>' . $v . '</option>';
				$c++;
			}

			$html .= '</select></label>';

			$html .= '<label><input type="checkbox" name="ivpa_archive_include[%%]" checked="checked" /> <span class="ivpa_checkbox_desc">' . __( 'Show on Shop/Archives (This only works if the Shop/Archive mode is set to Show Only)', 'ivpawoo' ) . '</span></label>';

			$html .= '<div class="ivpa_terms">';

			foreach ( $attributes as $v ) {

			}

			$html .= '</div>';

			die($html);
			exit;

		}

		public static function ivpa_get_terms() {

			$curr_tax = ( isset($_POST['taxonomy']) ? $_POST['taxonomy'] : '' );
			$curr_style = ( isset($_POST['style']) ? $_POST['style'] : '' );

			if ( $curr_tax == '' || $curr_style == '' ) {
				die();
				exit;
			}

			$catalog_attrs = get_terms( $curr_tax, array( 'hide_empty' => false ) );

			if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){

				ob_start();

				switch ( $curr_style ) {

					case 'ivpa_text' :

						?>
							<div class="ivpa_term_style">
								<span class="ivpa_option">
									<?php _e('CSS', 'ivpawoo'); ?>
									<select name="ivpa_term[%%][style]">
								<?php
									$styles = array(
										'ivpa_border' => __( 'Border', 'ivpawoo' ),
										'ivpa_background' => __( 'Background', 'ivpawoo' ),
										'ivpa_round' => __( 'Round', 'ivpawoo' )
									);

									$c=0;
									foreach ( $styles as $k => $v ) {
								?>
										<option value="<?php echo $k; ?>"<?php echo ($c==0?' selected="selected"':''); ?>><?php echo $v; ?></option>
								<?php
										$c++;
									}
								?>
									</select>
								</span>
								<span class="ivpa_option">
									<?php _e('Normal', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[%%][normal]" value="#bbbbbb"/>
								</span>
								<span class="ivpa_option">
									<?php _e('Active', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[%%][active]" value="#333333"/>
								</span>
								<span class="ivpa_option">
									<?php _e('Disabled', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[%%][disabled]" value="#e45050"/>
								</span>
								<span class="ivpa_option">
									<?php _e('Out of stock', 'ivpawoo'); ?> <input class="ivpa_color" type="text" name="ivpa_term[%%][outofstock]" value="#e45050"/>
								</span>

							</div>
						<?php

							foreach ( $catalog_attrs as $term ) {

							?>
								<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
									<span class="ivpa_option ivpa_option_plaintext">
										<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[%%][<?php echo $term->slug; ?>]""/>
									</span>
								</div>
							<?php
							}

					break;


					case 'ivpa_color' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
								<span class="ivpa_option ivpa_option_color">
									<em><?php echo $term->name . ' ' . __('Color', 'ivpawoo'); ?></em> <input class="ivpa_color" type="text" name="ivpa_term[%%][<?php echo $term->slug; ?>]" value="#cccccc" />
								</span>
								<span class="ivpa_option">
									<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[%%][<?php echo $term->slug; ?>]""/>
								</span>
							</div>
						<?php
						}

					break;


					case 'ivpa_image' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
								<span class="ivpa_option">
									<em><?php echo $term->name . ' ' . __('Image URL', 'ivpawoo'); ?></em> <input type="text" name="ivpa_term[%%][<?php echo $term->slug; ?>]" />
								</span>
								<span class="ivpa_option ivpa_option_button">
									<em><?php _e( 'Add/Upload image', 'ivpawoo' ); ?></em> <a href="#" class="ivpa_upload_media button"><?php _e('Image Gallery', 'ivpawoo'); ?></a>
								</span>
								<span class="ivpa_option">
									<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[%%][<?php echo $term->slug; ?>]""/>
								</span>
							</div>
						<?php
						}

					break;


					case 'ivpa_html' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="ivpa_term" data-term="<?php echo $term->slug; ?>">
								<span class="ivpa_option ivpa_option_text">
									<em><?php echo $term->name . ' ' . __('HTML', 'ivpawoo'); ?></em> <textarea type="text" name="ivpa_term[%%][<?php echo $term->slug; ?>]"></textarea>
								</span>
								<span class="ivpa_option">
									<em><?php echo $term->name . ' ' . __('Tooltip', 'ivpawoo'); ?></em> <input type="text" name="ivpa_tooltip[%%][<?php echo $term->slug; ?>]""/>
								</span>
							</div>
						<?php
						}

					break;

					case 'ivpa_selectbox' :
					?>
						<div class="ivpa_selectbox"><i class="ivpa-warning"></i> <span><?php _e( 'This style has no extra settings!', 'ivpawoo' ); ?></span></div>
					<?php
					break;

					default :
					break;

				}

				$html = ob_get_clean();

				die($html);
				exit;

			}
			else {
				die();
				exit;
			}

		}

	}

	add_action( 'init', 'WC_Ivpa_Settings::init');

?>