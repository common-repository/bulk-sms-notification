<?php
    
    global $mobsms_utility,$mobsms_dirname,$mobsms_db_queries;

if (current_user_can('manage_options') and isset($_POST['option'])) {
    $option = sanitize_text_field(trim($_POST['option']));
    switch ($option) {
        case "mobsms_register_customer":
            mobsms_register_customer();
            break;
        case "mobsms_verify_customer":
            mobsms_verify_customer();
            break;
        case "mobsms_cancel":
            mobsms_revert_back_registration();
            break;
        case "mobsms_reset_password":
            mobsms_reset_password();
            break;
        case "mobsms_goto_verifycustomer":
            mobsms_goto_sign_in_page();
            break;
    }
}
    $user = wp_get_current_user();
if (get_option('mobsms_verify_customer') == 'true') {
    $admin_email = get_option('mobsms_email') ? get_option('mobsms_email') : "";
    include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'login.php';
} elseif (! mobsms_icr()) {
    include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'register.php';
} else {
    $email = get_option('mobsms_email');
    $key   = get_option('mobsms_customer_key');
    $api   = get_option('mobsms_api_key');
    $token = get_option('mobsms_customer_token');
    include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'profile.php';
}




function mobsms_register_customer()
{
    global $mobsms_db_queries, $mobsms_utility;
    $nonce = sanitize_text_field($_POST['nonce']);
    if (! wp_verify_nonce($nonce, 'mobsms-account-nonce')) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ERROR'), 'ERROR');
        return;
    }

        $email           = sanitize_email($_POST['email']);
        $company         = $_SERVER["SERVER_NAME"];

        $password        = sanitize_text_field($_POST['password']);
        $confirmPassword = sanitize_text_field($_POST['confirmPassword']);

    if (strlen($password) < 6 || strlen($confirmPassword) < 6) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('PASS_LENGTH'), 'ERROR');
        return;
    }
        
    if ($password != $confirmPassword) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('PASS_MISMATCH'), 'ERROR');
        return;
    }
    if (mobsms_check_empty_or_null($email) || mobsms_check_empty_or_null($password)
        || mobsms_check_empty_or_null($confirmPassword)) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('REQUIRED_FIELDS'), 'ERROR');
        return;
    }

        update_option('mobsms_email', $email);

        $customer = new MOBSMS_Api();
        $content  = json_decode($customer->check_customer($email), true);
    switch ($content['status']) {
        case 'CUSTOMER_NOT_FOUND':
              $mobsms_customer_key = json_decode($customer->create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = ''), true);
                  
            if (strcasecmp($mobsms_customer_key['status'], 'SUCCESS') == 0) {
                mobsms_save_success_customer_config($email, $mobsms_customer_key['id'], $mobsms_customer_key['apiKey'], $mobsms_customer_key['token'], $mobsms_customer_key['appSecret']);
                mobsms_get_current_customer($email, $password);
            }
                
            break;
        default:
            mobsms_get_current_customer($email, $password);
            break;
    }
}


function mobsms_goto_sign_in_page()
{
     global $mobsms_db_queries;
     $nonce = sanitize_text_field($_POST['nonce']);
    if (! wp_verify_nonce($nonce, 'mobsms-account-nonce')) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ERROR'), 'ERROR');
        return;
    }
       update_option('mobsms_verify_customer', 'true');
}

function mobsms_revert_back_registration()
{
    $nonce = sanitize_text_field($_POST['nonce']);
    if (! wp_verify_nonce($nonce, 'mobsms-account-nonce')) {
            do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ERROR'), 'ERROR');
        return;
    }
        delete_option('mobsms_email');
        delete_option('mobsms_verify_customer');
}


function mobsms_reset_password()
{
    global $mobsms_db_queries;
    $nonce = sanitize_text_field($_POST['nonce']);
    if (! wp_verify_nonce($nonce, 'mobsms-account-nonce')) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ERROR'), 'ERROR');
        return;
    }
        $customer = new MOBSMS_Api();
        $forgot_password_response = json_decode($customer->forgot_password());
    if ($forgot_password_response->status == 'SUCCESS') {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('RESET_PASS'), 'SUCCESS');
    }
        return;
}


function mobsms_verify_customer()
{
    global $mobsms_db_queries;
    $nonce = sanitize_text_field($_POST['nonce']);
    if (! wp_verify_nonce($nonce, 'mobsms-account-nonce')) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ERROR'), 'ERROR');
        return;
    }
        global $mobsms_utility;
        $email    = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);

    if (mobsms_check_empty_or_null($email) || mobsms_check_empty_or_null($password)) {
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('REQUIRED_FIELDS'), 'ERROR');
        return;
    }
        mobsms_get_current_customer($email, $password);
}

function mobsms_get_current_customer($email, $password)
{
    global $mobsms_db_queries;
    $user        = wp_get_current_user();
    $customer    = new MOBSMS_Api();
    $content     = $customer->get_customer_key($email, $password);
    $mobsms_customer_key = json_decode($content, true);
    if (json_last_error() == JSON_ERROR_NONE) {
        if (isset($mobsms_customer_key['phone'])) {
            update_option('mobsms_admin_phone', $mobsms_customer_key['phone']);
        }
        update_option('mobsms_email', $email);
        mobsms_save_success_customer_config($email, $mobsms_customer_key['id'], $mobsms_customer_key['apiKey'], $mobsms_customer_key['token'], $mobsms_customer_key['appSecret']);
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('REG_SUCCESS'), 'SUCCESS');
        return;
    } else {
        update_option('mobsms_verify_customer', 'true');
        do_action('mobsms_show_message', MOBSMS_Messages::showMessage('ACCOUNT_EXISTS'), 'ERROR');
    }
}
    
        
function mobsms_save_success_customer_config($email, $id, $apiKey, $token, $appSecret)
{
    global $mobsms_db_queries;

    $user   = wp_get_current_user();
    update_option('mobsms_customer_key', $id);
    update_option('mobsms_api_key', $apiKey);
    update_option('mobsms_customer_token', $token);
    update_option('mobsms_app_secret', $appSecret);
    delete_option('mobsms_verify_customer');
}

function mobsms_icr()
{
    global $mobsms_db_queries;
    $email          = get_option('mobsms_email');
    $mobsms_customer_key    = get_option('mobsms_customer_key');
    if (! $email || ! $mobsms_customer_key || ! is_numeric(trim($mobsms_customer_key))) {
        return 0;
    } else {
        return 1;
    }
}
    
function mobsms_check_empty_or_null($value)
{
    if (! isset($value) || empty($value)) {
        return true;
    }
    return false;
}
