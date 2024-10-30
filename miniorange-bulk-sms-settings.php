<?php
/**
 * Plugin Name: Bulk SMS Notification
 * Description: Bulk SMS marketing plugin is a easy to use and simple in UI. This plugin supports user role based bulk sms notifications.
 * Version: 2.0.0
 * Author: miniOrange
 * Author URI: https://miniorange.com
 * License: GPL2
 */

    define('MOBSMS_HOST_NAME', 'https://login.xecurify.com');
    define('MOBSMS_VERSION', '2.0.0');
    define('MOBSMS_TEST_MODE', false);

class MOBSMS
{

    function __construct()
    {
        register_deactivation_hook(__FILE__, array( $this, 'mobsms_deactivate'           ));
        register_activation_hook(__FILE__, array( $this, 'mobsms_activate'             ));
        add_action('admin_menu', array( $this, 'mobsms_widget_menu'          ));
        add_action('admin_enqueue_scripts', array( $this, 'mobsms_settings_style'       ));
        add_action('admin_enqueue_scripts', array( $this, 'mobsms_settings_script'      ));
        add_action('mobsms_show_message', array( $this, 'mobsms_show_message'         ), 1, 2);
        add_action('admin_footer', array( $this, 'mobsms_feedback_request'     ));
        $this->mobsms_includes();
    }

    
    function mobsms_widget_menu()
    {
        $menu_slug =  'mobsms_menu';

        add_menu_page('miniOrange Bulk SMS', 'miniOrange Bulk SMS', 'activate_plugins', $menu_slug, array( $this, 'mobsms'), plugin_dir_url(__FILE__) . 'includes'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'miniorange_icon.png');

        add_submenu_page($menu_slug, 'miniOrange Bulk SMS', 'Configuration', 'administrator', 'mobsms_menu', array( $this, 'mobsms'), 1);
        add_submenu_page($menu_slug, 'miniOrange Bulk SMS', 'Send Bulk SMS', 'administrator', 'mobsms_send_bulk_sms', array( $this, 'mobsms'), 2);
        add_submenu_page($menu_slug, 'miniOrange Bulk SMS', 'Account', 'administrator', 'mobsms_account', array( $this, 'mobsms'), 3);
    }


    function mobsms()
    {
        include 'controllers'.DIRECTORY_SEPARATOR.'main_controller.php';
    }

    function mobsms_activate()
    {
        if (is_network_admin()) {
            wp_die(esc_html__('Site Admin can activate the plugin.'));
        }
        global $mobsms_db_queries;
        $mobsms_db_queries->plugin_activate();
    }

    function mobsms_deactivate()
    {
        global $wpdb;
        delete_site_option('mobsms_activated_time');
    }

    function mobsms_settings_style($hook)
    {
        if (strpos($hook, 'page_mobsms')) {
            wp_enqueue_style('mobsms_admin_settings_style', plugins_url('includes'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'style_settings.css', __FILE__));
            wp_enqueue_style('mobsms_admin_settings_datatable_style', plugins_url('includes'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'jquery.dataTables.min.css', __FILE__));
        }
    }

    function mobsms_settings_script($hook)
    {
        wp_enqueue_script('mobsms_admin_settings_script', plugins_url('includes'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'settings_page.js', __FILE__), array('jquery'));
        if (strpos($hook, 'page_mobsms')) {
            wp_enqueue_script('mobsms_admin_datatable_script', plugins_url('includes'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.dataTables.min.js', __FILE__), array('jquery'));
        }
    }

    function mobsms_includes()
    {
        require('controllers'.DIRECTORY_SEPARATOR.'ajax.php');
        require('database'.DIRECTORY_SEPARATOR.'database.php');
        require('api'.DIRECTORY_SEPARATOR.'api.php');
        require('helper'.DIRECTORY_SEPARATOR.'constants.php');
        require('helper'.DIRECTORY_SEPARATOR.'messages.php');
        require('handler'.DIRECTORY_SEPARATOR.'feedback_form.php');
    }

    function mobsms_show_message($content, $type)
    {
        if ($type=="CUSTOM_MESSAGE") {
              echo "<div class='overlay_not_JQ_success' id='pop_up_success'><p class='popup_text_not_JQ'>".esc_html($content)."</p> </div>";
            ?>
                <script type="text/javascript">
                 setTimeout(function () {
                    var element = document.getElementById("pop_up_success");
                       element.classList.toggle("overlay_not_JQ_success");
                       element.innerHTML = "";
                        }, 4000);
                        
                </script>
                <?php
        }
        if ($type=="NOTICE") {
               echo "<div class='overlay_not_JQ_error' id='pop_up_error'><p class='popup_text_not_JQ'>".esc_html($content)."</p> </div>";
            ?>
                <script type="text/javascript">
                 setTimeout(function () {
                    var element = document.getElementById("pop_up_error");
                       element.classList.toggle("overlay_not_JQ_error");
                       element.innerHTML = "";
                        }, 4000);
                        
                </script>
                <?php
        }
        if ($type=="ERROR") {
            echo "<div class='overlay_not_JQ_error' id='pop_up_error'><p class='popup_text_not_JQ'>".esc_html($content)."</p> </div>";
            ?>
                <script type="text/javascript">
                 setTimeout(function () {
                    var element = document.getElementById("pop_up_error");
                       element.classList.toggle("overlay_not_JQ_error");
                       element.innerHTML = "";
                        }, 4000);
                        
                </script>
                   <?php
        }
        if ($type=="SUCCESS") {
            echo "<div class='overlay_not_JQ_success' id='pop_up_success'><p class='popup_text_not_JQ'>".esc_html($content)."</p> </div>";
            ?>
                    <script type="text/javascript">
                     setTimeout(function () {
                        var element = document.getElementById("pop_up_success");
                           element.classList.toggle("overlay_not_JQ_success");
                           element.innerHTML = "";
                            }, 4000);
                            
                    </script>
            <?php
        }
    }

    function mobsms_feedback_request()
    {
        if ('plugins.php' != basename($_SERVER['PHP_SELF'])) {
            return;
        }
        global $mobsms_dirname;

        $email = get_option("mobsms_email");
        if (empty($email)) {
            $user = wp_get_current_user();
            $email = $user->user_email;
        }
        $imagepath=plugins_url('/includes/images/', __FILE__);
        wp_enqueue_style('wp-pointer');
        wp_enqueue_script('wp-pointer');
        wp_enqueue_script('utils');
        wp_enqueue_style('mobsms_admin_plugins_page_style', plugins_url('/includes/css/feedback_style.css?ver=4.8.60', __FILE__));

        include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'feedback_form.php';
    }
}new MOBSMS;