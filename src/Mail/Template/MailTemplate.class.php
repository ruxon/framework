<?php

class MailTemplate extends Ruxon
{
    public function fetch($name)
    {
        $sResult = '';
		ob_start();
        
        include(RX_PATH.'/ruxon/email_templates/'.$name.'.tpl.php');

        $sResult = ob_get_contents();
		ob_end_clean();

		return $sResult;
    }
}