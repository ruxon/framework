<?php

class AssetManager extends ToolkitBase
{
    protected $_published = array();
    
    public function publish($path, $level = -1)
    {
        $assetPath = $this->generatePath($path);
        
        if (isset($this->_published[$path]))
        {
            return $this->_published[$path];
        }
        
        if (is_dir($this->getBasePath().'/'.$assetPath) && ($this->fastCheck || FileHelper::md5Directory($this->getBasePath().'/'.$assetPath) == FileHelper::md5Directory($path)))
        {
            return $this->getBaseUrl().'/'.$assetPath;
        } else {
            if (!is_dir($this->getBasePath().'/'.$assetPath))
                mkdir($this->getBasePath().'/'.$assetPath, 0777, true);
            
            FileHelper::copyDirectory($path, $this->getBasePath().'/'.$assetPath, 0777, $level);
            
            $this->_published[$path] = $this->getBaseUrl().'/'.$assetPath;
            
            return $this->_published[$path];
        }
    }
    
    public function getBasePath()
    {
        return RX_PATH.'/assets';
    }
    
    public function getBaseUrl()
    {
        return RX_URL.'/assets';
    }
    
    public function generatePath($file)
    {
        if (is_file($file))
        {
            $string = dirname($file).filemtime($file);
        }
        else
        {
            $string = $file.filemtime($file);
        }
        
        return $this->hash($string);
    }
    
    protected function hash($string)
    {
        return md5($string);
    }
}