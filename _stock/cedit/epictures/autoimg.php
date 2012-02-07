<?php
#
# Auto Images Class...
#
# WEBCORE (c) 2006 Ahmad Amarullah <amarullz@yahoo.com>
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
        			$is_success=false;
        			if (function_exists("imagecreatefromstring")){
	        			if ($this->im=imagecreatefromstring($filename)){
		        			$this->w=imagesx($this->im);
		        			$this->h=imagesy($this->im);
		        			$is_success=true;
		        		}
	        		}
	        		if (!$is_success){
							  $tmpfname = tempnam(_dir_tmp, "imtmp");
								$handle = fopen($tmpfname, "w");
								fwrite($handle, $filename);
								fclose($handle);
								$this->autoimg($tmpfname);
								unlink($tmpfname);
	        		}
        		}
        		else{
	            $image_info=getimagesize($filename);
	            list($this->w,$this->h)=$image_info;
	            $this->mime=$image_info['mime'];
	            
	            if ($this->im=@imagecreatefrompng($filename)){}
	            else if ($this->im=@imagecreatefromjpeg($filename)){}
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
            if ($width<$height)
                $b=(($this->w<$this->h)?($width/$this->w):($width/$this->h));
            else
                $b=(($this->w>$this->h)?($height/$this->w):($height/$this->h));
            
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
        $h2=floor($h2);
        $w2=floor($w2);
        $im=imagecreatetruecolor($w2,$h2);
        if (function_exists('imagecopyresampled'))
            imagecopyresampled($im, $this->im, 0, 0, 0 , 0, $w2, $h2, $this->w, $this->h);
        else
            imagecopyresized($im, $this->im, 0, 0, 0 , 0, $w2, $h2, $this->w, $this->h);
        
        $this->im=$im;
        $this->w=$width;
        $this->h=$height;
        return $this->im;
    }
    function buf(){
      ob_start();
      imagejpeg($this->im,false,80);
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