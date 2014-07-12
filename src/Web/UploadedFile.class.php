<?php

class UploadedFile
{
    protected $_name;
    protected $_tempName;
    protected $_type;
    protected $_size;
    protected $_error;
    
    public static function getInstance($model, $attribute) 
    {
        $name = $_FILES[$attribute]['name'];
        $tempName = $_FILES[$attribute]['tmp_name'];
        $type = $_FILES[$attribute]['type'];
        $size = $_FILES[$attribute]['size'];
        $error = $_FILES[$attribute]['error'];
        
        return new UploadedFile($name, $tempName, $type, $size, $error);
    }
    
    public function __construct($name, $tempName, $type, $size, $error)
    {
        $this->_name = $name;
        $this->_tempName = $tempName;
        $this->_type = $type;
        $this->_size = $size;
        $this->_error = $error;
        
        $this->init();
    }
    
    public function init() 
    {
        // получаем инфу о файле
    }
    
    public function getErrors() 
    {
        return $this->_error;
    }
    
    public function getName() 
    {
        return $this->_name;
    }
    
    public function getTempName() 
    {
        return $this->_tempName;
    }
    
    public function getExtension() 
    {
        return substr($this->_name, strrpos($this->_name, ".") + 1);
    }
    
    public function getSize() 
    {
        return $this->_size;
    }
    
    public function getType() 
    {
        return $this->_type;
    }
    
    public function saveAs($fileName)
    {
        return move_uploaded_file($this->getTempName(), $fileName);
    }
}