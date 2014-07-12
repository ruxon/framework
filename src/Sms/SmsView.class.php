<?php

class SmsView extends Ruxon
{
    public function fetch($name)
    {
        $sResult = '';
		ob_start();
        
        include(RX_PATH.'/ruxon/sms_templates/'.$name.'.tpl.php');

        $sResult = ob_get_contents();
		ob_end_clean();

		return $sResult;
    }
}