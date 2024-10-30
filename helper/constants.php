<?php
    
class MOBSMS_Constants
{
    const HOST_NAME                 = "https://login.xecurify.com";
    const DEFAULT_CUSTOMER_KEY      = "16555";
    const DEFAULT_mobsms_api_key           = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
    
    function __construct()
    {
        $this->define_global();
    }

    function define_global()
    {
        global $mobsms_db_queries,$mobsms_utility,$mobsms_dirname;
        $mobsms_db_queries   = new MOBSMS_DATABASE();
        $mobsms_dirname      = plugin_dir_path(dirname(__FILE__));
    }
}
    new MOBSMS_Constants;
