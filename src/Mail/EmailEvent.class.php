<?php

class EmailEvent extends ToolkitBase
{
    public $fromName;

    public $fromEmail;
    
    public function raise($alias, $recipients, $params = array(), $subject = '')
    {
        if (!is_array($recipients)) $recipients = array($recipients);
        
        Toolkit::i()->mail->ClearAddresses();
        Toolkit::i()->mail->From = $this->fromEmail;
        Toolkit::i()->mail->FromName = $this->fromName;

        foreach ($recipients as $email)
        {
            Toolkit::i()->mail->AddAddress($email);
        }

        Toolkit::i()->mail->Subject = $subject;

        $emailTemplate = new MailTemplate($params);

        Toolkit::i()->mail->SetHtmlBody($emailTemplate->fetch($alias));

        return Toolkit::i()->mail->Send();
    }
}