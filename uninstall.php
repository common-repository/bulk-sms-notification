<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}
    delete_option('mobsms_customer_key');
    delete_option('mobsms_api_key');
    delete_option('mobsms_customer_token');
    delete_option('mobsms_app_secret');
    delete_option('mobsms_email');
    delete_option('mobsms_verify_customer');
    delete_option('mobsms_phone_meta_key');
    delete_option('mobsms_admin_phone');
