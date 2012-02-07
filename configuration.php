<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#----------- MAIN CONFIGURATION ----------------
#
    #
    # MySQL
    define('_db_name',      'pdi'); 			# NAMA DATABASE
    define('_db_host',      'localhost'); # HOST DATABASE
    define('_db_user',      'root');      # USERNAME DATABASE
    define('_db_pass',      'password');    			# PASSWORD DATABASE
    define('_dbprefix',     'pdip');      # TABLE PREFIX

    ## Network
    #
    # NAMA DOMAIN
    define('_cfg_dns',          $_SERVER["SERVER_NAME"]);
    
    #
    # BASE URL (KOSONGKAN BILA DISIMPAN DI ROOT)
    define('_cfg_base_uri',     '/pdip');
    
    #
    # WEB TITLE
    define('_default_source','pdiperjuangan-jabar.com');
    define('_web_title',     'PDI Perjuangan Jawa Barat');
    define('_session_id',    '__pdip_jabar');
    
    #
    # SOCIAL NETWORKING
    define('_twitter_username',    'pdip_jabar');
    define('_facebook_fan_url',    'http://www.facebook.com/pages/PDI-Perjuangan-Jawa-Barat/167580603261394');
    
    #
    # .htaccess Support mod_rewrite (Untuk Apache HTTPD):
    # Set 0 = Tidak support, 1 = Support
    define('_cfg_support_rewrite', 0);

#######
####### Jangan di ubah:
#######
    # FIX REQUEST JIKA magic_quotes_gpc=Off dalam konfigurasi php.ini
    if (!get_magic_quotes_gpc()) {
        function slashes_mime_magic($str){
            $str=str_replace("'","\\'",$str);
            return $str;
        }
        $_GET    = array_map('slashes_mime_magic', $_GET);
        $_POST   = array_map('slashes_mime_magic', $_POST);
        $_COOKIE = array_map('slashes_mime_magic', $_COOKIE);
    }
    
?>
