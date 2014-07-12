<?php

class CmsSmsEvent extends ToolkitBase
{
    public function raise($alias, $recipients, $params = array())
    {
        $event = Manager::getInstance()->getMapper('SmsTemplateMapper')->findFirst(array(
            'Criteria' => array(
                'IsActive' => true,
                'Alias' => $alias
            )
        ));
        
        if ($event->getId())
        {
            $sms = Manager::getInstance()->getMapper('SmsProviderMapper')->findFirst(array(
                'Criteria' => array(
                    'IsActive' => true,
                )
            ));
            
            if ($sms->getId())
            {
                $classname = 'Sms'.$sms->getHandler();
                $handler = new $classname;
                $handler->init();
                
                $smsTemplate = new SmsView($params);
                $content = $smsTemplate->fetch($event->getAlias());
                
                return $handler->send($recipients, $content, Manager::getInstance()->getModule('Main')->config('AdminPhoneSender'));
            }
        }
        
        return false;
    }
}