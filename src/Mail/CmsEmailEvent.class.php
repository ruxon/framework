<?php

class CmsEmailEvent extends ToolkitBase
{
    public function raise($alias, $recipients, $params = array())
    {
        Loader::import('Modules.Main');

        if (!is_array($recipients)) $recipients = array($recipients);

        $event = Manager::getInstance()->getMapper('EmailObjectMapper')->findFirst(array(
            'Criteria' => array(
                'IsActive' => true,
                'Alias' => $alias
            )
        ));

        if ($event->getId())
        {
            Toolkit::getInstance()->mail->ClearAddresses();
            Toolkit::getInstance()->mail->From = Manager::getInstance()->getModule('Main')->config('FromEmail');
            Toolkit::getInstance()->mail->FromName = Manager::getInstance()->getModule('Main')->config('FromName');

            foreach ($recipients as $email)
            {
                Toolkit::getInstance()->mail->AddAddress($email);
            }

            Toolkit::getInstance()->mail->Subject = $event->getSubject();

            $emailTemplate = new MailTemplate($params);

            Toolkit::getInstance()->mail->SetHtmlBody($emailTemplate->fetch($event->getAlias()));

            return Toolkit::getInstance()->mail->Send();
        }

        return false;
    }
}