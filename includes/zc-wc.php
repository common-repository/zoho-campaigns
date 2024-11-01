<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . esc_html(wp_create_nonce( 'mh-ajax-nonce' )) . '" />';
$ActiveClassess = array();
$end = 0;
if(get_option("zcwc_integration") != null)
{
  $end = intval(get_option("zcwc_integration"));
}
switch ($end) {
        case 0:
         $ActiveClassess = array("","","","","");
        break;

        case 1:
         $ActiveClassess = array("active","active","","","");
        break;

        case 2:
        $ActiveClassess = array("active","active","active","active","");
        break;

        case 3:
         $ActiveClassess = array("active","active","active","active","active");
         break;
      }
      if(get_option("zcwc_error_msg"))
      { ?>
          <div class="zhmalertmsg redband" onclick="closeBand()">
          <p><?php echo esc_html(get_option("zcwc_error_msg"));?></p>
          <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>"></button>
         </div>
    <?php }
?>
<div class="zmhpopupgen" id="wc_disconnect_popup" style="display: none;">
    <div class="">
        <img src="<?php echo esc_url( plugins_url('../assets/images/alert-circle.svg', __FILE__ ) ); ?>" />
    </div>
    <p>Are you sure you want to disconnect your store?</p>
    <div class="zmhconfsigcont zmhcenter" >
        <button class="zmhbtn zmhpri zmhmb35 zmhmr20 zcwcdeny">Disconnect</button>
        <button class="zmhbtn zmhcan zmhmb35 zmhmr20" onclick="confirm_activate(0);">Cancel</button>
    </div>
</div>
<div class="zmhtit">
    <img src="<?php echo esc_url( plugins_url('../assets/images/zc_campaigns_logo.svg', __FILE__ ) ); ?>">
    <h1>Zoho Campaigns for WooCommerce</h1>
</div>
   <div class="zhmalertmsg redband" style="display:none" onclick="closeBand()">
        <p></p>
        <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>"></button>
    </div>
    <div class="zhmalertmsg greenband" style="display:none" onclick="closeBand()" >
        <p></p>
        <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>"></button>
    </div>
