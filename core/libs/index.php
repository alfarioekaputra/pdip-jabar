<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
# WEBCORE (c)
#
#
    # Function for Load Class...
    function &new_class(){
        $arg = func_get_args();
        $classname=$arg[0];
        $classfilename=_dir_libs."/classes/{$classname}.php";
        if (!file_exists($classfilename))
            return false;
        else{
            if (!class_exists($classname))
                include_once($classfilename);

            for ($i=1;$i<count($arg);$i++){
                $args.="\$arg[{$i}]";
                if ($i<count($arg)-1)
                    $args.=",";
            }

            if ($args){
                eval("\$P=new {$classname}({$args});");
                return $P;
            }
            else
                return new $classname();
        }
    }
    # MySQL Connection
    $db=&new_class("mysql_access");

    # Main Libraries
    include_once _dir_libs."/functions/strings.php";
    include_once _dir_libs."/functions/datetime.php";
    include_once _dir_libs."/functions/modules.php";
?>