<?php

class SmsEvent extends ToolkitBase
{
    public $phoneSender;

    public $handlerClass;

    public $handlerParams;
    
    public function raise($alias, $recipients, $params = array())
    {
        $handler = new $this->handlerClass ($this->handlerParams);
        $handler->init();

        $smsTemplate = new SmsView($params);
        $content = $smsTemplate->fetch($alias);

        return $handler->send($recipients, $content, $this->phoneSender);
    }
}