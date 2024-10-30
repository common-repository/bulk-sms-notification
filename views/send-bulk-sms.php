<?php

if (!$is_registered) {
    echo '<i>Please <a href="'.esc_url($profile_url).'">register</a> with miniOrange to use our bulk SMS service</i>';
}
echo '<div >
<h2> User Role </h2><hr> 
 <p>
    Note: Please select a user role to send the bulk SMS. Select all if there are more that one role.
 </p>
 <h3>User role:
<select name="role" id="role" '.esc_html($disabled).'>
    <option value="all">all</option>';
foreach ($roles as $role) {
    echo '<option value="'.esc_html($role).'">'.esc_html($role).'</option>';
}
echo '</select>
</h3>

<h2> Message </h2><hr>
<p>
    Note: Please enter the message to be sent to the selected role.
</p>
<input type="hidden" id="mobsms_nonce" name="mobsms_nonce" value="'. wp_create_nonce('mobsms_nonce').'">
<textarea cols="100" rows="5" id="mobsms_message" name="mobsms_message" '.esc_html($disabled).'></textarea>
<br>
<button name="mobsms_send_sms" id="mobsms_send_sms" class="mo_wpns_button mo_wpns_button1" '.esc_html($disabled).'>Send</button>
</div>
<div id="mobsms_logs">

</div>
<script type="text/javascript">
jQuery("#mobsms_send_sms").click(function(){
    jQuery("#mobsms_logs").html("<h2 style=\'color:blue\'>Messages are being sent... You will be notified once done!</h2>");
    var data = {
        "action" : "mobsms_action",
        "option" : "send_sms",
        "role"   : jQuery("#role").val(),
        "sms"    : jQuery("textarea#mobsms_message").val(),
        "nonce"  : jQuery("#mobsms_nonce").val()
    };
    jQuery.post(ajaxurl, data, function(response) {
        if(response == "INVALID_KEY")
            mobsms_error_msg("Please configure the phone meta key first.");
        else if(response == "SMS_EXCEED"){
            mobsms_error_msg("SMS transaction limit exceeded. Please recharge to use the service.");
            jQuery("#mobsms_logs").html("<h2 style=\'color:red\'>Could not send messages!</h2>");
        }
        else if(response == "INVALID_USER_ROLL")
            mobsms_error_msg("Invalid user role.");
        else if(response == "SUCCESS"){
            mobsms_success_msg("SMS notification sent successfully.");
            jQuery("#mobsms_logs").html("<h2 style=\'color:green\'>SMS sent successfully!</h2>");
        }
        
    });
});

</script>';
