<?php
ob_start();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'ZC4WP__CLIENT_ID', '1000.FHN7CDW7YTSV62725E8VMO2AQ66TYH' );
define( 'ZC4WP__CLIENT_SECRET_COM', '939da91118ca937829901b04971c4755363bb9a529' );
define( 'ZC4WP__CLIENT_SECRET_EU', 'cee48631d42e36150129c7014838a12183db194b90' );
define( 'ZC4WP__CLIENT_SECRET_IN', '25c30240dea1bdc0f8b5fecb272c01f8a43fe0e29b' );
define( 'ZC4WP__CLIENT_SECRET_AU', '738ce3404a5e1f36078555f7a776b152fddc484f18' );
define( 'ZC4WP__CLIENT_SECRET_JP', '9a46bf511b6c45bb629251afe00b4748d7b416514d' );
define( 'ZC4WP__CLIENT_SECRET_CA', 'd6570ebc2d2a82c5453849f49279c9d2a04f02b078');

class ZohoCampaign_Admin {

	const DELETED = 0;
	const NOT_USED = 1;
	const VISIBLE = 2;
	const INVISIBLE = 3;

	private static $initiated = false;

	public static function zcwc_init() {
		if (!self::$initiated) {
			self::zcwc_init_hooks();
		}
	}

	public static function zcwc_init_hooks() {
		self::$initiated = true;

		add_action( 'admin_menu', array( 'ZohoCampaign_Admin', 'zcwc_admin_menu' ));
		add_action( 'admin_enqueue_scripts', array( 'ZohoCampaign_Admin', 'zcwc_load_resources' ) );

		// Admin-Post
		add_action( 'current_screen', array( 'ZohoCampaign_Admin', 'zcwc_admin_texts' ) );

		// Ajax-Post
		add_action( 'wp_ajax_zcwc_connect', array( 'ZohoCampaign_Admin', 'zcwc_connect' ));
		add_action( 'wp_ajax_zcwc_disconnect', array( 'ZohoCampaign_Admin', 'zcwc_disconnect' ));
		add_action( 'wp_ajax_zcwc_fetch_form', array( 'ZohoCampaign_Admin', 'zcwc_fetch_form' ));
		add_action( 'wp_ajax_zcwc_change_form_status', array( 'ZohoCampaign_Admin', 'zcwc_change_form_status' ) );
		add_action( 'wp_ajax_zcwc_refresh_forms_list', array( 'ZohoCampaign_Admin', 'zcwc_refresh_forms_list' ) );
		add_action( 'wp_ajax_zcwc_get_short_code', array( 'ZohoCampaign_Admin', 'zcwc_get_short_code' ) );
		add_action( 'wp_ajax_zoho_campaign_rated', array( 'ZohoCampaign_Admin', 'zoho_campaign_rated' ) );
		add_action( 'wp_ajax_zcwc_woocommerce_authorize', array( 'ZohoCampaign_Admin', 'zcwc_woocommerce_authorize' ));
		add_action( 'wp_ajax_zcwc_add_list', array( 'ZohoCampaign_Admin', 'zcwc_add_list' ));
		add_action( 'wp_ajax_zcwc_integration_status', array( 'ZohoCampaign_Admin', 'zcwc_integration_status' ));
		add_action( 'wp_ajax_zcwc_get_list', array( 'ZohoCampaign_Admin', 'zcwc_integration_status' ));
		add_action( 'wp_ajax_zcwc_integration_disconnect', array( 'ZohoCampaign_Admin', 'zcwc_integration_disconnect' ));
		add_action( 'wp_ajax_zcwc_optin_save', array( 'ZohoCampaign_Admin', 'zcwc_optin_save' ));
	}

	public static function zcwc_admin_texts() {
		add_filter( 'admin_footer_text', array( 'ZohoCampaign_Admin', 'zcwc_footer_text' ) );
	}

	public static function zcwc_footer_text( $text ) {
		if ( ! current_user_can( 'manage_options' ) || ! get_option('zcwc_connect_time') ) {
			return $text;
		}
		if(! empty( $_GET['page'] ) &&  (strpos( $_GET['page'], 'zc-start' )  === 0 || strpos( $_GET['page'], 'zc-wc' )  === 0 || strpos( $_GET['page'], 'zc-forms' ) === 0  ) )    {

			if(!get_option("zcwc_rated"))
			{
				$text = sprintf( 'If you enjoy using <strong>Zoho Campaigns</strong>, please <a href="%s" target="_blank" class="zmhub-rating-link" >leave us a ★★★★★ rating</a>. A huge thanks in advance!', 'https://wordpress.org/support/plugin/zoho-campaigns/reviews/?rate=5#new-post' );
			}
			else
			 $text = sprintf( 'Thanks for using <a href="%s" target="_blank"> Zoho Campaigns</a>.', 'https://wordpress.org/plugins/zoho-campaigns' );
		}
		return $text;
	}

