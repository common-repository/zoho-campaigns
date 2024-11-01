<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . esc_html(wp_create_nonce( 'mh-ajax-nonce' )) . '" />';
?>
<div class="zmhpopupgen" id="mh_disconnect_popup" style="display: none;">
    <div class="">
        <img src="<?php echo esc_url( plugins_url('../assets/images/alert-circle.svg', __FILE__ ) ); ?>" />
    </div>
    <p>Are you sure you want to disconnect Zoho Campaigns plugin?</p>
    <div class="zmhconfsigcont zmhcenter" >
        <button class="zmhbtn zmhpri zmhmb35 zmhmr20" id="mh_remove">Disconnect</button>
        <button class="zmhbtn zmhcan zmhmb35 zmhmr20" onclick="confirm_activate(0);">Cancel</button>
    </div>
</div>

<div class="zmhcontaainer">
	<div class="zmhaccoutname">
	    <div class="zmhaccoutright">
		    <div class="zmhaccname">
			    <span class="email"><?php echo esc_attr(get_option("zcwc_user_email")); ?></span>
			    <?php $time = intval(get_option('zcwc_connect_time'));?>
			    <span class="timedate"> Connected On <?php echo esc_html(get_date_from_gmt(gmdate('Y-m-d H:i:s' , $time), get_option('date_format') .' '. get_option('time_format'))); ?></span>
		    </div>
	    	<input type="submit" id="mh-disconnect" value="Disconnect Account">
	    </div>
	</div>
	<div class="zmhtextland">
            <div class="zcplulogo">
                <img src="<?php echo esc_url( plugins_url('../assets/images/zc_campaigns_logo.svg', __FILE__ ) ); ?>">
                <div class="zcplulogomsg">
                    <h1>Welcome to Zoho Campaigns</h1>
                    <p>You have now connected your Zoho Campaigns account</p>
                </div>
            </div>
            <div class="zmhauthenticatecont">
                <div class="zmhauthenticatebox">
                    <img src="<?php echo esc_url( plugins_url('../assets/images/signup-form.svg', __FILE__ ) ); ?>" />
                    <div class="zmhauthenticatinr">
                        <h1>Signup From</h1>
                        <p>Create customized signup forms in Zoho Campaigns, bring them into Wordpress, and embed them in your webpages using the forms' short code.</p>

                        <div class="zmhauthenticatinbtn">
                            <button class="zmhbtn zmhpri zmhmb20"onclick="window.location = 'admin.php?page=zc-forms'">Proceed</button>
                        </div>
                    </div>
                </div>
                <div class="zmhauthenticatebox">
                    <img src="<?php echo esc_url( plugins_url('../assets/images/zcampaign-to-woocommerce.png', __FILE__ ) ); ?>" />
                     <div class="zmhauthenticatinr">
                        <h1>WooCommerce</h1>
                        <p>Connect your online store, send product promotional emails, and earn a greater return on your investment. Recover sales from abandoned carts, bring customers back to your store using automated email follow-ups, and much more.</p>
                        <div class="zmhauthenticatinbtn">
                            <button class="zmhbtn zmhpri zmhmb20"onclick="window.location = 'admin.php?page=zc-wc'">Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
