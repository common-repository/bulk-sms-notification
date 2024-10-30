<?php
    global $mobsms_utility,$mobsms_dirname;

    $profile_url        = add_query_arg(array('page' => 'mobsms_account'       ), $_SERVER['REQUEST_URI']);
    $support_url        = add_query_arg(array('page' => 'mobsms_support'       ), $_SERVER['REQUEST_URI']);
    $configuration_url         = add_query_arg(array('page' => 'mobsms_menu'            ), $_SERVER['REQUEST_URI']);
    $send_bulk_sms_url      = add_query_arg(array('page' => 'mobsms_send_bulk_sms'     ), $_SERVER['REQUEST_URI']);
    $deep_url           = add_query_arg(array('page' => 'mobsms_deep'          ), $_SERVER['REQUEST_URI']);
    $report_url         = add_query_arg(array('page' => 'mobsms_report'            ), $_SERVER['REQUEST_URI']);
    $upgrade_url        = add_query_arg(array('page' => 'mobsms_upgrade'       ), $_SERVER['REQUEST_URI']);
    $logo_url           = plugin_dir_url(dirname(__FILE__)) . 'includes/images/miniorange_logo.png';
    $otp_recharge_url   = 'https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=otp_recharge_plan';
    $view_transaction_url = 'https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/viewtransactions';
    $active_tab         = sanitize_text_field($_GET['page']);
    include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'navbar.php';
