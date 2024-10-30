<?php
class MoblcFeedbackHandler
{
    function __construct()
    {
        add_action('admin_init', array($this, 'mobsms_feedback_actions'));
    }

    function mobsms_feedback_actions()
    {
        if (current_user_can('manage_options') && isset($_POST['option'])) {
            switch (sanitize_text_field($_REQUEST['option'])) {
                case "mobsms_skip_feedback":
                case "mobsms_feedback":
                    $this->handle_feedback();
                    break;
            }
        }
    }


    function handle_feedback()
    {
        if (MOBSMS_TEST_MODE) {
            deactivate_plugins(dirname(dirname(__FILE__))."\\miniorange-bulk-sms-settings.php");
                return;
        }
        
        $user = wp_get_current_user();
        $feedback_option = sanitize_text_field($_POST['option']);
        $message = 'Plugin Deactivated';

        $deactivate_reason_message = array_key_exists('mobsms_query_feedback', $_POST) ? sanitize_text_field($_POST['mobsms_query_feedback']) : false;
        $activation_date = get_site_option('mobsms_activated_time');
        $current_date = time();
        $diff = $activation_date - $current_date;
        if ($activation_date == false) {
            $days = 'NA';
        } else {
            $days = abs(round($diff / 86400));
        }

        $reply_required = '';
        if (isset($_POST['get_reply'])) {
            $reply_required = sanitize_text_field($_POST['get_reply']);
        }
        if (empty($reply_required)) {
            $reply_required = "don't reply";
            $message .= ' &nbsp; [Reply:<b style="color:red";>' . $reply_required . '</b>,';
        } else {
            $reply_required = "yes";
            $message .= '[Reply:' . $reply_required . ',';
        }

        $message .= 'D:' . $days . ',';

        $message .= ', Feedback : ' . $deactivate_reason_message . '';

        if (isset($_POST['rate'])) {
            $rate_value = sanitize_text_field($_POST['rate']);
        } else {
            $rate_value = "--";
        }
        $message .= ', [Rating :' . $rate_value . ']';

        $email = isset($_POST['query_mail'])? sanitize_email($_POST['query_mail']): '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = get_option('mobsms_email');
            if (empty($email)) {
                $email = $user->user_email;
            }
        }
        $phone = get_option('mobsms_admin_phone');
        if (!$phone) {
            $phone = '';
        }
        $feedback_reasons = new MOBSMS_Api();
        if (!is_null($feedback_reasons)) {
            if (!$feedback_reasons->mobsms_is_curl_installed()) {
                deactivate_plugins(dirname(dirname(__FILE__))."\\miniorange-bulk-sms-settings.php");
                wp_redirect('plugins.php');
            } else {
                $submited = json_decode($feedback_reasons->send_email_alert($email, $phone, $message, $feedback_option), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                        do_action('mobsms_show_message', $submited['message'], 'ERROR');
                    } else {
                        if ($submited == false) {
                            do_action('mobsms_show_message', 'Error while submitting the query.', 'ERROR');
                        }
                    }
                }

                deactivate_plugins(dirname(dirname(__FILE__))."\\miniorange-bulk-sms-settings.php");
                do_action('mobsms_show_message', 'Thank you for the feedback.', 'SUCCESS');
            }
        }
    }
}new MoblcFeedbackHandler();
