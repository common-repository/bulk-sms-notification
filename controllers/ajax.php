<?php
class MOBSMS_Ajax
{
    function __construct()
    {
        add_action('admin_init', array( $this, 'mobsms_ajax' ));
    }

    function mobsms_ajax()
    {
        add_action('wp_ajax_mobsms_action', array($this,'mobsms_action'));
    }

    function mobsms_action()
    {
        global $mobsms_db_queries;
        $option = sanitize_text_field($_POST['option']);  
        switch ($option) {
            case 'save_phone_meta_key':
                $this->save_phone_meta_key();
                break;
            case 'send_sms':
                $this->send_sms();
                break;
        }
    }

    function save_phone_meta_key()
    {
        if (!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mobsms_nonce')) {
            wp_send_json("ERROR");
        } else {
            $key = sanitize_text_field($_POST['key']);
            if (!empty($key)) {
                update_option('mobsms_phone_meta_key', $key);
            } else {
                wp_send_json("FAIL");
            }
        }
            wp_send_json("SUCCESS");
    }
    function send_sms()
    {
        if (!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'mobsms_nonce')) {
            wp_send_json("ERROR");
        } else {
            $phone_key = get_option("mobsms_phone_meta_key");
            if (!$phone_key) {
                wp_send_json('INVALID_KEY');
            }

            $role = sanitize_text_field($_POST['role']);
            if (empty($role)) {
                wp_send_json('INVALID_USER_ROLL');
            }
            $flag = 0;

            $nicename='user_nicename';
            if(get_user_meta(get_current_user_id(),'user_nicename')==null)
            $nicename='nickname';

            if ($role == 'all') {
                $arg = array(
                 'orderby' => $nicename,
                 'order' => 'ASC'
                );
            } else {
                $arg = array(
                 'role' => $role,
                 'orderby' => $nicename,
                 'order' => 'ASC'
                );
            }
             $users     = get_users($arg);
             $message   = sanitize_text_field($_POST['sms']);
             $error = 'none';
           $phone_numbers ="";
           $mobsms_api = new MOBSMS_Api();  

           foreach ($users as $key => $value) {
        
                $phone = get_user_meta(strval($value->ID), $phone_key);
           
              if (!empty($phone[0])) {
                
                $phone_numbers .= $phone[0].",";
           
              }
           }
    
 
           $phone_numbers = rtrim($phone_numbers, ',');
           $result = $mobsms_api->send_sms_notify($phone_numbers, $message);
           
           $result = json_decode($result);
           error_log(print_r($result,true));
           if ($result->status == 'ERROR') {
               $flag = 1;
           }

            if ($flag) {
                wp_send_json('SMS_EXCEED');
            } else {
                wp_send_json('SUCCESS');
            }
        }
    }
}new MOBSMS_Ajax;
