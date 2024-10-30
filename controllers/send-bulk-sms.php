<?php
global $mobsms_dirname;
global $wp_roles;
$roles = $wp_roles->get_names();
$is_registered = get_option('mobsms_customer_key');
$disabled='';
if (!$is_registered) {
    $disabled = 'disabled';
}
include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'send-bulk-sms.php';
