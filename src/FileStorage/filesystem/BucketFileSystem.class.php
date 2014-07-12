<?php

class BucketFileSystem extends BucketBase
{
    public $filePermission = 0777;
    
    public $basePath;
    
    public $baseUrl;
    
    public function __construct($basePath, $baseUrl, $filePermission = 0777) 
    {
        $this->basePath = str_replace("~", RX_PATH, $basePath);
        $this->baseUrl  = str_replace("~", RX_URL, $baseUrl); 
        $this->filePermission = $filePermission;
    }
    
    public function saveUploadedFile($model, $attribute, $fileName = null, $obj = false, $returnObj = false)
    {
        $result = UploadedFile::getInstance($model, $attribute);
        
        if ($fileName == null)
        {
            $fileName = time().'.'.$result->getExtension();
        }
        
        if (strpos($fileName, "/") !== false)
        {
            $fileSubPath = substr($fileName, 0, strrpos($fileName, "/"));
            
            if (!is_dir($this->basePath.'/'.$fileSubPath)) 
            {
                mkdir($this->basePath.'/'.$fileSubPath, $this->filePermission, true);
            }
        }

        if ($result->saveAs($this->basePath."/".$fileName))
        {
            chmod($this->basePath."/".$fileName, $this->filePermission);
        }
        
        return $returnObj ? $returnObj : $fileName;
    }
    
    public function saveFile($fileName, $content, $returnObj = false)
    {
        if (strpos($fileName, "/") !== false)
        {
            $fileSubPath = substr($fileName, 0, strrpos($fileName, "/"));
            
            if (!is_dir($this->basePath.'/'.$fileSubPath)) 
            {
                mkdir($this->basePath.'/'.$fileSubPath, $this->filePermission, true);
            }
        }
        
        $result = file_put_contents($this->basePath."/".$fileName, $content);
        
        chmod($this->basePath."/".$fileName, $this->filePermission);
        
        return $returnObj ? $result : $fileName;
    }
    
    public function getFileUrl($name)
    {
        return $this->baseUrl . "/" . $name;
    }
    
    public function getPath()
    {
        return $this->basePath;
    }
    
    public function getUrl()
    {
        return $this->baseUrl;
    }
    
    public function getFile($name, $bucket = 'files')
    {
        return new FileBase($name, $bucket);
    }
    
    public function getImage($name, $bucket = 'images')
    {
        return new FileImageBase($name, $bucket);
    }
    
    public function removeFile($name)
    {
        return @unlink($this->basePath . "/" . $name);
    }

    public function fileExists($name) 
    {
        return is_file($this->basePath . "/" . $name);
    }
}