<?php

    global $mobsms_dirname,$mobsms_db_queries;
    
if (current_user_can('manage_options')  && isset($_POST['option'])) {
    $option = sanitize_text_field($_POST['option']);
    switch ($option) {
        case "mobsms_send_query":
            mobsms_handle_support_form(sanitize_email($_POST['query_email']), sanitize_text_field($_POST['query']), sanitize_text_field($_POST['query_phone']));
            break;
    }
}

    $current_user   = wp_get_current_user();
    $email          = get_option("mobsms_email");
    $phone          = get_option("mobsms_admin_phone");


if (empty($email)) {
    $email      = $current_user->user_email;
}

    include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'support.php';

function mobsms_handle_support_form($email, $query, $phone)
{
    $nonce = sanitize_text_field($_POST['nonce']);
    if (!wp_verify_nonce($nonce, 'sendQueryNonce')) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ERROR'), 'ERROR');
        return;
    }
    if (empty($email) || empty($query)) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('SUPPORT_FORM_VALUES'), 'ERROR');
        return;
    }

    $contact_us = new MOBSMS_Api();
    $contact_us->submit_contact_us($email, $phone, $query);

    if (json_last_error() == JSON_ERROR_NONE) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('SUPPORT_FORM_SENT'), 'SUCCESS');
        return;
    }
            
    do_action('mobsms_show_message', MOBSMS_Messages::showMessage('SUPPORT_FORM_ERROR'), 'ERROR');
    return;
}
