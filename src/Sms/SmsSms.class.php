<?php

class SmsSms extends BaseSmsSms
{
    public function init()
    {
        parent::init();

        $sms = Manager::getInstance()->getMapper('SmsProviderMapper')->findFirst(array(
            'Criteria' => array(
                'IsActive' => true,
                'Handler' => 'Sms'
            )
        ));

        if ($sms->getId())
        {
            $this->login = $sms->getLogin();
            $this->password = $sms->getPassword();
            $this->secretkey = $sms->getSecretkey();
        }
    }
}