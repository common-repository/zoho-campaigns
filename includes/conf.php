<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . esc_html(wp_create_nonce( 'mh-ajax-nonce' )) . '" />';
if(get_option('zcwc_error_msg')!= null && get_option('zcwc_error_msg') == 1)
{ ?>
	<div class="zhmalertmsg redband" onclick="closeBand()">
          <p>No Accounts Found for this user in Zoho Campaigns. Kindly create an account in Zoho Campaigns and try again.</p>
          <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>"></button>
      </div>
<?php
}
?>
<div class="zmhcontainer">
    <div class="zmhtextland">
        <img src="<?php echo esc_url( plugins_url('../assets/images/zc_campaigns_logo.svg', __FILE__ ) ); ?>" class="landlogo">
        <h1 class="landtit tc" >Zoho Campaigns</h1>
        <p class="tc" style="width: 70%; margin: 20px 15% 20px; line-height: 22px;color: #6d6c6c;">Connect WordPress with your Zoho Campaigs account, import your signup forms from Zoho Campaigns and embed them in your webpages. Make the best out of sophisticated automation for your WooCommerce store.</p>
        <div class="zmhbtncont">
			<button class="zmhbtn zmhpri zmhconnect" name="zh-submit" id="submit" value="Connect">Connect</button>
        </div>
    </div>
</div>
