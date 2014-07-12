<?php

class Mail extends ToolkitBase
{
    private $_myMailer;

    public function __construct()
    {
        $this->_myMailer = new PHPMailer();
        $this->_myMailer->CharSet = 'utf-8';
    }

    public function __call($method, $params)
    {
        if (is_object($this->_myMailer) && get_class($this->_myMailer)==='PHPMailer')
        {
            return call_user_func_array(array($this->_myMailer, $method), $params);
        }
        else 
        {
            throw new Exception('Can not call a method of a non existent object');
        }
    }

    public function __set($name, $value)
    {
       if (is_object($this->_myMailer) && get_class($this->_myMailer)==='PHPMailer')
       {
           $this->_myMailer->$name = $value;
       }
       else 
       {
           throw new Exception('Can not set a property of a non existent object');
       }
    }

    public function __get($name)
    {
       if (is_object($this->_myMailer) && get_class($this->_myMailer)==='PHPMailer')
       {
           return $this->_myMailer->$name;
       }
       else
       {
           throw new Exception('Can not access a property of a non existent object');
       }
    }

    public function SetHtmlBody($body)
    {
        return $this->_myMailer->MsgHTML($body);
    }
}