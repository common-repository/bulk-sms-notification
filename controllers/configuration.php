<?php

global $mobsms_dirname;
$phone_meta_key = get_option('mobsms_phone_meta_key');
    $phone_meta_key_list = get_user_meta(get_current_user_id());
    $size = 0;
    $keys=null;
    $values=null;
foreach ($phone_meta_key_list as $key => $value) {
    if ((is_numeric($value[0]) || preg_match('/^(\+)[0-9]+$/', $value[0])) && strlen($value[0]) > 5) {
        if ($keys == null) {
            $keys = array($key);
            $values = array($value[0]);
        } else {
            array_push($keys, $key);
            array_push($values, $value[0]);
        }
        $size++;
    }
}
include $mobsms_dirname . 'views'.DIRECTORY_SEPARATOR.'configuration.php';
