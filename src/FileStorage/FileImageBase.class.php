<?php

class FileImageBase extends FileBase
{
    public function isExists()
    {
        $bucket = $this->bucket;
        $sPath = $this->path;
        $path = Toolkit::getInstance()->fileStorage->bucket($bucket)->getPath();
        
        $sFullPath = $path.'/'.$sPath;
        
        return file_exists($sFullPath);
    }
    
    public function getImageUrl()
    {
        return Toolkit::getInstance()->fileStorage->bucket($this->bucket)->getUrl().'/'.$this->path;  
    }
    
    public function getThumbUrl($nWidth, $square = false, $only_name = false, $nHeight = 0)
    {
        $bucket = $this->bucket;
        $sPath = $this->path;
        $path = Toolkit::getInstance()->fileStorage->bucket($bucket)->getPath();
        //echo '<pre>asda:', $path,  print_r($this, true), '</pre>'; die();
        
        $sFullPath = $path.'/'.$sPath;
        
        
         try {
            if (file_exists($sFullPath)) {
                if ($square) {
                    $sNewName = str_replace(".".pathinfo($sPath, PATHINFO_EXTENSION), "_".$nWidth."x".$nWidth.".".pathinfo($sPath, PATHINFO_EXTENSION), $sPath);
                } else {
                    $sNewName = str_replace(".".pathinfo($sPath, PATHINFO_EXTENSION), "_".$nWidth.".".pathinfo($sPath, PATHINFO_EXTENSION), $sPath);
                }
                
                $sNewName = '.tmb/'.$sNewName;
                
                if (!file_exists($path.'/'.$sNewName)) {     
                    
                    if (!is_dir($path.'/.tmb'))
                    {
                        @mkdir($path.'/.tmb', 0777, true);
                    }
                    
                    $img = Toolkit::getInstance()->image->load($sFullPath);
                    if ($square) {
                        $img->resize($nWidth, $nHeight)->crop($nWidth, $nHeight, 'top', 'left');
                    } else {
                        $img->resize($nWidth, $nHeight);
                    }
                    
                    if (!is_dir(dirname($path.'/'.$sNewName)))
                        mkdir (dirname ($path.'/'.$sNewName), 0777, true);
                    
                    $img->quality(100)->save($path.'/'.$sNewName);
                    
                    //echo $path.'/'.$sNewName; die();
                }

                return $only_name ? $sNewName : Toolkit::getInstance()->fileStorage->bucket($bucket)->getUrl().'/'.$sNewName;
            }
        
         } catch (CException $o) {
             
             return false;
         }
        
        return false;
    }
    
    public function resizeToWidth($nWidth, $square = false, $only_name = false)
    {
        return $this->getThumbUrl($nWidth, $square, $only_name);
    }
    
    public function resizeToHeight($nHeight, $square = false, $only_name = false)
    {
        return $this->getThumbUrl(0, $square, $only_name, $nHeight);
    }
    
    public function resize($nWidth, $nHeight, $square = false, $only_name = false)
    {
        return  $this->getThumbUrl($nWidth, $square, $only_name, $nHeight);
    }
    
    public function getWidth()
    {
        $image_info = getimagesize(Toolkit::getInstance()->fileStorage->bucket($this->bucket)->getPath().'/'.$this->path);
        
        return intval($image_info[0]);
    }
    
    public function getHeight()
    {
        $image_info = getimagesize(Toolkit::getInstance()->fileStorage->bucket($this->bucket)->getPath().'/'.$this->path);
        
        return intval($image_info[1]);
    }
}