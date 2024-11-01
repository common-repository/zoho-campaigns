jQuery(document).ready(function($) {

    jQuery("#datepicker").datepicker({changeMonth: true,yearRange: '2003:2020', showOn: 'focus', showButtonPanel: true, closeText: 'Clear',
        onClose: function () {
            var event = arguments.callee.caller.caller.arguments[0];
            if ($(event.delegateTarget).hasClass('ui-datepicker-close')) {
                $(this).val('');
            }
        }});

    jQuery('#zcwc_submit').click(function(e) {
        jQuery("#test").removeClass("err");
        jQuery("#add_page_tracking_script_globalErr").html("");
        jQuery("#add_page_tracking_script_globalErr").hide();
        jQuery("#cateogrylist").removeClass("err");
        jQuery(this).disabled = true;
        if(jQuery("#ldcodesnippet").val() == "")
        {
            jQuery("#test").addClass("err");
            jQuery("#testErr").show();
        }
        else {

           if(jQuery("#zcwc_code_loc").attr('value') == 'specific')
            {
                if((jQuery("#pagebutton").hasClass("zcicon-checkbox-blank-outline") && jQuery("#postbutton").hasClass("zcicon-checkbox-blank-outline")))
                {
                         jQuery("#add_page_tracking_script_globalErr").html("Please select at least one page or post to proceed");
                         jQuery("#add_page_tracking_script_globalErr").show();

                }
                else if(jQuery("#pagebutton").hasClass("zcicon-checkbox-marked") && jQuery("#zcwc_pagevalue").attr('value') == "")
                {
                     //alert("please select at least one page to proceed.");
                     jQuery("#add_page_tracking_script_globalErr").html("Please select at least one page to proceed");
                     jQuery("#add_page_tracking_script_globalErr").show();

                }
                else if(jQuery("#postbutton").hasClass("zcicon-checkbox-marked") && jQuery("#zcwc_postvalue").attr('value') == "")
                {
                     //alert("please select at least one post to proceed.");
                     jQuery("#add_page_tracking_script_globalErr").html("Please select at least one post to proceed");
                     jQuery("#add_page_tracking_script_globalErr").show();
                }
                else {
                    jQuery("#zcwc_submit").addClass("btnload");
                    var val = jQuery("#zcwc_submit").attr('value');
                    jQuery("#ldsubmit").attr("value",val);
                    //alert("Data Saved succesfully");
                    jQuery("#zcwc_form").submit();
                }
            }
                    
            else if(jQuery("#zcwc_code_loc").attr('value') == 'cateogry' && jQuery("#cateogrylist").val() == "")
            {
               //alert("please select a post cateogry");
               jQuery("#add_page_tracking_script_globalErr").html("Please select a post cateogry");
               jQuery("#cateogrylist").addClass("err");
               jQuery("#add_page_tracking_script_globalErr").show();
            }       
            else{
                jQuery("#zcwc_submit").addClass("btnload");
                var val = jQuery("#zcwc_submit").attr('value');
                jQuery("#ldsubmit").attr("value",val);
                //alert("Data Saved succesfully");
                jQuery("#zcwc_form").submit();
            }
        }
        
    });
jQuery('#zcwc_submit1').click(function(e) {
    var val = jQuery("#zcwc_submit1").attr('value');
            var change = jQuery("#zcwc_change").attr('value');
            jQuery("#zcwc_submit1").addClass("btnload");
            if(change == 1)
            {
                jQuery("#webAutocancelpopup").slideDown();
                jQuery("#wa_body").css("z-index",-1);
                jQuery("#wa_body").css("opacity",0.5);
            }
            else
            {
                jQuery("#ldsubmit").attr("value",val);
                jQuery("#zcwc_form").submit();
            }
});

       
    jQuery('#add_page_tracking_script_global').change(function(e) {
        var tsm_global = jQuery('input[name="zcwc_code_loc"]:checked').val();
        if(tsm_global == 'global') {
            jQuery('#selectpost').css('display','none');
            jQuery('#Cateogry').css('display','none');
            
        } else if(tsm_global == 'specific') {
            
            jQuery('#selectpost').css('display','block');
            jQuery('#Cateogry').css('display','none');
        }
        else if(tsm_global == 'cateogry'){
            
            jQuery('#Cateogry').css('display','block');
            jQuery('#selectpost').css('display','none');
        }
    });

    jQuery('#zcwc_post_name').change(function(e) {
        tsm_postType = jQuery('#zcwc_post_name option:selected').val();
        if(tsm_postType == 'none') {
        } else {
            tsm_load_posts();
            zcwc_load_posts();
        }
    });
    jQuery('#postlists').change(function(e) {
        jQuery('#selectedAddTagsList').val() = jQuery('#postlists option:selected').val();
        jQuery('#selectedAddTagsList').innerHTML() = jQuery('#postlists').innerHTML();
    });

    jQuery('#getdomain').click(function(e) {
            wa_load_domain();
    });
    jQuery('#addPostsSrchBox').click(function(e) {

            jQuery("#addPostList").toggle();
    });  

    jQuery('.zmhsiforlstcont').click(function(event){
        if(event.target && event.target.className && typeof(event.target.className) == "string" && event.target.className.indexOf("animOff") > -1)
        {
            return false;
        }
        if(jQuery(this).find('div .zmhsiforlstrit').css('display')!='block' && jQuery(this).find('div .zmhsigfomdet').css('display')!='block')
        {
            jQuery('.zmhsiforarr').removeClass("rota90");
            jQuery('div .zmhsiforlstrit').slideUp();
            jQuery('div .zmhsigfomdet').slideUp();
            var target = jQuery(this);
            target.find('div.zmhsiforlstrit').slideDown();
            target.find('div.zmhsigfomdet').slideDown();
            target.find('#zmh_code_msg').css('display','none');
            target.find('.zmhsiforarr').addClass("rota90");
            if(target.find('#formview').attr("loaded") == "false")
            {   
                target.find('#formview').parent().addClass("zmhloading");
                target.find('#formview').attr("src",jQuery(this).find('#formview').attr("srcval"));
                target.find('#formview').attr("loaded" , "true");
                target.find('#formview').load(function () {
                    jQuery('.zmhsiforlstrit').removeClass('zmhloading');
                }); 
            }
                
        }
        else
        {
            jQuery('div .zmhsiforlstrit').slideUp();
            jQuery('div .zmhsigfomdet').slideUp();
            jQuery(this).find('.zmhsiforarr').removeClass("rota90");
        }
    });
    //.children().click(function(e) {return false;})
    jQuery('.zmhbtnonoff').click(function(e) {
            jQuery('.greenband').hide();
            jQuery('.redband').hide();
            if(jQuery(".zmhbtnonoff").hasClass('zhmtoglabon'))
            {
                jQuery("#wacheckbox").find('input[type=checkbox]').attr('checked',false);
                jQuery("#webAutoStatusPopup").slideDown();
                jQuery("#wa_body").addClass('popupopen');
            }
            else
            {
                jQuery(".zmhbtnonoff").addClass('zhmtoglabon')
                jQuery("#zmh_trk_swth_txt").text('Deactivate');
                jQuery("#zcwc_status").attr('value',1);
                jQuery("#zcwc_change").attr('value',1);
            }
    });  

    jQuery('#mhrefreshForm').click(function(e) {
     //    jQuery("#formRefreshPopup").slideDown();
        // jQuery('.zmhsignformlst').addClass('popupopen');
        mh_refresh_forms();
    });  

    var ele ="";
    jQuery('.mhlab').click(function(e) {
        ele = this;
             var data = {
            'action': 'zcwc_change_form_status',
            'id': jQuery(this).attr('mhid'),
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
            if (response == 'successful') {
                
                if( jQuery(ele).hasClass("active") )
                {
                    jQuery(ele).removeClass('active')
                    jQuery(ele).parent().parent().find('span:first-child').removeClass('active').addClass('hidden');
                    jQuery(ele).parent().parent().find('span:nth-child(2)').text('Hidden');

                }
                else
                {
                     jQuery(ele).addClass('active')
                    jQuery(ele).parent().parent().find('span:first-child').removeClass('hidden').addClass('active');
                    jQuery(ele).parent().parent().find('span:nth-child(2)').text('Visible');
                }
                ele ="";
            }
        });
    });  

    jQuery('.mhclip').click(function() {
        var newInput = document.createElement('input');
        newInput.setAttribute('type','text');
        newInput.setAttribute('display','none');
        newInput.setAttribute('value',jQuery(this).attr('mhtext'));
        document.body.appendChild(newInput);
        newInput.select();
        document.execCommand('copy');
        newInput.parentElement.removeChild(newInput);
        jQuery(this).parent().parent().find('#zmh_code_msg').css('display','block');
    }); 

    jQuery('#getForms').click(function(e) {
        document.getElementsByClassName('zhmworcont')[1].style.display = 'block';
        closeBand();
             var data = {
            'action': 'zcwc_fetch_form',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
            document.getElementsByClassName('zhmworcont')[1].style.display = 'none';
            if(response != "")
            {
                jQuery('.redband').find("p").text(response);
                jQuery('.redband').show();
            }
            else
            {
                window.location.reload();
            }
            
        });
    });   

    jQuery('#mh_remove').click(function(e) {
                var data = {
            'action': 'zcwc_disconnect',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
            window.location.reload();
        });
    });   
    var win ="";
    jQuery( '.zmhconnect' ).click( function() {
        if(jQuery("#domainselect").attr("urldomain") != "")
        {
            var data = {
            'action': 'zcwc_connect',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
            };
            jQuery.post(ajaxurl, data, function(response) {
                win = window.open(response.substring(0, response.length-1));
                //opener.location = response.substring(0, response.length-1);
                var timer = setInterval(function () {
                if (win.closed) {
                        clearInterval(timer);
                        window.location.reload(); // Refresh the parent page
                    }
                }, 1000);
                // window.close();
                // window.reload();
            });
        }
        else
        {
            jQuery("#domainselect").addClass('err');
        }
    });

    jQuery('#mh-disconnect').click(function(e) {
        jQuery('#mh_disconnect_popup').slideDown();
        jQuery(".zmhcontaainer").addClass('popupopen');
    });   

    jQuery('.wcdeny').click(function(e) {
        jQuery('#wc_disconnect_popup').slideDown();
        jQuery(".zmhcontaainer").addClass('popupopen');
    }); 

    jQuery('.mh_sc').click(function(e) {
         ele = this;
        var data = {
            'action': 'zcwc_get_short_code',
            'id': jQuery(this).attr('mhid'),
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
           if(response == 'successful') {
            jQuery(ele).removeClass('mh_sc animOff');
            jQuery(ele).text('[zcwp id= '+jQuery(ele).attr('mhid')+"]");
            jQuery(ele).removeAttr('style');
            jQuery(ele).next().fadeIn();
            jQuery(ele).parent().parent().parent().find('.zmhformtogche').fadeIn();
            jQuery(ele).parent().parent().parent().find('label:first-child').addClass('active');
            jQuery(ele).parent().parent().parent().removeClass('inactive').addClass('active');
            jQuery(ele).parent().parent().parent().find('span:first-child').removeClass('inactive').addClass('active');
            jQuery(ele).parent().parent().parent().find('p span:nth-child(2)').text('Visible');
            ele ="";
            }
        });
 
    });  

    jQuery('#formview').load(function () {
        jQuery('.zmhsiforlstrit').removeClass('zmhloading');
     }); 

    jQuery( 'a.zmhub-rating-link' ).click( function() {
        jQuery.post( ajaxurl, { action: 'zoho_marketinghub_rated', 'security': jQuery( '#mh-ajax-nonce' ).val()} );
        jQuery( this ).parent().text( 'Thanks :)' );
    });

    jQuery('.zcauthorize').click(function(e) {
        jQuery('.redband').hide();
        jQuery('.zhmworcont').show();
        ele = this;
             var data = {
            'action': 'zcwc_woocommerce_authorize',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
             jQuery('.zhmworcont').hide();
            if (response == '1') {
               var msg = "Error in connecting your store with Zoho Campaign, Please contact support";
            }
            else if (response == '2')
            {
                var msg = "You don't have an account in Zoho Campaign Please Signup";
            }
            else if (response == '3')
            {
                var msg = "You don't have privileges to perform this action, Please contact support";
            }
            else if (response == '4')
            {
                var msg = "You don't have privileges to perform this action, Please contact support";
            }
            else
            {
                window.location.href = response;
                return;
            }
           jQuery('.redband').find("p").text(msg);
           jQuery('.redband').show();
        });
    });
    jQuery('.zcwc-add-list').click(function(e) {
         e.stopImmediatePropagation();
         e.preventDefault();
        if(jQuery("#zcwc_list").attr("value"))
        {
            jQuery('.redband').hide();
            jQuery('.zhmworcont').show();
            var isChecked = "false";
            if(jQuery("#zcautocheck").prop("checked"))
            {
                isChecked = "true";
            }
            ele = this;
                 var data = {
                'action': 'zcwc_add_list',
                'security': jQuery( '#mh-ajax-nonce' ).val(),
                'list_digest': jQuery("#zcwc_list").attr("value"),
                'isSubscribed': isChecked,
            };
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('.zhmworcont').hide();
                if (response != 'error') {
                    window.location.href = response;
                }
                else
                {

                }
            });
        }
        else alert("Please select a list");
    });
    jQuery('.zcwcrefresh').click(function(e) {
        ele = this;
             var data = {
            'action': 'zcwc_integration_status',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
            if (response != 'error') {
                window.location.href = response;
            }
            else
            {
                
            }
        });
    });
    jQuery('.zcwc_list').click(function(e) {
        ele = this;
             var data = {
            'action': 'zcwc_get_list',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
            if (response != 'error') {
                //window.location.href = response;
              //   foreach($zcwc_lists as $key => $value){ 
              //    jQuery("#pagecheck").appendChild("<li value= onclick="zcwc_setvalue('','');" > </li>");
                // } 
            }
            else
            {

            }
        });
    });
    jQuery('.zcwcdeny').click(function() {
             var data = {
            'action': 'zcwc_integration_disconnect',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
        };
        jQuery.post(ajaxurl, data, function(response) {
            window.location.reload();
        });
    }); 
    jQuery('.zcwc_optin').click(function() {
        jQuery('.greenband').hide();
        jQuery('.redband').hide();
        var optin_msg = jQuery('.zcwc_optin_msg').val();
        var optin_action_hook = jQuery('.zcwc_optin_hook').val();
        if(optin_msg =='' || optin_action_hook == '')
        {
            alert("Fields Can't be Empty");
        }
        else if(optin_msg.length > 120 || optin_action_hook.length > 120 )
        {
            alert("Fields length exceeds the limit");
        }
        else
        {
            var data = {
            'action': 'zcwc_optin_save',
            'security': jQuery( '#mh-ajax-nonce' ).val(),
            'msg': jQuery('.zcwc_optin_msg').val(),
            'hook': jQuery('.zcwc_optin_hook').val(),
            'check': jQuery('.zcwc_optin_option').children("option:selected").attr("check"),
            };
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('.greenband').find("p").text('Settings Saved Successfully');
                jQuery('.greenband').show();
            });
        }
    }); 
    jQuery('.zcwc-refresh-sync').click(function() {
        window.location.reload();
    }); 
});
jQuery(document).click(function()
{
    jQuery(".allfltrdrpdwns").slideUp();
});
