<?php

class MOBSMS_Api
{

    public static function wp_remote_post($url, $args = array())
    {
        $response = wp_remote_post($url, $args);
        if (!is_wp_error($response)) {
            return $response['body'];
        } else {
            $message = 'Please enable curl extension. <a href="admin.php?page=mo_2fa_troubleshooting">Click here</a> for the steps to enable curl.';

            return json_encode(array( "status" => 'ERROR', "message" => $message ));
        }
    }

    public static function make_curl_call($url, $fields, $http_header_array = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic"))
    {
        if (gettype($fields) !== 'string') {
            $fields = json_encode($fields);
        }

        $args = array(
            'method' => 'POST',
            'body' => $fields,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $http_header_array
        );

        $response = self::wp_remote_post($url, $args);
        return $response;
    }


    public static function get_customer_key($email, $password)
    {
        $url    = MOBSMS_Constants::HOST_NAME. "/moas/rest/customer/key";
        $fields = array (
                    'email'     => $email,
                    'password'  => $password
                );
        $json       = json_encode($fields);
        $response   = self::make_curl_call($url, $json);
        return $response;
    }

    function check_customer($email)
    {
        $url    = MOBSMS_Constants::HOST_NAME . "/moas/rest/customer/check-if-exists";
        $fields = array(
                    'email'     => $email,
                );
        $json     = json_encode($fields);
        $response = self::make_curl_call($url, $json);
        return $response;
    }

    public static function create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = '')
    {
        $url = MOBSMS_Constants::HOST_NAME . '/moas/rest/customer/add';
        $fields = array (
            'companyName'    => $company,
            'areaOfInterest' => 'WP Bulk SMS',
            'firstname'      => $first_name,
            'lastname'       => $last_name,
            'email'          => $email,
            'phone'          => $phone,
            'password'       => $password
        );
        $json = json_encode($fields);
        $response = self::make_curl_call($url, $json);
        return $response;
    }

    function submit_contact_us($q_email, $q_phone, $query)
    {
        $current_user = wp_get_current_user();
        $url          = MOBSMS_Constants::HOST_NAME . "/moas/rest/customer/contact-us";
        global $mowafutility;
        $query = '[WordPress Bulk SMS Plugin: -V '.MOBSMS_VERSION.']: ' . $query;
        
        $fields = array(
                    'firstName' => $current_user->user_firstname,
                    'lastName'  => $current_user->user_lastname,
                    'company'   => $_SERVER['SERVER_NAME'],
                    'email'     => $q_email,
                    'ccEmail'   => '2fasupport@xecurify.com',
                    'phone'     => $q_phone,
                    'query'     => $query
                );
        $field_string = json_encode($fields);
        $response = self::make_curl_call($url, $field_string);
        return $response;
    }

    function send_email_alert($email, $phone, $message, $feedback_option)
    {
        global $user;
        $url = MOBSMS_Constants::HOST_NAME . '/moas/api/notify/send';
        $mobsms_customer_key = MOBSMS_Constants::DEFAULT_CUSTOMER_KEY;
        $apiKey      = MOBSMS_Constants::DEFAULT_mobsms_api_key;
        $fromEmail   = 'no-reply@xecurify.com';
        if ($feedback_option == 'mobsms_skip_feedback') {
            $subject = "Deactivate [Feedback Skipped]: WordPress Bulk SMS";
        } elseif ($feedback_option == 'mobsms_feedback') {
            $subject = "Feedback: WordPress Bulk SMS - ". $email;
        }

        $user  = wp_get_current_user();
        $query = '[WordPress Bulk SMS Plugin: - V '.MOBSMS_VERSION.']: ' . $message;
        if (get_option('mobsms_customer_key')) {
            $register = 1;
        } else {
            $register = 0;
        }

        $phone_meta_key = get_option('mobsms_phone_meta_key');
        if ($phone_meta_key) {
            $key  = $phone_meta_key;
        } else {
            $key = 'Phone Number';
        }
        $content='<div >Hello, <br><br>Ticket ID : R'.rand(100, 999).$register.'<br><br>First Name :'.$user->user_firstname.'<br><br>Last  Name :'.$user->user_lastname.'   <br><br>Company :<a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>'.$key.' :'.$phone.'<br><br>Email :<a href="mailto:'.$email.'" target="_blank">'.$email.'</a><br><br>Query :'.$query.'</div>';

        $fields = array(
            'customerKey'   => $mobsms_customer_key,
            'sendEmail'     => true,
            'email'         => array(
                'customerKey'   => $mobsms_customer_key,
                'fromEmail'     => $fromEmail,
                'fromName'      => 'Xecurify',
                'toEmail'       => '2fasupport@xecurify.com',
                'toName'        => '2fasupport@xecurify.com',
                'subject'       => $subject,
                'content'       => $content
            ),
        );
        $field_string = json_encode($fields);
        $authHeader   = $this->createAuthHeader($mobsms_customer_key, $apiKey);
        $response     = self::make_curl_call($url, $field_string, $authHeader);
        return $response;
    }

    function createAuthHeader($mobsms_customer_key, $apiKey)
    {
        $currentTimestampInMillis = round(microtime(true) * 1000);
        $currentTimestampInMillis = number_format($currentTimestampInMillis, 0, '', '');

        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash   = $mobsms_customer_key . $currentTimestampInMillis . $apiKey;
        ;
        $hashValue      = hash("sha512", $stringToHash);

        $headers = array(
            "Content-Type"  => "application/json",
            "Customer-Key"  => $mobsms_customer_key,
            "Timestamp"     => $currentTimestampInMillis,
            "Authorization" => $hashValue
        );

        return $headers;
    }

    function send_sms_notify($phone, $message)
    {
        $url = MOBSMS_Constants::HOST_NAME . '/moas/api/plugin/gateway/send';
        $customerKey = get_option('mobsms_customer_key');
        $apiKey      = get_option('mobsms_api_key');
        $fields      = [
                   'customerKey' => $customerKey,
                   'sendEmail' => false,
                   'sendSMS' => true,
                   'apiKey' => $apiKey,
                   'sms' => [
                       'customerKey' => $customerKey,
                       'phoneNumber' => $phone,
                       'message' => $message
                   ]
               ];
 
        $field_string = json_encode($fields);
        $authHeader   = $this->createAuthHeader($customerKey, $apiKey);
        $response     = self::make_curl_call($url, $field_string, $authHeader);
        return $response;
    }

    function mobsms_is_curl_installed()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return 1;
        } else {
            return 0;
        }
    }
}