	public static function zoho_campaign_rated() {
		if(current_user_can('manage_options') && check_ajax_referer( 'mh-ajax-nonce', 'security' ))
		{
			update_option('zcwc_rated',true);
		}
	}

	public static function zcwc_return_constant_val($start,$end)
	{
		 if($start == 'CLIENT_SECRET')
		 {
		 	switch ($end) {
	 			case 'com':
	 			return ZC4WP__CLIENT_SECRET_COM;
	 			break;

	 			case 'eu':
	 			return ZC4WP__CLIENT_SECRET_EU;
	 			break;

	 			case 'in':
	 			return ZC4WP__CLIENT_SECRET_IN;
	 			break;

	 			case 'com.au':
	 			return ZC4WP__CLIENT_SECRET_AU;
	 			break;

				case 'jp':
	 			return ZC4WP__CLIENT_SECRET_JP;
	 			break;

				case 'ca':
				return ZC4WP__CLIENT_SECRET_CA;
				break;
		 	}
		 }
	}

	public static function zcwc_connect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
	   	$response_body ='';
		$auth_url = ZC4WP__ACCOUNTS_URL . "com/oauth/v2/auth?response_type=code&client_id=" . ZC4WP__CLIENT_ID . "&scope=AaaServer.profile.READ,ZohoCampaigns.contact.ALL&redirect_uri=https://campaigns.zoho.com/ua/wpredirect&prompt=consent&access_type=offline&state=" .get_admin_url('') ."admin.php?page=zc-start";
		echo esc_url_raw($auth_url);
	}

	public static function zcwc_disconnect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
	    self::zcwc_remove_account();
	    echo esc_url(get_admin_url('') ."admin.php?page=zc-wc");
	    wp_die();
	}

	public static function zcwc_woocommerce_authorize() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		$response  = self::zcwc_http_request('/api/v2/woocommerce/ping','GET','');
		$response_body = json_decode($response['body'],true);
		if(isset($response_body['code']) && $response_body['code'] == 500)
		{
			echo("1");
		}
		else if((isset($response_body['isUserPresent']) && !$response_body['isUserPresent']) || (isset($response_body['code']) && $response_body['code'] == 901) )
		{
			echo("2");
		}
		else if(isset($response_body['isAdmin']) && $response_body['isAdmin'] )
		{
			$user_id =  $response_body['userid'];
			$store_url = get_site_url();
			$endpoint = '/wc-auth/v1/authorize';
			$zcwc_domname = 'com';
			if(get_option('zcwc_domname'))
			{
				$zcwc_domname = get_option('zcwc_domname');
			}
			$campaign_url = ZC4WP__CAMPAIGN_URL;
			if($zcwc_domname=='ca')	{
					$campaign_url = ZC4WP__CAMPAIGN_URL_CA;
			}
			$params = [
			    'app_name' => 'Zoho Campaigns',
			    'scope' => 'read_write',
			   'user_id' => get_site_url(). '_' .get_bloginfo( 'name' ). '_' .get_woocommerce_currency(). '_GMT_' .$user_id,
			    'return_url' => get_admin_url('') .'admin.php?page=zc-wc',
			    'callback_url' => $campaign_url . $zcwc_domname . '/ua/wcaction'
			];
			$query_string = http_build_query( $params );
			echo esc_url_raw($store_url . $endpoint . '?' . $query_string);
		}
		else if(isset($response_body['isAdmin']) && !$response_body['isAdmin'] )
		{
			echo("4");
		}
		else
		{
			echo("3");
		}
		wp_die();
	}

	public static function zcwc_add_list() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		if(isset($_POST['list_digest']))
		{
			$query_string = http_build_query(['integrationIdDigest' => self::zcwc_get_parsed_val('zcwc_intergration_details','integration_digest'), 'listIdDigest' => $_POST['list_digest'], 'isSubscribed' => $_POST['isSubscribed'], 'storeurl' => get_site_url()]);
			$response = self::zcwc_http_request('/api/v2/woocommerce/authorize?' .$query_string,'POST','');
			$response_body = json_decode($response['body'],true);
			if($response_body['code'] == 200)
			{
				update_option('zcwc_integration', 2);
				//Add default Options
				$zcwc_optin_setting = array('check' => 'unchecked', 'label' => 'Subscribe to our newsletter', 'hook' => 'woocommerce_after_checkout_billing_form');
				if(isset($_POST['isSubscribed']) && $_POST['isSubscribed'] == 'true')
				{
					$wp_user_query = new WP_User_Query(array('role' => 'Customer'));
					$users = $wp_user_query->get_results();
					if (!empty($users)) {
					    foreach ($users as $user)
					    {
					    	if(get_user_meta($user->ID, 'zcwc_newsletter_subscription', true) == '')
					    	{
					    		add_user_meta( $user->id, 'zcwc_newsletter_subscription', true );
					    	}
					    }
					}
				}
				else
				{
					$wp_user_query = new WP_User_Query(array('role' => 'Customer'));
					$users = $wp_user_query->get_results();
					if (!empty($users)) {
					    foreach ($users as $user)
					    {
					      delete_user_meta( $user->id, 'zcwc_newsletter_subscription', true );
					    }
					}
				}
				update_option('zcwc_optin_setting',serialize($zcwc_optin_setting));
				echo esc_url(get_admin_url('') ."admin.php?page=zc-wc");
				exit();
			}
		}
		else
		{
			echo "Integration doesn't exist";
		}
		wp_die();
	}

	public static function zcwc_integration_status()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		$query_string = http_build_query(['integrationIdDigest' => self::zcwc_get_parsed_val('zcwc_intergration_details','integration_digest')]);
			$response = self::zcwc_http_request('/api/v2/woocommerce/status?' .$query_string,'GET','');
			if( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$response_body = json_decode($response['body'],true);
				if($response_body['code'] == 200)
				{
					$new_response = self::zcwc_http_request('/api/v2/woocommerce/storestats?' .$query_string,'GET','');
					$new_response_body = json_decode($new_response['body'],true);
					if($new_response_body['account_status'] && $new_response_body['account_status'] == 0 )
					{
						self::zcwc_integration_disconnect();
						wp_safe_redirect(get_admin_url('') ."admin.php?page=zc-wc");
	      					exit();
					}
					update_option('zcwc_store_stats',serialize($new_response_body));
					update_option('zcwc_integration', 3);
					echo esc_url(get_admin_url('') ."admin.php?page=zc-wc");
					exit();
				}
				else
				{
					echo 'error';
					exit();
				}
			}
	}
	public static function zcwc_integration_disconnect() {
		if (!current_user_can('manage_options')) {
			die();
		}
		check_ajax_referer('mh-ajax-nonce', 'security');
		try {
			$query_string = http_build_query(['integrationIdDigest' => self::zcwc_get_parsed_val('zcwc_intergration_details', 'integration_digest')]);
			$response = self::zcwc_http_request('/api/v2/woocommerce/deny?' . $query_string, 'POST', '');
			$response_body = json_decode($response['body'], true);
			$wp_user_query = new WP_User_Query(array('role' => 'Customer'));
			$users = $wp_user_query->get_results();
			if (!empty($users)) {
				foreach ($users as $user) {
					delete_user_meta($user->ID, 'zcwc_newsletter_subscription');
				}
			}
		} catch (Exception $e) {
			// Empty catch block to ignore exceptions
		} finally {
			delete_option('zcwc_store_stats');
			delete_option('zcwc_integration');
			delete_option('zcwc_intergration_details');
			delete_option('zcwc_error_msg');
			delete_option('zcwc_optin_setting');
		}
	}
	public static function zcwc_fetch_form() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		global $wpdb,$table_prefix;
		$table = $table_prefix . 'zcwc_forms';
		if (!in_array($table, $wpdb->tables)) {
		 	//echo "Please Deactivate and Reactivate the plugin."
		 	self::zcwc_create_mhforms_table();
		}
		ZohoCampaign_Admin::zcwc_construct_fetch_zcwc_forms();
        wp_die();
	}


    // Change Form Status
	public static function zcwc_change_form_status() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		global $wpdb;
		$table = $wpdb->prefix . 'zcwc_forms';
	    $id = intval( $_POST['id'] );
			if (filter_var($id, FILTER_VALIDATE_INT)!== false) {
		    $form_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", array($id)));
		    if(!is_null($form_data))
		    {
		    	$form_status = $form_data->status;
		    	if($form_status == self::VISIBLE ||  $form_status == self::INVISIBLE)
		    	{
		    		$form_status = ($form_status == self::INVISIBLE ? self::VISIBLE : self::INVISIBLE);
		    		$res = $wpdb->update( $table, array( 'status' => $form_status ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
		    		echo esc_html("successful");
		    	}
		    	else echo esc_html("unsuccessful");
		    }
		    else
		    {
		    	echo esc_html("unsuccessful");
		    }
		}
		wp_die();
	}

	public static function zcwc_http_request($endpoint,$req_type,$params) {

		$zcwc_domname = 'com';
		if(get_option('zcwc_domname'))
		{
			$zcwc_domname = get_option('zcwc_domname');
		}
		$campaign_url = ZC4WP__CAMPAIGN_URL;
		if($zcwc_domname=='ca')	{
				$campaign_url = ZC4WP__CAMPAIGN_URL_CA;
		}
		$headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zcwc_get_parsed_val('zcwc_token_details','access_token') );
		$auth_url = $campaign_url. $zcwc_domname . $endpoint;
		$response = wp_remote_request( $auth_url, array(
	    'method'      => $req_type,
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $params,
	    'headers'     => $headarray
	    ) );
	    return $response;
	}

	public static function zcwc_insert_record( $zmh_tbl_name, $data, &$allforms ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		global $wpdb;
		if(!empty($allforms) && !array_search($data['id'], $allforms) != false)
		{
			$newdata = array(
          	'form_name' => sanitize_text_field($data['name']),
          	'form_type' => sanitize_text_field($data['type']),
          	'list_name' => sanitize_text_field($data['list_name']),
			);
			$res = $wpdb->update($zmh_tbl_name,$newdata, array( 'form_id' => sanitize_text_field($data['id']) ), array( '%s' ,'%s' , '%s'), array( '%s' ) );
			unset($allforms[array_search(sanitize_text_field($data['id']), $allforms)]);
		}
		else
		{
			$newdata = array(
			'form_id' => sanitize_text_field($data['id']),
          	'form_name' => sanitize_text_field($data['name']),
          	'form_type' => sanitize_text_field($data['type']),
          	'list_name' => sanitize_text_field($data['list_name']),
          	'url' => esc_url_raw($data['url']),
          	'created_time' => sanitize_text_field($data['created_time']),
			);
			$wpdb->insert($zmh_tbl_name,$newdata );
		}
	}

	public static function zcwc_construct_fetch_zcwc_forms()
	{
		self::zcwc_fetch_zcwc_forms();
		wp_die();
	}
	public static function zcwc_search_array($arr,$value)
	{
		$zval = false;
		foreach ($arr as $singleval) {
			if($singleval == $value)
			{
				$zval = true;
			}
		}
		return $zval;
	}
	public static function zcwc_fetch_zcwc_forms() {

		global $wpdb, $table_prefix;
		$tblname = $table_prefix . 'zcwc_forms';
		$allforms = $wpdb->get_col($wpdb->prepare("SELECT form_id FROM {$tblname}"));
		$allNewForms = self::zcwc_fetch_zcwc_forms_update();
		foreach ($allNewForms as $singleform) {
			if(!empty($allforms) && self::zcwc_search_array($allforms,$singleform['id']))
			{
				$newdata = array(
	          	'form_name' => sanitize_text_field($singleform['name']),
	          	'form_type' => sanitize_text_field($singleform['type']),
	          	'list_name' => sanitize_text_field($singleform['list_name']),
				);
				$res = $wpdb->update($tblname,$newdata, array( 'form_id' => sanitize_text_field($singleform['id']) ), array( '%s' ,'%s' , '%s', '%d'), array( '%s' ) );
				unset($allforms[array_search($singleform['id'], $allforms)]);
			}
			else
			{
				$newdata = array(
				'form_id' => sanitize_text_field($singleform['id']),
	          	'form_name' => sanitize_text_field($singleform['name']),
	          	'form_type' => sanitize_text_field($singleform['type']),
	          	'list_name' => sanitize_text_field($singleform['list_name']),
	          	'url' => esc_url_raw($singleform['url']),
	          	'created_time' => intval($singleform['created_time']),
				);
				$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tblname
						( form_id, form_name, form_type, list_name, url, created_time )
						VALUES ( %s, %s, %s, %s, %s, %d )
					",
				        array(
						'form_id' => sanitize_text_field($singleform['id']),
			          	'form_name' => sanitize_text_field($singleform['name']),
			          	'form_type' => sanitize_text_field($singleform['type']),
			          	'list_name' => sanitize_text_field($singleform['list_name']),
			          	'url' => esc_url_raw($singleform['url']),
			          	'created_time' => intval($singleform['created_time']),
						)
				) );
			}
		}
		if(!empty($allforms))
			{
				foreach ($allforms as $singleform) {
					$newdata = array(
			          	'status' => self::DELETED,
					);
					$res = $wpdb->update($tblname,$newdata, array( 'form_id' => sanitize_text_field($singleform['form_id']) ), array('%d'), array( '%s' ) );
				}
			}
	}
	public static function zcwc_fetch_zcwc_forms_update() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$response = self::zcwc_http_request('/api/v2/forms','GET','');
	    $response_body = json_decode($response['body'],true);
	    $allFetchedForms = array();
	    if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if(isset($response_body['code']) && $response_body['code'] == 901)
        {
            echo esc_html("No org exists for the user.");
        }
		else if(isset($response_body['code']) && $response_body['code'] != 200)
		{
			echo esc_html("An internal error occured while processing your request, Please try again in some time.");
		}
		else {
			    if(isset($response_body['count']) && intval($response_body['count']) != 0)
			    {
			    	$totalFormsCount = $response_body['count'];
			    	$from = 1;
			    	while($totalFormsCount>0)
			    	{
			    		$details = wp_json_encode(array("from" => $from, "count" => 25));
						$response = self::zcwc_http_request('/api/v2/forms?details='.$details,'GET','');
						if( wp_remote_retrieve_response_code( $response ) != 200 ) {
							echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
							break;
						}
						else
						{
		    				$response_body = json_decode($response['body'],true);
		    			}
	    				foreach($response_body['forms'] as $singleform) {
					    	$newform = array(
							'id' => sanitize_text_field($singleform['id']),
				          	'name' => sanitize_text_field($singleform['name']),
				          	'type' => sanitize_text_field($singleform['type']),
				          	'list_name' => sanitize_text_field($singleform['list_name']),
				          	'url' => esc_url_raw($singleform['url']),
				          	'created_time' => sanitize_text_field($singleform['created_time']),
							);
							array_push($allFetchedForms, $newform);
						}
						$totalFormsCount -= 26;
						$from += 26;
			    	}
				}
				else if(intval($response_body['count']) == 0)
				{
					echo ("There is no signup form available in your Zoho Campaigns account. To create a new form, log in to Zoho Campaigns -> Contacts -> Signup Forms -> Click Create Form.");
				}
		}
		return $allFetchedForms;
	}

	public static function zcwc_get_short_code()
	{

		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		global $wpdb;
		$table = $wpdb->prefix . 'zcwc_forms';
	    $id = intval( $_POST['id'] );
			if (filter_var($id, FILTER_VALIDATE_INT)!== false) {
		    $form_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", array($id)));
		    if(!is_null($form_data))
		    {
	    		$res = $wpdb->update($table, array('status' => self::VISIBLE ), array( 'id' => $id ), array('%d'), array('%d'));
	    		if($res)
	    			echo esc_html("successful");
	    		else
	    			echo esc_html("unsuccessful");
		    }
		    else
		    {
		    	echo esc_html("unsuccessful");
		    }
		}
		wp_die();
	}

	public static function zcwc_refresh_forms_event_hook()
	{
		if(get_option('zcwc_connect_time'))
		self::zcwc_construct_fetch_zcwc_forms();
	}

	public static function zcwc_refresh_forms_list()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		self:: zcwc_construct_fetch_zcwc_forms();
		wp_die();
	}

	public static function zcwc_admin_menu() {
	 	add_menu_page( 'Zoho Campaigns', 'Zoho Campaigns', 'manage_options', 'zc-start', array( 'ZohoCampaign_Admin', 'zcwc_display_page' ), plugins_url('../../assets/images/zc_campaigns_logo.1.svg', __FILE__ ), 90);
		add_submenu_page('zc-start','SignUp-Forms','Signup Forms','manage_options','zc-forms',array( 'ZohoCampaign_Admin', 'zcwc_form_page' ));
		add_submenu_page('zc-start','Ecommerce','Ecommerce','manage_options','zc-wc',array( 'ZohoCampaign_Admin', 'zcwc_wa_page' ));
	}

	protected static function zcwc_icon_svg()	{
       	return base64_encode('<?xml version="1.0" encoding="utf-8"?>
				<!-- Generator: Adobe Illustrator 24.1.0, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 1024 1024" style="enable-background:new 0 0 1024 1024;" xml:space="preserve">
				<style type="text/css">
					.st0{fill:#E42427;}
					.st1{fill:#226DB4;}
					.st2{fill:#FFFFFF;}
					.st3{fill:#E42527;}
				</style>
				<path class="st3" d="M1022.01,663.75L922.22,98.26c-6.46-36.61-34.17-65.09-70.6-72.53c-36.43-7.45-73.09,7.86-93.41,39
					c-9.05,13.88-5.14,32.46,8.73,41.52c13.88,9.05,32.46,5.14,41.52-8.73c10.34-15.85,26.45-13.96,31.14-13
					c4.69,0.96,20.24,5.54,23.53,24.18l99.79,565.48c2.12,11.99-3.2,20.49-6.56,24.5s-10.81,10.72-22.98,10.72H353.39
					c-16.57,0-30,13.43-30,30s13.43,30,30,30h77.28l-54.64,150.96c-4.28,11.84-15.62,19.79-28.21,19.79H323.4
					c-14.57,0-26.99-10.41-29.54-24.76L188.96,324.06l465.47-219.75c14.98-7.07,21.39-24.95,14.32-39.94
					c-7.07-14.98-24.95-21.39-39.94-14.32L52.18,322.28C15.6,339.55-5.05,379.45,1.97,419.29l48.64,275.87
					c7.6,43.09,44.87,74.37,88.63,74.37h67.81l27.73,156.33c7.64,43.04,44.9,74.28,88.62,74.28h24.42c37.77,0,71.77-23.86,84.63-59.37
					l62.03-171.38h438.91c26.67,0,51.8-11.72,68.95-32.15C1019.47,716.8,1026.65,690.02,1022.01,663.75z M139.24,709.53
					c-14.59,0-27.01-10.43-29.54-24.79L61.06,408.87c-2.34-13.28,4.54-26.58,16.74-32.34l54.93-25.93l63.68,358.93H139.24z"/>
				<g>
					<path class="st1" d="M496.54,500.91c-13.63,0-27.44-3.1-40.37-9.6L318,421.94c-14.81-7.44-20.78-25.47-13.35-40.27
						c7.44-14.81,25.47-20.78,40.27-13.35l138.17,69.38c13.65,6.85,30.24,2.38,38.59-10.42L758.21,64.73
						c9.05-13.88,27.64-17.79,41.52-8.73c13.88,9.05,17.79,27.64,8.73,41.52L571.94,460.06C554.8,486.32,526.08,500.91,496.54,500.91z"
						/>
				</g>
				</svg>');
    	}

	public static function zcwc_display_page() {
		if(isset($_POST['mode']) && $_POST['mode']== 'callback')
		{
			$data = file_get_contents("php://input");;
			update_option("zcwc_wc_token",$data);
		}
		else if (self::zcwc_get_access_token() )
			self::zcwc_view( 'start' );
		else if(isset($_GET['code']))
			self::zcwc_display_configuration_page();
		else
			self::zcwc_view( 'conf' );
		delete_option('zcwc_error_msg');
	}

	public static function zcwc_pre_configuration_page() {
		$zcwc_domname = 'com';
		if(get_option('zcwc_domname'))
		{
			$zcwc_domname = get_option('zcwc_domname');
		}
		$auth_params = array(
					'client_id' => ZC4WP__CLIENT_ID,
					'response_type' => 'code',
					'scope' => 'ZohoCampaigns.forms.READ,ZohoCampaigns.userinfo.READ',
					'redirect_uri'    => 'https://campaigns.zoho.com/ua/wpredirect',
					'prompt'          => 'consent',
					'access_type'     => 'offline',
					'state'          => esc_url(get_admin_url() .'admin.php?page=zc-start'),
				);
		$auth_url = esc_url(ZC4WP__ACCOUNTS_URL. $zcwc_domname . '/oauth/v2/auth');
		$response = wp_remote_post( $auth_url, array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $auth_params
	    ) );
	}

	public static function zcwc_is_campaign_user()
	{
		$response  = self::zcwc_http_request('/api/v2/woocommerce/checkuserincampaign','GET','');
		$response_body = json_decode($response['body'],true);
		return $response_body;
	}

	public static function zcwc_display_configuration_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$zcwc_domname = 'com';
		if(isset($_GET['location']))
		{
			$loc = $_GET['location'];
			if($loc == 'in' || $loc == 'eu' || $loc == 'jp')
				$zcwc_domname = $loc;
			else if($loc == 'au')
				$zcwc_domname = 'com.au';
			else if($loc=='ca')
					$zcwc_domname = 'ca';
		}
		update_option("zcwc_domname",$zcwc_domname);
		$req_time = time();
		$auth_params = array(
					'client_id' => ZC4WP__CLIENT_ID,
					'grant_type'     => 'authorization_code',
					'client_secret'     => self::zcwc_return_constant_val('CLIENT_SECRET', $zcwc_domname),
					'code'          => $_GET['code'],
					'redirect_uri'    => esc_url('https://campaigns.zoho.com/ua/wpredirect'),
				);
		$account_url = ZC4WP__ACCOUNTS_URL;
		if($zcwc_domname=='ca')	{
				$account_url = ZC4WP__ACCOUNTS_URL_CA;
		}
		$auth_url = esc_url($account_url. $zcwc_domname. '/oauth/v2/token');
		$response = wp_remote_post( $auth_url, array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $auth_params
	    ) );
	    $response_body = json_decode($response['body'],true);
		if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if(isset($response_body['error']))
		{
			echo esc_html($response_body['error']);
		}
		else {
		    $response_body['req_time'] = $req_time;
		    update_option('zcwc_token_details', serialize($response_body),false);
		    update_option('zcwc_connect_time',$req_time,false);
		    //Validate if user exists in Zoho Campaigns
		    $zcwc_user = self::zcwc_is_campaign_user();
		    if(isset($zcwc_user['isUserPresent']) && $zcwc_user['isUserPresent'])
		    {
		    	update_option('zcwc_user',$zcwc_user);
		    	delete_option('zcwc_error_msg');
		    	self::zcwc_updateUserDetails();
		    }
		    else
		    {
		    	self::zcwc_remove_account();
		    	update_option('zcwc_error_msg',1);
				echo "<script>window.close();</script>";
		    	exit;
		    }
		}
		 echo "<script>window.close();</script>";
		exit;
	}

	public static function zcwc_updateUserDetails()
	{
		$zcwc_domname = 'com';
		if(get_option('zcwc_domname'))
		{
			$zcwc_domname = get_option('zcwc_domname');
		}
		$account_url = ZC4WP__ACCOUNTS_URL;
		if($zcwc_domname=='ca')	{
				$account_url = ZC4WP__ACCOUNTS_URL_CA;
		}
		$headarray = array('Authorization' => 'Zoho-oauthtoken '. self::zcwc_get_parsed_val('zcwc_token_details','access_token'));
		$auth_url = esc_url($account_url. $zcwc_domname. '/oauth/user/info');
		$response = wp_remote_get( $auth_url, array(
	    'method'      => 'GET',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => NULL,
	    'headers'     => $headarray,
	    ) );
		if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
		}
		else if($response)
		{
			 $response_body = json_decode($response['body'],true);
			 update_option('zcwc_user_email',sanitize_email($response_body['Email']),true);
		}
	}

	// Include Required php file
	public static function zcwc_view( $name, array $args = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
		$file = ZC4WP__PLUGIN_DIR . 'includes/'. $name . '.php';
		include( $file );
	}

	//Load styles and scripts maintaining version number.
	public static function zcwc_load_resources($hook) {
		$zmh_pages = array( 'toplevel_page_zc-start', 'zoho-campaigns_page_zc-wc', 'zoho-campaigns_page_zc-forms' );
		if(!in_array($hook, $zmh_pages)) {
                return;
        	}
			wp_register_style( 'mh-admin.css', plugin_dir_url( __FILE__ ) . '../../assets/css/mhadmin.css', array(), ZC4WP_VERSION );
			wp_enqueue_style( 'mh-admin.css');

		    wp_register_style('zcstyle', plugin_dir_url(  __FILE__ ) . '../../assets/css/old/style.css', array(), ZC4WP_VERSION );
		    wp_enqueue_style('zcstyle');

		    wp_register_style('zcfonts', plugin_dir_url(  __FILE__ ) . '../../assets/css/old/zcfonts.css', array(), ZC4WP_VERSION );
		    wp_enqueue_style('zcfonts');

		    wp_enqueue_script( 'jquery');
		    wp_enqueue_script( 'jquery-ui-datepicker');

		    wp_enqueue_script( 'mh_functions_js', plugin_dir_url(  __FILE__ ) . '../../assets/js/mh-functions.js', array('jquery'), ZC4WP_VERSION  );
		    wp_enqueue_script( 'mh_onload_js', plugin_dir_url(  __FILE__ ) . '../../assets/js/mh-onload.js', array('jquery'), ZC4WP_VERSION  );


  			wp_register_style( 'mh-jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/Redmond/jquery-ui.css' );
    		wp_enqueue_style( 'mh-jquery-ui' );

		   	wp_register_script( 'zcwc_onload_js', plugin_dir_url(  __FILE__ ) . '../../assets/js/zcwc-campaign.js' );
	}

	protected static function zcwc_get_parsed_val($key,$value) {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		 $mh_Object = unserialize(get_option($key));
		 if(isset($mh_Object[$value]))
		 {
		 	return $mh_Object[$value];
		 }
		 else return false;
	}

	protected static function zcwc_get_access_token() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		$mh_aceess_token = self::zcwc_get_parsed_val('zcwc_token_details','access_token');
		if ( $mh_aceess_token ) {
			if((time() - self::zcwc_get_parsed_val('zcwc_token_details','req_time')) >= 3600)
			{
				return self::zcwc_update_token(self::zcwc_get_parsed_val('zcwc_token_details','refresh_token'));
			}
			else
			return $mh_aceess_token;
		}
		else return false;
	}

	protected static function zcwc_update_token($ref_token) {
		$zcwc_domname = 'com';
		if(get_option('zcwc_domname'))
		{
			$zcwc_domname = get_option('zcwc_domname');
		}
		$req_time = time();
		$ref_auth_params = array(
					'client_id' => ZC4WP__CLIENT_ID,
					'grant_type'     => 'refresh_token',
					'client_secret'     => self::zcwc_return_constant_val('CLIENT_SECRET', $zcwc_domname),
					'refresh_token'          => $ref_token,
				);
		$account_url = ZC4WP__ACCOUNTS_URL;
		if($zcwc_domname=='ca')	{
				$account_url = ZC4WP__ACCOUNTS_URL_CA;
		}
		$auth_url = esc_url($account_url. $zcwc_domname. '/oauth/v2/token');
		$response = wp_remote_post( $auth_url, array(
	    'method'      => 'POST',
	    'timeout'     => 45,
	    'redirection' => 5,
	    'httpversion' => '1.0',
	    'blocking'    => true,
	    'body'        => $ref_auth_params
	    ) );
	    $response_body = json_decode($response['body'],true);
		if( wp_remote_retrieve_response_code( $response ) != 200 ) {
			echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $response )) ;
			return false;
		}
		else if(isset($response_body['error']))
		{
			echo esc_html($response_body['error']);
			return false;
		}
		else {
		    $response_body['refresh_token'] = $ref_token;
		    $response_body['req_time'] = $req_time;
		    update_option('zcwc_token_details', serialize($response_body),false);
		    return $response_body['access_token'];
		}
	}

	// Redirect to connect page if account is not connected.
	public static function zcwc_form_page() {

		if (self::zcwc_get_access_token() )
			self::zcwc_view( 'zc-signup-form' );
		else
			self::zcwc_view( 'conf' );
	}

	public static function zcwc_wa_page() {
		if (self::zcwc_get_access_token() )
		{
			if(isset($_GET['success']) && $_GET['success'] == 0)
		    {
		      update_option('zcwc_error_msg',"Integration with Zoho Campaigns has been denied");
		      wp_safe_redirect(get_admin_url('') ."admin.php?page=zc-wc");
		      exit();
		    }
			else if(isset($_GET['user_id']))
			{
				self::zcwc_update_integration_data();
				wp_safe_redirect(get_admin_url('') ."admin.php?page=zc-wc");
			}
			else {
					if(get_option('zcwc_integration') == 1)
					{
						self::zcwc_update_integration_data();
					}
					else if(get_option('zcwc_integration') == 3)
					{
						$query_string = http_build_query(['integrationIdDigest' => self::zcwc_get_parsed_val('zcwc_intergration_details','integration_digest')]);
						$new_response = self::zcwc_http_request('/api/v2/woocommerce/storestats?' .$query_string,'GET','');
						$new_response_body = json_decode($new_response['body'],true);
						if( wp_remote_retrieve_response_code( $new_response ) != 200 ) {
							echo esc_html("Sorry, something went wrong. HTTP Error Code: - " . wp_remote_retrieve_response_code( $new_response )) ;
						}
						else if(isset($new_response_body['account_status']) && $new_response_body['account_status'] == 0 )
						{
							self::zcwc_integration_disconnect();
							wp_safe_redirect(get_admin_url('') ."admin.php?page=zc-wc");
		      				exit();
						}
						else
						{
							update_option('zcwc_store_stats',serialize($new_response_body));
						}
					}
					self::zcwc_view( 'zc-wc' );
					delete_option('zcwc_error_msg');
			}
		}
		else
		{
			self::zcwc_view( 'conf' );
			delete_option('zcwc_error_msg');
		}

	}


	public static function zcwc_update_integration_data() {
		delete_option('zcwc_error_msg');
		$response  = self::zcwc_http_request('/api/v2/woocommerce/validate?storeurl='.get_site_url(),'POST','');
		$response_body = json_decode($response['body'],true);
		if(isset($response_body['message']))
		{
			update_option("zcwc_error_msg",$response_body['message']);
		}
		else if(isset($response_body['code']) && $response_body['code'] == 200)
		{
			update_option("zcwc_intergration_details",serialize($response_body));
			update_option("zcwc_integration",1);
			//echo $response_body;
		}
		else
		{
			echo "Error in fetching details";
			update_option("zcwc_error_msg","Error in fetching details. Try Again & If the issue still persists contact customer support");
		}
	}

	// Delete All Data and Settings Before Removing the Account.
	public static function zcwc_remove_account() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		self::zcwc_integration_disconnect();
		global $wpdb, $table_prefix;
		$tblname = $table_prefix . 'zcwc_forms';
		$delete = $wpdb->query("TRUNCATE TABLE $tblname");
		delete_option('zcwc_connect_time');
		delete_option('zcwc_user_email');
		delete_option('zcwc_rated');
		delete_option('zcwc_domname');
		delete_option('zcwc_user');
		delete_option('zcwc_token_details');
	}

	// Runs on Activation, Any meta changes will be updated to the table.
	public static function zcwc_create_mhforms_table()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		  global $wpdb, $table_prefix;
	      $tblname = $table_prefix . 'zcwc_forms';
	      $charset_collate = $wpdb->get_charset_collate();
	        $sql = "CREATE TABLE $tblname (
	          id int(11) NOT NULL AUTO_INCREMENT,
	          form_id varchar(56) NOT NULL UNIQUE,
	          form_name varchar(56) NOT NULL,
	          form_type varchar(30) NOT NULL,
	          list_name varchar(100) NOT NULL,
	          status tinyint(2) NOT NULL DEFAULT 1,
	          url varchar(255)  NOT NULL,
	          created_time bigint(19),
	          PRIMARY KEY  (id)
	        ) $charset_collate;";
	        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	        dbDelta($sql);
	}
	public static function  zcwc_generatePluginActivationLinkUrl($plugin)
	{
	    if (strpos($plugin, '/')) {
	        $plugin = str_replace('/', '%2F', $plugin);
	    }

	    $activateUrl = sprintf('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s', $plugin);

	    // change the plugin request nonce check
	    $_REQUEST['plugin'] = $plugin;
	    $activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);

	    return $activateUrl;
	}

	public static function zcwc_optin_save()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}
		check_ajax_referer( 'mh-ajax-nonce', 'security' );
		if (isset($_POST['msg']) && isset($_POST['check'])) {
			$allowed_values = array('checked', 'unchecked', 'hidden');
			$check_value = sanitize_text_field($_POST['check']);
			if (!in_array($check_value, $allowed_values)) {
				$check_value = 'unchecked';
			}
			$zcwc_optin_setting = array(
				'check' => $check_value,
				'label' => sanitize_text_field($_POST['msg']),
				'hook' => sanitize_text_field($_POST['hook'])
			);
			update_option('zcwc_optin_setting', serialize($zcwc_optin_setting));
    	}
	}
}
?>
