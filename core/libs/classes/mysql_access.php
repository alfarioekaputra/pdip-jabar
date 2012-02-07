<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#

// {{{ Class: mysql_access

class mysql_access
{
    /**
     * Configurations Variables ( Edit as u like )
     */
    var $handle_conn;
    var $handle_db;
    
    /**
     * Constructor Function
     * Usage : 
     *    > $db = new mysql_access($_CONF['db']);
     */
    function mysql_access()
    {
        $this->handle_conn  =   @mysql_connect(_db_host,_db_user,_db_pass);
        if (!$this->handle_conn) return false;
        $this->handle_db    =   mysql_select_db(_db_name,$this->handle_conn);
        if (!$this->handle_db) return false;
        //$this->query("SET NAMES utf8");
    }
    function query($str)
    {
        return @mysql_query($str,$this->handle_conn);
    }
    function sql($str)
    {
        if ($result=$this->query($str))
			return @mysql_fetch_assoc($result);
		else 
			return false;
    }
    function all($str){
		if ($result=$this->query($str)){
			while ($RETT=@mysql_fetch_assoc($result)){
			    $RET[]=$RETT;
			}
			return $RET;
		}
		else
			return false;
	}
    function num($str)
    {
		return @mysql_num_rows($this->query($str));
	}
    function error()
    {
		return mysql_error();
	}
	function err()
    {
		return mysql_error();
	}
    function slash($str)
    {
		return mysql_real_escape_string($str);
	}
}

// }}}

?>