<?php

class ConsoleRequest extends ToolkitBase
{
    protected $params;
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function getParams()
    {
        if (!isset($this->params)) {
            if (isset($_SERVER['argv'])) {
                $this->params = $_SERVER['argv'];
                array_shift($this->params);
            } else {
                $this->params = array();
            }
        }
        
        return $this->params;
    }
}