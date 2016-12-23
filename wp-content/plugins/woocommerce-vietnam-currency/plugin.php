<?php
/**
 * Plugin Name: Woocommerce Vietnam Currency
 * Plugin URI: http://thachpham.com
 * Description: Thêm loại tiền tệ Việt Nam Đồng (VNĐ) vào Woocommerce và tích hợp tính năng tự chuyển tỷ giá VNĐ sang USD để sử dụng thanh toán qua Paypal.
 * Version: 1.3
 * Author: Thach Pham
 * Author URI: http://thachpham.com
 * License: GPL2
 */

include ('wc_vn_currency_options.php'); // Add the options of plugin.

/**
 * Add Vietnam provinces and cities.
 */
add_filter( 'woocommerce_states', 'vietnam_cities_woocommerce' );
function vietnam_cities_woocommerce( $states ) {
  $states['VN'] = array(
    'CANTHO' => __('Cần Thơ', 'woocommerce') ,
    'HOCHIMINH' => __('Hồ Chí Minh', 'woocommerce') ,
    'HANOI' => __('Hà Nội', 'woocommerce') ,
    'HAIPHONG' => __('Hải Phòng', 'woocommerce') ,
    'DANANG' => __('Đà Nẵng', 'woocommerce') ,
    'ANGIANG' => __('An Giang', 'woocommerce') ,
    'BARIAVUNGTAU' => __('Bà Rịa - Vũng Tàu', 'woocommerce') ,
    'BACLIEU' => __('Bạc Liêu', 'woocommerce') ,
    'BACKAN' => __('Bắc Kạn', 'woocommerce') ,
    'BACNINH' => __('Bắc Ninh', 'woocommerce') ,
    'BACGIANG' => __('Bắc Giang', 'woocommerce') ,
    'BENTRE' => __('Bến Tre', 'woocommerce') ,
    'BINHDUONG' => __('Bình Dương', 'woocommerce') ,
    'BINHDINH' => __('Bình Định', 'woocommerce') ,
    'BINHPHUOC' => __('Bình Phước', 'woocommerce') ,
    'BINHTHUAN' => __('Bình Thuận', 'woocommerce'),
    'CAMAU' => __('Cà Mau', 'woocommerce'),
    'DAKLAK' => __('Đak Lak', 'woocommerce'),
    'DAKNONG' => __('Đak Nông', 'woocommerce'),
    'DIENBIEN' => __('Điện Biên', 'woocommerce'),
    'DONGNAI' => __('Đồng Nai', 'woocommerce'),
    'GIALAI' => __('Gia Lai', 'woocommerce'),
    'HAGIANG' => __('Hà Giang', 'woocommerce'),
    'HANAM' => __('Hà Nam', 'woocommerce'),
    'HATINH' => __('Hà Tĩnh', 'woocommerce'),
    'HAIDUONG' => __('Hải Dương', 'woocommerce'),
    'HAUGIANG' => __('Hậu Giang', 'woocommerce'),
    'HOABINH' => __('Hòa Bình', 'woocommerce'),
    'HUNGYEN' => __('Hưng Yên', 'woocommerce'),
    'KHANHHOA' => __('Khánh Hòa', 'woocommerce'),
    'KIENGIANG' => __('Kiên Giang', 'woocommerce'),
    'KOMTUM' => __('Kom Tum', 'woocommerce'),
    'LAICHAU' => __('Lai Châu', 'woocommerce'),
    'LAMDONG' => __('Lâm Đồng', 'woocommerce'),
    'LANGSON' => __('Lạng Sơn', 'woocommerce'),
    'LAOCAI' => __('Lào Cai', 'woocommerce'),
    'LONGAN' => __('Long An', 'woocommerce'),
    'NAMDINH' => __('Nam Định', 'woocommerce'),
    'NGHEAN' => __('Nghệ An', 'woocommerce'),
    'NINHBINH' => __('Ninh Bình', 'woocommerce'),
    'NINHTHUAN' => __('Ninh Thuận', 'woocommerce'),
    'PHUTHO' => __('Phú Thọ', 'woocommerce'),
    'PHUYEN' => __('Phú Yên', 'woocommerce'),
    'QUANGBINH' => __('Quảng Bình', 'woocommerce'),
    'QUANGNAM' => __('Quảng Nam', 'woocommerce'),
    'QUANGNGAI' => __('Quảng Ngãi', 'woocommerce'),
    'QUANGNINH' => __('Quảng Ninh', 'woocommerce'),
    'QUANGTRI' => __('Quảng Trị', 'woocommerce'),
    'SOCTRANG' => __('Sóc Trăng', 'woocommerce'),
    'SONLA' => __('Sơn La', 'woocommerce'),
    'TAYNINH' => __('Tây Ninh', 'woocommerce'),
    'THAIBINH' => __('Thái Bình', 'woocommerce'),
    'THAINGUYEN' => __('Thái Nguyên', 'woocommerce'),
    'THANHHOA' => __('Thanh Hóa', 'woocommerce'),
    'THUATHIENHUE' => __('Thừa Thiên - Huế', 'woocommerce'),
    'TIENGIANG' => __('Tiền Giang', 'woocommerce'),
    'TRAVINH' => __('Trà Vinh', 'woocommerce'),
    'TUYENQUANG' => __('Tuyên Quang', 'woocommerce'),
    'VINHLONG' => __('Vĩnh Long', 'woocommerce'),
    'VINHPHUC' => __('Vĩnh Phúc', 'woocommerce'),
    'YENBAI' => __('Yên Bái', 'woocommerce'),
  );
 
  return $states;
}

/**
* Add Vietnam currency (VND)
*/
add_filter( 'woocommerce_currencies', 'add_vnd_currency' );
function add_vnd_currency( $currencies ) {
 $currencies['VND'] = __( 'Việt Nam Đồng', 'woocommerce' );
 return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vnd_currency_symbol', 10, 2);
function add_vnd_currency_symbol( $currency_symbol, $currency ) {
 switch( $currency ) {
 case 'VND': $currency_symbol = 'VNĐ'; break;
 }
 return $currency_symbol;
}


/**
* Convert VND to USD to use PayPal.
*/
add_filter('woocommerce_paypal_args', 'vnd_to_usd'); 
function vnd_to_usd($paypal_args){ 
	if ( $paypal_args['currency_code'] == 'VND'){
		$convert_rate = (get_option('vnd_convert_rate') == '') ? 21083.7 : get_option('vnd_convert_rate');
		$paypal_args['currency_code'] = 'USD'; // Ký hiệu của loại tiền cần chuyển ra.
		$i = 1; 

		while (isset($paypal_args['amount_' . $i])) { 
			$paypal_args['amount_' . $i] = round( $paypal_args['amount_' . $i] / $convert_rate, 2); 
			++$i; 
		}
		
		/* Fix VND for coupon usage. Thanks @Pham Duy Thanh 
		 */
		if(isset($paypal_args['discount_amount_cart']) && $paypal_args['discount_amount_cart'] > 0){

			$paypal_args['discount_amount_cart'] = round( $paypal_args['discount_amount_cart'] / $convert_rate, 2);

		}		

	} 
	return $paypal_args; 
}

/*
 * Fix 

/* Enable VND for PayPal */
add_filter( 'woocommerce_paypal_supported_currencies', 'add_bgn_paypal_valid_currency' );     
    function add_bgn_paypal_valid_currency( $currencies ) {  
    array_push ( $currencies , 'VND' );
    return $currencies;  
} 

