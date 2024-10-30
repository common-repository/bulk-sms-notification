<?php

    global $mobsms_utility,$mobsms_dirname;
    $controller = $mobsms_dirname . 'controllers'.DIRECTORY_SEPARATOR;

    
if (current_user_can('administrator')) {
    include $controller      . 'navbar.php';
    echo '<table class="mobsms_main_table" style="width:100%;"><tr><td class="mo_wpns_send_sms_layout" style="width:60%;">';
    if (isset($_GET[ 'page' ])) {
        $page = sanitize_text_field($_GET[ 'page' ]);
        switch ($page) {
            case 'mobsms_account':
                include $controller . 'account.php';
                break;
            case 'mobsms_menu':
                include $controller . 'configuration.php';
                break;
            case 'mobsms_send_bulk_sms':
                include $controller . 'send-bulk-sms.php';
                break;
        }
        echo '</td><td style="width=2%;"></td><td class="mo_wpns_support_layout" style="width:38%;">';
    }
    include $controller . 'support.php';
    echo '</td></tr></table>';
}
