<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#----------- MAIN REQUEST ----------------
#
    # File Konfigurasi
    include_once "./configuration.php";

    # Core Configuration Rebuild
    define('_dns',          _cfg_dns);
    define("_base_uri",     _cfg_base_uri);
    define('_relative',     _base_uri.'/');
    define('_www',          'http://'._dns._relative);

    # MAIN URL (*)
    if (_cfg_support_rewrite)
        define('_net',        _base_uri.'');
    else
        define('_net',        _base_uri.'/main.php');
    define('_shr',            _relative.'_stock');
    define('_www_net',        'http://'._dns._net);

    # DIREKTORI (*)
    define('_rootdir',      getcwd());
    define('_dir_includes', _rootdir.'/core');
    define('_dir_libs',     _dir_includes.'/libs');
    define('_dir_mods',     _dir_includes.'/mods');
    define('_dir_tpl',      _dir_includes.'/tpl');
    define('_dir_res',      _dir_includes.'/res');
    define('_dir_tmp',      _rootdir.'/tmp');
    
    # DATABASE PROFILE
    define('_p',     				_dbprefix);   # TABLE PREFIX
    define('_dbp',     			_dbprefix);   # TABLE PREFIX

#----------- START REQUEST ----------------
    if (!isset($_SERVER["PATH_INFO"])&isset($_SERVER["REQUEST_URI"])){
        $_SERVER["PATH_INFO"]=substr($_SERVER["REQUEST_URI"],strlen(_net));
    }
    # If no pathname
    if (!isset($_SERVER["PATH_INFO"])){
        header('location:'._relative);
        exit();
    }
    
    # Start Session
    session_name(_session_id);
    session_start();

    # Parsing The Path
    $__PATH=explode('/',substr($_SERVER["PATH_INFO"],1));
    $_SERVER['MODULE']=$__PATH[0];

    # Get The Module Filename
    if ($_SERVER['MODULE'])
        $_SERVER['MODULE_FILENAME']=_dir_mods."/{$_SERVER['MODULE']}.php";
    else{
            $_SERVER['MODULE']='index';
            $_SERVER['MODULE_FILENAME']=_dir_mods."/{$_SERVER['MODULE']}.php";
    }

    # Check Exists Module File
    if (!file_exists($_SERVER['MODULE_FILENAME'])){
        header('location:'._relative);
        exit();
    }

    # Build Query Variables
    for ($i=1;$i<count($__PATH);$i+=2)
    {
        if ($__PATH[$i]){
            $_QUERY_VAR=$__PATH[$i];
            $_QUERY[$_QUERY_VAR]=addslashes($__PATH[$i+1]);
            $_SERVER['QUERY'][$_QUERY_VAR]=& $_QUERY[$_QUERY_VAR];
        }
    }

    unset($_QUERY_VAR);

    # Load Standard Functions Library
    require_once _dir_libs.'/index.php';

    # Now Load Template Module
    $_SERVER['TRIM_OUTPUT'] = false;
    $_SERVER['MAINMENU_SEL']= '';
    $_TPL=new_class('tpl');
    $_TPL->init('main');

    # BIND OUTPUT BUFFER
    ob_start();
    require_once $_SERVER['MODULE_FILENAME'];
    $___FLUSH=ob_get_contents();
    if ($_SERVER['TRIM_OUTPUT']){
    	$___FLUSH=trimAllWhiteSpace($___FLUSH);
    }
    ob_end_clean();

    # Load Rebuilder Template...
    require_once _dir_tpl."/rebuilder.php";

    # FLUSH INTO MAIN TEMPLATE
    header('content-type:text/html;charset="utf-8"');
    $_TPL->start();
    echo $___FLUSH;
    $_TPL->end();
    # (c)XAXMXAXRXUXLXLXZX
    
    # statistik
    if (!$_SESSION['is_first_time']){
    	$_SESSION['is_first_time']=1;
    	$db->query("INSERT INTO `"._dbp."_statistik` VALUES (NULL,'".date("Y-m-d")."','{$_SERVER['REMOTE_ADDR']}','".time()."',1)");
    }
    else{
    	$db->query("INSERT INTO `"._dbp."_statistik` VALUES (NULL,'".date("Y-m-d")."','{$_SERVER['REMOTE_ADDR']}','".time()."',0)");
    }
?>
