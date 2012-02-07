<?php
# Security
if (!defined('_IN_PHP')){ header("HTTP/1.1 403 Forbidden"); exit(); }
#
# Auto Images Class...
#

// {{{ Class: autoimg

class autoimg
{
    var $im;
    var $w;
    var $h;
    var $mime;
    function autoimg($filename,$isstring=false)
    {
        if (extension_loaded('gd')){
        		if ($isstring){
						  $tmpfname = tempnam(_dir_tmp, "imtmp");
							$handle = fopen($tmpfname, "w");
							fwrite($handle, $filename);
							fclose($handle);
							$success = true;
							$image_info=getimagesize($tmpfname);
	            list($this->w,$this->h)=$image_info;
	            $this->mime=$image_info['mime'];
	            if ($this->im=@imagecreatefrompng($tmpfname)){}
	            elseif ($this->im=@imagecreatefromjpeg($tmpfname)){}
	            elseif ($this->im=@imagecreatefromgif($tmpfname)){}
	            else $success = false;
							@unlink($tmpfname);
							if (!$success) return false;
        		}
        		else{
	            $image_info=getimagesize($filename);
	            list($this->w,$this->h)=$image_info;
	            $this->mime=$image_info['mime'];
	            
	            if ($this->im=@imagecreatefrompng($filename)){}
	            elseif ($this->im=@imagecreatefromjpeg($filename)){}
	            elseif ($this->im=@imagecreatefromgif($filename)){}
	            else
	              return false;
	          }
        }
        else
            return false;
    }
    function resize($width,$height,$proportional=true){
        if ($proportional){
            // Proportional Size
            $w_tmp	= $width/$this->w;
            $h_tmp	= $height/$this->h;

            $w2_tmp	= $h_tmp * $this->w;
            $h2_tmp	= $w_tmp * $this->h;
            $b			= $w_tmp;
            
            if ($proportional==2){
	            if ($h2_tmp>$height)
	            	$b		= $h_tmp;
	          }
	          else{
	          	if ($h2_tmp<$height)
	            	$b		= $h_tmp;
	          }
            
            $w2=$this->w*$b;
            $h2=$this->h*$b;

            // Proportional Position
            $x2=floor(($w2-$width)/2);
            $y2=floor(($h2-$height)/2);
        }
        else{
            $w2=$width;
            $h2=$height;
            $x2=0;
            $y2=0;
        }
        $im=imagecreatetruecolor($width,$height);
        $col=imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $col);
        if (function_exists('imagecopyresampled'))
            imagecopyresampled($im, $this->im, 0-$x2, 0-$y2, 0 , 0, $w2, $h2, $this->w, $this->h);
        else
            imagecopyresized($im, $this->im, 0-$x2, 0-$y2, 0 , 0, $w2, $h2, $this->w, $this->h);
        
        $this->im=$im;
        $this->w=$width;
        $this->h=$height;
        return $this->im;
    }
    function buf($quality=80){
      ob_start();
      imagejpeg($this->im,false,$quality);
      $obc=ob_get_contents();
      ob_end_clean();
      return $obc;
    }
    function frame($framename){
        $fileframe=_dir_res."/{$framename}";
        $image_info=getimagesize($fileframe);
        list($w,$h)=$image_info;
        if 		 ($im=@imagecreatefrompng($fileframe)){}
        elseif ($im=@imagecreatefromjpeg($fileframe)){}
        elseif ($im=@imagecreatefromgif($fileframe)){}
        else    return false;
        imagecopyresized($this->im, $im, 0, 0, 0 , 0,$this->w, $this->h, $w,$h);
    }
}

// }}}

?>