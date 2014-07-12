<?php

class DataProvider
{
    public $method = 'find';
    
    public $mapper;
    
    public $params = array();
    
    public function __construct($params)
    {
        $this->params = !empty($params[2]) ? $params[2] : array();
        $this->mapper = $params[1];
        
        $this->init($params[0]);
    }
    
    public function init($module)
    {
        Core::import('Modules.'.$module);
    }
    
    public function execute() 
    {
        $mapper = Manager::getInstance()->getMapper($this->mapper);
        
        return call_user_func(array($mapper, $this->method), $this->params);
    }
}