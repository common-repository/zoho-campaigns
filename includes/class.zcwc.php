<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ZohoCampaign {

	private static $initiated = false;

	public static function zcwc_checkout_field_update_order_meta( $order_id ) {
	  $current_user = wp_get_current_user();
	   if($current_user != null)
	   {
        if(isset($_POST['zc_optin_checkbox']) && get_user_meta($current_user->ID, 'zcwc_newsletter_subscription', true) == '')
         {
            update_user_meta( $current_user->ID, 'zcwc_newsletter_subscription', true );
         }
           else {
               update_user_meta( $current_user->ID, 'zcwc_newsletter_subscription', false );
           }
	   }
	 }
	public static function zcwc_init() {
		if (!self::$initiated) {
			self::zcwc_init_hooks();
		}
	}

	public static function zcwc_init_hooks() {
		self::$initiated = true;
		add_shortcode( 'zcwp', array('ZohoCampaign', 'zcwc_form_sc') );
		add_action('wp_enqueue_scripts', array('ZohoCampaign','zcwc_find_footer_tracking_codes') );
		//add_action('zcwc_refresh_forms_event', array('ZohoCampaign','zcwc_refresh_forms_event_hook'),10);
		add_action('zcwc_track_order_event_hook', array('ZohoCampaign','zcwc_track_order_event_action'),10,2);
		/**
		 * Add opt-in checkbox
		 **/
		$hook = ZohoCampaign::zcwc_get_parsed_val('zcwc_optin_setting', 'hook');
		add_action( $hook , array('ZohoCampaign','zcwc_custom_checkout_field'), 10, 1);
		add_action('woocommerce_register_form', array('ZohoCampaign','zcwc_custom_registration_field'), 10);
		add_action('woocommerce_created_customer', array('ZohoCampaign','zcwc_checkout_field_update_order_meta') );
        	add_action('woocommerce_checkout_order_processed', array('ZohoCampaign','zcwc_checkout_field_update_order_meta') );
		/**
		 * Update the order meta with field value
		 **/
		add_action('woocommerce_after_cart_totals',array('ZohoCampaign','zcwc_checkout_started'));
		add_action('woocommerce_before_checkout_billing_form',array('ZohoCampaign','zcwc_checkout_started'));
		add_action('woocommerce_cart_item_removed',array('ZohoCampaign','zcwc_checkout_started'));
		add_action('woocommerce_checkout_order_processed',array('ZohoCampaign','zcwc_order_placed'),10,1);
	}

	public static function zcwc_form_sc($attr) {
	  if( isset( $attr['id'] ) ) {
	    $sc_id = $attr['id'] ;
			if (filter_var($sc_id, FILTER_VALIDATE_INT)!== false) {
	    	return self::zcwc_form_post($sc_id);
			}
			else {
				return false;
			}
		}
	}

	public static function zcwc_find_footer_tracking_codes() {
	   if(!is_admin()){
	   		echo "<script>
					(function(){
						var zccmpurl = new URL(document.location.href);
						var cmp_id =  zccmpurl.search.split('zc_rid=')[1];
						if (cmp_id != undefined) {
							document.cookie = 'zc_rid=' + cmp_id + ';max-age=10800;path=/';
						}
					})();
				</script>";
	    }
    }

	public static function zcwc_plugin_activation()
	{
		ZohoCampaign_Admin::zcwc_create_mhforms_table();
		//wp_schedule_event( time(), 'daily','zcwc_refresh_forms_event' );
	}

	public static function zcwc_plugin_deactivation()
	{
		//wp_clear_scheduled_hook( 'zcwc_refresh_forms_event' );
		wp_clear_scheduled_hook( 'zcwc_track_order_event_hook' );
		$zcwc_domname = 'com';
		if(get_option('zcwc_domname'))
		{
			$zcwc_domname = get_option('zcwc_domname');
		}
	    $headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zcwc_get_parsed_val('zcwc_token_details','access_token') );
    	$query_string = http_build_query(['integrationIdDigest' => self::zcwc_get_parsed_val('zcwc_intergration_details','integration_digest')]);
		$url= ZC4WP__CAMPAIGN_URL. $zcwc_domname . '/api/v2/woocommerce/deny?' . $query_string;
		$response = wp_remote_request( $url, array(
	    'method'      => 'POST',
	    'headers'     => $headarray,
	    ) );
	}

	public static function zcwc_form_post($id)
	{
		global $wpdb, $table_prefix;
	    $tblname = $table_prefix . 'zcwc_forms';
		$zmh_form = $wpdb->get_row($wpdb->prepare( "SELECT * FROM {$tblname} WHERE id = %d AND status = %d",array($id, 2)),ARRAY_A);
		if($zmh_form){
			$response = wp_remote_get($zmh_form['url']);
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 || isset($response_body['error']) )
				return false;
			else if(strpos($response['body'], 'signupFormContainer') != false)
			{
				if($zmh_form['form_type'] == 'Long Form')
					return str_replace("absolute", "" , str_replace("fixed", "", $response['body']));
				else
					return $response['body'];
			}
			else return false;
		}
	}

	public static function zcwc_get_product($product_id){
        if(function_exists('wc_get_product')){
            return wc_get_product($product_id);
        }else{
            return get_product($product_id);
        }
    }

	public static function zcwc_checkout_started()
	{
		global $woocommerce;
		$woo = function_exists('WC') ? WC() : $woocommerce;
	  	$current_user = wp_get_current_user();
		if (is_user_logged_in())
		{
	    	$user_id = $current_user->ID;
	    	$items = $woo->cart->get_cart();
	    	$product_arr = array();
	        foreach($items as $key ) {
	        	$product = $key['data'];
	            array_push($product_arr,$product->get_id());
	        }
	        $a = array('id' => $user_id, 'user_email' => $current_user->user_email, 'total_price' => $woo->cart->total, 'checkout_url' => wc_get_checkout_url(), 'line_items' => $product_arr,'currency' => get_woocommerce_currency());
			$zcwc_domname = 'com';
			$id="";
			$od="";
			if(get_option('zcwc_domname'))
			{
				$zcwc_domname = get_option('zcwc_domname');
			}
			if(get_option('zcwc_intergration_details')!=null)
			{
				$mh_Object = unserialize(get_option('zcwc_intergration_details'));
				if(isset($mh_Object['hook_digest']))
				{
				 	$id = $mh_Object['hook_digest'];
				}
				if(isset($mh_Object['org_digest']))
				{
				 	$od = $mh_Object['org_digest'];
				}
			}
	        $zcwc_cart_action = 'cart.updated';
	        if(empty($product_arr))
	        {
	        	$zcwc_cart_action = 'cart.deleted';
	        }
	    	$headarray = array('Content-type' => 'application/json', 'x-wc-webhook-topic' => $zcwc_cart_action ,'x-wc-webhook-referer' => 'zoho campaign plugin','x-zohocampaign-plugin-version' => ZC4WP_VERSION);
	    	$query_string = http_build_query(['id' => $id, 'od' => $od]);
			$url= ZC4WP__CAMPAIGN_URL. $zcwc_domname . '/ua/ecommercecallback.zc?' . $query_string;
			$response = wp_remote_request( $url, array(
		    'method'      => 'POST',
		    'body'        => wp_json_encode($a,true),
		    'headers'     => $headarray,
		    'data_format' => 'body',
		    ) );
	    }
	}

	public static function zcwc_get_parsed_val($key,$value) {
		 $mh_Object = unserialize(get_option($key));
		 if(isset($mh_Object[$value]))
		 {
		 	return $mh_Object[$value];
		 }
		 else return false;
	}
	public static function zcwc_custom_checkout_field($checkout) {
			$checked = self::zcwc_get_parsed_val('zcwc_optin_setting', 'check');
		    if ($checked == 'hidden') {
		      return;
		    }
		    $default_checked = $checked == 'checked';
		    $label = self::zcwc_get_parsed_val('zcwc_optin_setting', 'label');
		    global $woocommerce;
			$woo = function_exists('WC') ? WC() : $woocommerce;
			if (is_user_logged_in() && get_option("zcwc_integration") != null && intval(get_option("zcwc_integration")) == 3) {
			    $current_user = wp_get_current_user();
		        $status = get_user_meta($current_user->ID, 'zcwc_newsletter_subscription', true);
		        if ((bool) $status) {
		            $default_checked = true;
		        }
		        	echo '<style> #zc-optin-field span.optional {display:none;}</style>';
		        	echo '<div id="zc-optin-field">';
					woocommerce_form_field( 'zc_optin_checkbox', array(
					'type'  => 'checkbox',
					'label' => $label,
			        ), $default_checked);
			   		 echo '</div>';
 			}
 			else if(WC()->checkout()->is_registration_enabled())
 			{
 				echo '<style> #zc-optin-field span.optional {display:none;}</style>';
 				echo '<div id="zc-optin-field">';
					woocommerce_form_field( 'zc_optin_checkbox', array(
					'type'  => 'checkbox',
					'label' => $label,
			        ), $default_checked);
			   		 echo '</div>';
 			}
	}
	public static function zcwc_custom_registration_field() {
			if(get_option("zcwc_integration") != null && intval(get_option("zcwc_integration")) == 3) {
		    $checked = self::zcwc_get_parsed_val('zcwc_optin_setting', 'check');
		    $default_checked = $checked == 'checked';
		    $label = self::zcwc_get_parsed_val('zcwc_optin_setting', 'label');
		    if ($checked == 'hidden') {
		      return;
		    }
	        else
	        {
	        	echo '<style> #zc-optin-field span.optional{display:none;}</style>';
	        	echo '<div id="zc-optin-field">';
				woocommerce_form_field( 'zc_optin_checkbox', array(
				'type'  => 'checkbox',
				'label' => $label,
		        ), $default_checked);
		   		 echo '</div>';
	        }
 		}
	}
	public static function zcwc_track_order_event_action($order_id,$recurrence,$zc_rid)
	{
		$logger = new WC_Logger();
        $logger->add('zcwc_track_order_event_action_logger_order_id', $order_id);
		$zcwc_domname = 'com';
		if(get_option('zcwc_domname'))
		{
			$zcwc_domname = get_option('zcwc_domname');
		}
	   	if($recurrence < 2)
	   	{
	    	$query_string = http_build_query(['service' => 'WooCommerce', 'zc_rid' => $zc_rid ,'order_id' => $order_id]);
			$url= ZC4WP__CAMPAIGN_URL. $zcwc_domname . '/ua/ecommercetracking.zc?' . $query_string;
			$logger->add('zcwc_track_order_event_action_url', $url);
			$response = wp_remote_get($url);
		    $logger->add('zcwc_track_order_event_action', $response);
		    if( wp_remote_retrieve_response_code( $response ) != 200 && $recurrence < 1) {
		    	wp_schedule_single_event( time() + 720, 'zcwc_track_order_event_hook' , array($order_id,$recurrence + 1,$zc_rid));
		    }
		}
	}
	public static function zcwc_order_placed($order_id)
	{
		if(isset($_COOKIE["zc_rid"]) && $order_id){
	 		$zcwc_domname = 'com';
			if(get_option('zcwc_domname'))
			{
				$zcwc_domname = get_option('zcwc_domname');
			}
			$query_string = http_build_query(['service' => 'WooCommerce', 'zc_rid' => $_COOKIE["zc_rid"] ,'order_id' => $order_id]);
			$url= ZC4WP__CAMPAIGN_URL. $zcwc_domname . '/ua/ecommercetracking.zc?' . $query_string;
			$response = wp_remote_get($url);
		    if( wp_remote_retrieve_response_code( $response ) != 200 ) {
		    	wp_schedule_single_event( time() + 120, 'zcwc_track_order_event_hook' , array($order_id, 0, $_COOKIE["zc_rid"]));
		    }
		}
	}
}
?>
