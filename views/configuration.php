<?php
echo '<h2>User Phone Meta Key<hr></h2>
     <p>
        Note: Please select a user <b>meta_key</b> which has <b>meta_value</b> as phone number. This will be used while sending the sms to the users.
     </p>
     <h3>Phone Meta Key:
     <select id="mobsms_phone_meta_key" name="mobsms_phone_meta_key">';
for ($i=0; $i < $size; $i++) {
    echo '<option value="'.$keys[$i].'">'.$keys[$i].'('.$values[$i].')</option>';
}
 echo '</h3>
        <input type="hidden" id="mobsms_nonce" name="mobsms_nonce" value="'.wp_create_nonce('mobsms_nonce').'">
        <br><br>
        <input type="button" class="mo_wpns_button mo_wpns_button1" id="save_phone_meta_key"name="Save" value="Save">

<script type="text/javascript">
    var phone_key = "'.$phone_meta_key.'";
    if(phone_key){
        jQuery("#mobsms_phone_meta_key option[value=\'phone_key\']").attr("selected","selected");
    }
    jQuery("#save_phone_meta_key").click(function(){
    var data = {
        "action" : "mobsms_action",
        "option" : "save_phone_meta_key",
        "key"    : jQuery("#mobsms_phone_meta_key").val(),
        "nonce"  : jQuery("#mobsms_nonce").val(),
    };
    jQuery.post(ajaxurl, data, function(response) {
        if(response == "FAIL")
         mobsms_error_msg("Invalid phone meta key");
         else
         mobsms_success_msg("Phone meta key saved successfully.");
    });
});
</script>';
