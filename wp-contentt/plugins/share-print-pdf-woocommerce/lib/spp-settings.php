<?php

	/*
	 * Share, Print and PDF Settings
	 */
	class WC_Spp_Settings {

		public static function init() {

			if ( !class_exists( 'WooCommerce' ) ) {
				return false;
			}

			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
			add_action( 'woocommerce_settings_tabs_share_print_pdf', __CLASS__ . '::settings_tab' );
			add_action( 'woocommerce_update_options_share_print_pdf', __CLASS__ . '::save_settings' );

		}

		public static function add_settings_tab( $settings_tabs ) {
			$settings_tabs['share_print_pdf'] = __( 'Share, Print, PDF', 'wcsppdf' );
			return $settings_tabs;
		}

		public static function settings_tab() {
			woocommerce_admin_fields( self::get_settings() );
		}

		public static function save_settings() {
			woocommerce_update_options( self::get_settings() );
		}

		public static function get_settings() {

			$settings = array(
				'spp_settings_title' => array(
					'name'     => __( 'Share, Print and PDF - Settings', 'wcsppdf' ),
					'type'     => 'title',
					'desc'     => __( 'General settings for Share, Print and PDF for WooCommerce plugin.', 'wcsppdf' ) . ' Get more awesome plugins by <a href="http://mihajlovicnenad.com" target="_blank">http://mihajlovicnenad.com</a> here <a href="http://bit.ly/1IreccI" target="_blank">http://bit.ly/1IreccI</a>!',
					'id'       => 'spp_settings_title'
				),
				'spp_enable' => array(
					'name' => __( 'Share, Print and PDF Installation', 'wcsppdf' ),
					'type' => 'select',
					'desc' => __( 'Select method for installing the Share, Print and PDF template in your Shop.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_enable',
					'options' => array(
						'override' => __( 'Override Default WooCommerce Templates', 'wcsppdf' ),
						'action' => __( 'Custom Action', 'wcsppdf' )
					),
					'default' => 'yes',
					'css' => 'width:300px;margin-right:12px;'
				),
				'spp_action' => array(
					'name' => __( 'Share, Print and PDF Init Action', 'wcsppdf' ),
					'type' => 'text',
					'desc' => __( 'Enter custom Share, Print and PDF action to initiate the plugin template. This option is used if the Share, Print and PDF Installation is set to Custom Action option. Use actions from your theme content-single-product.php template. Please enter action name in following format action_name:priority. Priority is not required. E.G. woocommerce_single_product_summary:60', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_action',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'spp_logo' => array(
					'name' => __( 'Site Logo', 'wfsm' ),
					'type' => 'text',
					'desc' => __( 'Use site logo on print and PDF templates. Paste in the logo URL.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_logo',
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'spp_style' => array(
					'name' => __( 'Button Style', 'wcsppdf' ),
					'type' => 'select',
					'desc' => __( 'Choose button style.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_style',
					'options' => array(
						'line-icons' => __( 'Line Icons', 'wcsppdf' ),
						'background-colors' => __( 'Background Colors', 'wcsppdf' ),
						'border-colors' => __( 'Border Colors', 'wcsppdf' ),
						'flat' => __( 'Flat', 'wcsppdf' )
						
					),
					'default' => 'yes',
					'css' => 'width:300px;margin-right:12px;'
				),
				'spp_counts' => array(
					'name' => __( 'Show Counts', 'prdctfltr' ),
					'type' => 'checkbox',
					'desc' => __( 'Check this option to show share counts where possible.', 'prdctfltr' ),
					'id'   => 'wc_settings_spp_counts',
					'default' => 'no'
				),
				'spp_shares' => array(
					'name' => __( 'Hide Shares', 'wcsppdf' ),
					'type' => 'multiselect',
					'desc' => __( 'Select shares to hide on your webiste.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_shares',
					'options' => array(
						'facebook' => __( 'Facebook', 'wcsppdf' ),
						'twitter' => __( 'Twitter', 'wcsppdf' ),
						'google' => __( 'Google', 'wcsppdf' ),
						'pin' => __( 'Pinterest', 'wcsppdf' ),
						'linked' => __( 'LinkedIn', 'wcsppdf' ),
						'delicious' => __( 'Delicious', 'wcsppdf' ),
						'print' => __( 'Print', 'wcsppdf' ),
						'pdf' => __( 'PDF', 'wcsppdf' )

					),
					'default' => '',
					'css' => 'width:300px;margin-right:12px;'
				),
				'spp_header_after' => array(
					'name' => __( 'PDF and Print Header After', 'wfsm' ),
					'type' => 'textarea',
					'desc' => __( 'Set custom content after header in print and PDF mode.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_header_after',
					'default' => '',
					'css' => 'width:600px;height:200px;margin-right:12px;'
				),
				'spp_product_before' => array(
					'name' => __( 'PDF and Print Product Before', 'wfsm' ),
					'type' => 'textarea',
					'desc' => __( 'Set custom content before product content in print and PDF mode.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_product_before',
					'default' => '',
					'css' => 'width:600px;height:200px;margin-right:12px;'
				),
				'spp_product_after' => array(
					'name' => __( 'PDF and Print Product After', 'wfsm' ),
					'type' => 'textarea',
					'desc' => __( 'Set custom content after product content in print and PDF mode.', 'wcsppdf' ),
					'id'   => 'wc_settings_spp_product_after',
					'default' => '',
					'css' => 'width:600px;height:200px;margin-right:12px;'
				),
				'spp_settings_end' => array(
					'type' => 'sectionend',
					'id' => 'spp_settings_end'
				)
			);

			return apply_filters( 'wc_shareprintpdf_settings', $settings );
		}

	}

	add_action( 'init', array( 'WC_Spp_Settings', 'init' ), 999 );

?>