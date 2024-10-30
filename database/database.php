<?php
class MOBSMS_DATABASE
{
    function __construct()
    {
        global $wpdb;
    }

    function plugin_activate()
    {
        add_site_option('mobsms_activated_time', time());
    }
}
