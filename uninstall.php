<?php
/**
 * Zoho Campaigns Uninstall
 *
 * Uninstalling Zoho Campaigns deletes user data, settings, tables, and options.
 *
 * @package Zoho Campaigns\Uninstaller
 * @version 2.1.1
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

function zcwc_dummy_get_parsed_val($key,$value) {
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

global $wpdb, $wp_version;
$headarray = array('Authorization' => 'Zoho-oauthtoken '. zcwc_dummy_get_parsed_val('zcwc_token_details','access_token') );
$query_string = http_build_query(['integrationIdDigest' => zcwc_dummy_get_parsed_val('zcwc_intergration_details','integration_digest')]);
$zcwc_domname = 'com';
if(get_option('zcwc_domname'))
{
	$zcwc_domname = get_option('zcwc_domname');
}
$campaign_url = ZC4WP__CAMPAIGN_URL;
if($zcwc_domname=='ca')	{
		$campaign_url = ZC4WP__CAMPAIGN_URL_CA;
}
$url= $campaign_url . $zcwc_domname . '/api/v2/woocommerce/deny?' . $query_string;
			$response = wp_remote_request( $url, array(
		    'method'      => 'POST',
		    'headers'     => $headarray
		    ) );
delete_option('zcwc_store_stats');
delete_option('zcwc_integration');
delete_option('zcwc_intergration_details');
delete_option('zcwc_error_msg');
delete_option('zcwc_optin_setting');

//Remove Meta values
$wp_user_query = new WP_User_Query(array('role' => 'Customer'));
$users = $wp_user_query->get_results();
if (!empty($users)) {
    foreach ($users as $user)
    {
      delete_user_meta( $user->id, 'zcwc_newsletter_subscription', true );
    }
}
// Remove tables
global $wpdb;
$table_name = $wpdb->prefix . 'zcwc_forms';
$wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS {$table_name}"));
// Remove options
delete_option('zcwc_script');
delete_option('zcwc_script_setting');
delete_option('zcwc_connect_time');
delete_option('zcwc_user_email');
delete_option('zcwc_rated');
delete_option('zcwc_domname');
delete_option('zcwc_user');
delete_option('zcwc_token_details');
?>
