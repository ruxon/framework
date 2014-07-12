<?php

class FileBase
{
    protected $name;
    
    protected $bucket;
    
    protected $extension;
    
    protected $path;
    
    protected $fullpath;
    
    protected $bytesize;
    
    public function __construct($name, $bucket = 'files') 
    {
        $this->path = $name;
        $this->bucket = $bucket;
    }
    
    public function getName()
    {
        
    }
    
    public function getExtension()
    {
        
    }
    
    public function getFullPath()
    {
        
    }
    
    public function getFileUrl()
    {
        return Toolkit::getInstance()->fileStorage->bucket($this->bucket)->getUrl().'/'.$this->path;
    }
    
    public function delete()
    {
        
    }
    
    public function copy()
    {
        
    }
    
    public function move()
    {
        
    }
}