<div class="zmhcontaainer mt80">
  <?php if($end < 3) { ?>
    <div class="p30 ">
     <div class=" p20 ">
        <div class="zcbigwizrdcont">
              <div class="zcbigwizrd">
                  <div class="zcbwpoint <?php echo esc_html($ActiveClassess[0]) ?> ">
                      <div class="zcbwstage"><span>Connect</span></div>
                  </div>
                  <div class="zcbwline <?php echo esc_html($ActiveClassess[1]) ?>"> </div>
                  <div class="zcbwpoint <?php echo esc_html($ActiveClassess[2]) ?>">
                      <div class="zcbwstage"><span>Select List</span></div>
                  </div>
                  <div class="zcbwline <?php echo esc_html($ActiveClassess[3]) ?>"></div>
                  <div class="zcbwpoint <?php echo esc_html($ActiveClassess[4]) ?>">
                      <div class="zcbwstage"><span>Sync Completed</span></div>
                  </div>
              </div>
          </div>
        </div>
      <div class="tc ltr  w80 c">
        <div class="p30">
        <div class="p40 dtbl c w70">
          <div style="" class="dtbl c rel   c">
            <?php if(get_option("zcwc_integration") == 2) { ?>
            <div style="animation-iteration-count: infinite;animation-name: spin;animation-timing-function: linear;" class="r50 abs animated zcrotate zcsyncrotate"><i class="zcicon-autorenew txtwhite f30 "></i></div>
            <?php } elseif(get_option("zcwc_integration") == 1){?>
                    <div class="r50 p15 abs zcsynctick"><i class="zcicon-tick txtwhite f22"></i></div>
            <?php } ?>
            <img src="<?php echo esc_url( plugins_url('../assets/images/campaign-woocommerce-integration.png', __FILE__ ) ); ?>" alt="" width="600">
              <div class="fntwht400"><?php if(get_option("zcwc_integration") == 2) echo "Syncing"; else if(get_option("zcwc_integration") == 1) echo "Connected"; ?> </div>
            </div>
        </div>
         <?php if(get_option("zcwc_integration") == 1) {?>
          <div class="f24 mt20 fntwht400">Associate a Zoho Campaigns list to your store.</div>
          <div class="txtcnt f16 lh2 mt20">Select a mailing list in Zoho Campaigns in which you want to add your store's customers.</div>
          <div class="w70 c" style="margin-top: 10px;" onclick= "showContent('pageList',event);"><div class="drpdnmnu"> <span class="fr ml10"><i class="zcicon-chevron-down f20"></i></span> <p id="zcwc_list" style="margin: 0px;">Select a list</p></div>
          <div class="rel allfltrdrpdwns" id="pageList" style="display:none;height:40px;margin-top:-6px;">
             <div class="mlslctmlist" style="overflow-y:scroll;z-index:10">
                                  <div id="searchpages" class="drpdnmnulstcntr">
                                      <ul id="pagecheck">
                                                    <?php $zcwc_lists = ZohoCampaign_Admin::zcwc_get_parsed_val("zcwc_intergration_details","listDetails");
                                                      if($zcwc_lists == "" || !$zcwc_lists) { ?>
                                                        <li><?php echo "No lists found";?> </li>
                                                    <?php  } else {
                                                      foreach($zcwc_lists as $key => $value){ ?>
                                                        <li value="<?php echo esc_html($value); ?>" onclick="zcwc_setvalue('<?php echo esc_html($value);?>','<?php echo esc_html($key); ?>');" > <?php echo esc_html($key); ?> </li>
                                                   <?php   } }?>
                                      </ul>
                                  </div>
                                </div>
                                  <div class="bdrbtm"></div>
          </div>
          </div>
          <div class="w70 c mt20" style="
              margin-top: 20px;
          "><div class="zmhcheckboxlg left">
                              <input id="zcautocheck" type="checkbox" checked>
                              <label>
                                  <span>During initial sync, auto subscribe the existing customers.</span>
                              </label>
                          </div>
              </div>
          <?php } else if(get_option("zcwc_integration") == 2) { ?>
          <div class="f24 mt20 fntwht400">Your store is being synced.</div>
          <div class="txtcnt f16 lh2 mt20">Sit back and relax! This might take a while</div>
        <?php } else { ?>
          <div class="f24 mt20 fntwht400">Reach out to your customers and start engaging with them</div>
          <div class="txtcnt f16 lh2 mt20">Connect your online store, send product promotional emails, and earn a greater return on your investment. Recover sales from abandoned carts, bring customers back to your store using automated email follow-ups, and much more.</div>
          <?php } ?>
        </div>
         <?php if(get_option("zcwc_integration") == 1) {?>
           <div class="">
            <input type="button" class="zmhbtn zmhpri zcwc-add-list" value="Associate List">
            <div class="zhmworcont" style="display: none;">
              <div class="zhmworlod">
                  <div></div>
                  <div></div>
                  <div></div>
                  <div></div>
              </div>
            </div>
          </div>
             <?php } else if(get_option("zcwc_integration") == 2) { ?>
              <div class=""><input type="button" class="zmhbtn zmhpri zcwcrefresh" value="Refresh"></div>
          <?php } else { ?>
                   <div class="">
                    <input type="button" class="zmhbtn zmhpri zcauthorize" value="Connect Store">
                    <div class="zhmworcont" style="display: none;">
                      <div class="zhmworlod">
                          <div></div>
                          <div></div>
                          <div></div>
                          <div></div>
                      </div>
                    </div>
                  </div>
          <?php } ?>
        </div>
   </div>
 <?php } else {?>
  <div class="p30 ">
        <div class="mt30">
        <div class=" ltr  w80 c  bgwhite " style="">
        <div class="f16 fntwht400">Store Details</div><div class="mt10 bgwhite bdr p10 rel">
        <div class=" abs" style="right: 20px;top: 20px;"><a href=" <?php $zcwc_domname = 'com';
          if(get_option('zcwc_domname'))
          {
            $zcwc_domname = get_option('zcwc_domname');
          }
          print_r(ZC4WP__CAMPAIGN_URL. $zcwc_domname . '/campaigns/org' .ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','org_url_digest'). '/home.do#e-commerce'); ?>" target="_blank"
          class="zmhbtn zmhpri">Open Zoho Campaigns</a></div><div class="tbl w100
        ">
        <div>
        <div class="vm w15 p10 fntwht400">Store Name:</div>
        <div class="vm">Zoho</div>
        </div>
        <div>
          <div class="vm w15 p10 fntwht400">Store Status:</div>
          <div class="vm"><?php $zc_acc_status = ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','account_status'); if($zc_acc_status == 0) echo "<font color='red'>Disconnected</font>"; else echo "<font color='green'>Connected</font>";?></div>
        </div>
        <div>
        <div class="vm  p10 fntwht400">Associated List:</div>
        <div class="vm"><?php $zc_cur_list = ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','listDigest');
        $zcwc_lists = ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_intergration_details','listDetails');foreach($zcwc_lists as $key => $value){
          if($value == $zc_cur_list)
            echo esc_html($key);
        }?></div>
        </div>

    <!--     <div>
        <div class="vm  p10 fntwht400">Sync Status:</div>
        <div class="vm">Success</div>
        </div> -->
        <div>
        <div class="vm  p10 fntwht400">Last Sync Time:</div>
        <div class="vm"><?php if(ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','last_sync_time'))echo esc_html(get_date_from_gmt(gmdate( 'Y-m-d H:i:s', substr(ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','last_sync_time'), 0 , -3)), get_option('date_format'))); else echo "<font color='green'>Syncing in progress</font>" ?>
         <i class="zcicon-refresh zcwc-refresh-sync" style="display: inline;font-size: 18px;position: relative;top: 3px;color: #009ad6;"></i>
        <!-- <a class="linktxt ml30">Resync</a> --></div>
        </div>

        <div>
          <input type="submit" class="vm  p10 fntwht400 wcdeny" value="Disconnect Store" style="background-color: transparent;color: #009ad5;cursor: pointer; outline: none;">
        </div>

        </div>
        </div>
              <div class="f16 fntwht400 mt30">Data Synced</div><div class="mt10">
        <div class="tbl w100">
        <div>
        <div class="w20 vt">
        <div class="zcbox mr10 vt">
        <div style="height: 150px;" class="p30 f30    bgwhite"><div class="  tc ">
        <div class=" f16 fntwht400 mt20">Orders</div><div class="f26   tc mt10 fntwht400"><?php echo esc_html(ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','order_count')); ?></div>
        </div></div>
        <div class="clr"></div>
        </div>
        </div><div class="w20 vt tc">
        <div class="zcbox mr10 ml10 " style="">
        <div style="height: 150px;" class="p30 f30 bgwhite"><div class=" f16 fntwht400 mt20">Products</div><div class="  tc mt10">
        <div class="f28   tc fntwht400"><?php echo esc_html(ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','product_count')); ?></div>
        </div></div>
        <div class="clr"></div>
        </div>
        </div><div class=" vt">
        <div class="zcbox  ml10">
        <div style="height: 150px;" class="p30 f30    bgwhite"><div class="  tc ">
        <div class=" f16 fntwht400 ">Customers</div><div class=" w50 fl">
        <div class="f28   tc mt10 fntwht400"><?php echo esc_html(ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','non_marketing_customers')); ?></div>
        <div class="f14 mt10">Marketing Not Allowed</div>
        </div><div class=" w50 fl">
        <div class="f28 tc mt10 fntwht400">
        <?php echo esc_html(ZohoCampaign_Admin::zcwc_get_parsed_val('zcwc_store_stats','marketing_customers')); ?></div>
        <div class="f14 mt10">Marketing  Allowed</div>
        </div>
        </div>
        </div>
        <div class="clr"></div>
        </div>
        </div>
        </div>
        </div>
        <div class="zcoptsetig">
        <div class="f16 fntwht400 mt30">
            Opt-in Settings
        </div>
        <div class="mt10 bgwhite bdr p20 rel fl mb20">
        <div class=" abs" style="right: 20px;top: 20px;"><button class="zmhbtn zmhpri zcwc_optin">Save</button></div>
        <div class="zcoptsetvalcot mb20 mt30">
            <div class="zcoptsetval">
                <h2>Message for the opt-in checkbox</h2>
                <p>Add the text that corresponds to the checkbox that customers can click to opt in to your newsletters.</p>
            </div>
            <div class="zcoptsetval">
                <textarea class="zcwc_optin_msg" placeholder="Subscribe to our newsletter"><?php echo esc_html(ZohoCampaign::zcwc_get_parsed_val('zcwc_optin_setting', 'label')); ?></textarea>
            </div>
        </div>

        <div class="zcoptsetvalcot mb20">
            <div class="zcoptsetval">
                <h2>Checkbox Display Options</h2>
                <p>Choose how you want the opt-in checkbox to be displayed in your pages.</p>
                <p>Customers can click a box at checkout to opt in to your newsletter.</p>
            </div>
            <div class="zcoptsetval">
              <?php $zcwccheck = ZohoCampaign::zcwc_get_parsed_val('zcwc_optin_setting', 'check'); ?>
                <select class="zcwc_optin_option">
                    <option check="unchecked" <?php if($zcwccheck == 'unchecked') echo 'selected'; ?>>Visible & unchecked</option>
                    <option check="checked" <?php if($zcwccheck == 'checked') echo 'selected'; ?> >Visible & checked</option>
                    <option check="hidden" <?php if($zcwccheck == 'hidden') echo 'selected'; ?>>Hidden & unchecked</option>
                </select>
            </div>
        </div>

        <div class="zcoptsetvalcot">
            <div class="zcoptsetval">
                <h2>Advanced Checkbox Settings</h2>
                <p>Choose the location of the opt-in checkbox at checkout, input one of the available<a href="https://woocommerce.github.io/code-reference/hooks/hooks.html" target="_blank"> WooCommerce form actions</a>.</p>
            </div>
             <div class="zcoptsetval">
                <input class="zcwc_optin_hook" type="text" value="<?php echo esc_html(ZohoCampaign::zcwc_get_parsed_val('zcwc_optin_setting', 'hook')); ?>">
            </div>
        </div>
      </div>
    </div>
        </div>
        <div class="fntwht400 f16 tl mt30">Features</div>
        <div>
        <div class="w50 fl lh25 mt10">
            <div><i class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></i><span class="vm">Promote your product stores</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">A/B testing</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">E-Commerce tracking and reports</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">Product content block</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">Coupon Code content block</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">Purchase activity segmentation</span></div>
        </div>
        <div class="w50 fl lh25 mt10">
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">Abandoned cart email</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">Purchase follow up email</span></div>
            <div><em class="zcicon-check-circle-outline txtgreen f20 mr5 vm"></em><span class="vm">Signup forms</span></div>
        </div>
            <div class="clr"></div>
          </div>
            <div class="clr"></div>
            <div class="bdrbtm mt20"></div>
            <div class="fntwht400 f18 tl mt30">Need Help</div>
            <div class="mt10">Have a question about Zoho Campaigns for WooCommerce? Please check our <a class="linktxt" href="https://help.zoho.com/portal/kb/articles/zoho-campaigns-integration-with-woocommerce" target="_blank">resources</a> or reach out to us at <a class="linktxt" href = "mailto:support@zohocampaigns.com">support@zohocampaigns.com</a>
            </div>
        </div>
    </div>
</div>
 <?php } ?>
</div>
