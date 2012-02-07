<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
#
# WEBCORE (c) 2007
#
#
// {{{ Class: tpl

class tpl
{
    /**
     * Configurations Variables
     */
    var $set_utf8       = false;
    var $mime           = 'text/html';
    var $_token_head    = '<!---HEAD-CONTENTS-->';
    var $_token_body    = '<!---MAIN-SECTION--->';
    var $title;
    var $tok;
    var $buffer;
    var $output_buffer;
    var $template_contents;
    var $includes_css;
    var $includes_js;
    var $includes_header;
    var $includes_custom_tokens;
    var $includes_custom_replaces;
    var $rptok;
    var $mainTemplate=false;
    /***********************************************
     * NON PUBLIC FUNCTIONS
     */
    function replace_tok($matches){
        return $this->tok[$matches[1]];
    }
    function replace_constant($matches){
        if (defined($matches[1]))
            return constant($matches[1]);
        else
            return $matches[0];
    }
    function replace_vars($matches)
    {
        eval("\$match=\${$matches[1]};");
        return $match;
    }
    function replace_repeat($matches)
    {
        $html=trim($matches[4]);
        $id=$matches[2];
        $ret='';
        for ($i=0;$i<count($this->rptok[$id]);$i++){
            $hasil=$html;
            for ($j=0;$j<count($this->rptok[$id][$i]);$j++)
                $hasil=str_replace('{='.($j).'}',$this->rptok[$id][$i][$j],$hasil);
            $ret.=$hasil;
        }
        return $ret;
    }
    function add_token($tokens,$replaces)
    {
        $this->includes_custom_tokens[]=$tokens;
        $this->includes_custom_replaces[]=$replaces;
    }
    function set()
    {
        // REPEAT TOKEN
        $this->template_contents=preg_replace_callback(
            "/({@)(.*?)(})(.*?)({\/@)(.*?)(})/ssss"
                ,array($this,"replace_repeat"), $this->template_contents);
        // CONSTANT TOKEN
        $this->template_contents=preg_replace_callback("/{%(.*?)}/"
                ,array($this,"replace_constant"), $this->template_contents);
        // VARIABLE TOKEN
        $this->template_contents=preg_replace_callback("/{\\$(.*?)}/"
                ,array($this,"replace_vars"), $this->template_contents);
        // NATIVES TOKEN
        $this->template_contents=preg_replace_callback("/{=(.*?)}/"
                ,array($this,'replace_tok'), $this->template_contents);
        // BODY TOKEN
        $this->add_token($this->_token_body,$this->buffer);

        // TITLE DEFINITION
        $tmp_head=  "<title>".(trim($this->title)?(htmlspecialchars($this->title).' | '):'')._web_title."</title>";
        // INCLUDED CSS
        for ($i=0;$i<count($this->includes_css);$i++)
            $tmp_head.= "<link href=\"{$this->includes_css[$i]}\" ".
                        "rel=\"stylesheet\" type=\"text/css\" />";
        // INCLUDED JAVASCRIPT
        for ($i=0;$i<count($this->includes_js);$i++)
            $tmp_head.= "<script type=\"text/javascript\" src=\"{$this->includes_js[$i]}\">".
                        "</script>";

        // INCLUDE HTML HEAD (META, ETC)
        for ($i=0;$i<count($this->includes_header);$i++)
            $tmp_head.= $this->includes_header[$i];

        // HEAD TOKEN
        $this->add_token($this->_token_head,$tmp_head);

        // SET BUFFER
        $this->output_buffer    =   str_replace(
                                        $this->includes_custom_tokens,
                                        $this->includes_custom_replaces,
                                        $this->template_contents
                                    );
    }
    /***********************************************
     * CONSTRUCTOR
     */
    function tpl($tplname=false)
    {
        if ($tplname)
            $this->init($tplname);
    }
    /***********************************************
     * INIT FUNCTION
     */
    function init($tplname){
        $template_filename=_dir_tpl."/{$tplname}.html";
        if ($tplname==_default_tpl)
            $this->mainTemplate=true;
        // Get HTML Template
        if (file_exists($template_filename)){
            $this->template_contents=file_get_contents($template_filename);
            $this->template_contents=trimAllWhiteSpace($this->template_contents);
         }
        else
            return false;
        return true;
    }
    /***********************************************
     * PUBLIC USING FUNCTIONS
     */
    function heading($title=false,$str=false){
        if ($title) $this->title=$title;
        if ($str) $this->repeat_token('pageheading',array(htmlspecialchars($str)));
    }
    function activePage($active){
        $this->token('active('.$active.')','class="active"');
    }
    function css($url)
    {
        $this->includes_css[]=$url;
    }
    function addheader($html){
        $this->includes_header[]=$html;
    }
    function addmeta($name,$content){
        $this->addheader("<meta name=\"".htmlspecialchars($name)."\" content=\"".htmlspecialchars($content)."\" />");
    }
    function js($url)
    {
        $this->includes_js[]=$url;
    }
    function token($id,$isi){
        $this->tok[$id]=$isi;
    }
    function repeat_token($id,$isi){
        $this->rptok[$id][]=$isi;
    }
    function start()
    {
        ob_start();
    }
    function stop()
    {
        $this->buffer=ob_get_contents();
        ob_end_clean();
        $this->set();
    }
    function end()
    {
        $this->stop();
        $this->flush();
    }
    function exec(){
        $this->start();
        return $this->stop();
    }
    function get_contents()
    {
        return $this->output_buffer;
    }
    function flush()
    {
        header('Content-Type: '.($this->mime).'; charset="utf-8"');
        header('Content-Length: '.strlen($this->output_buffer));
        echo $this->output_buffer;
    }
}

// }}}

?>