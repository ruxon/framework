<?php

class JsHelper
{
    public function script($file)
    {
        echo '<script type="text/javascript" src="'.$file.'"></script>';
    }
    
	public static function jsonEncode($mVar)
    {
        return json_encode($mVar);
    }

    public static function jsonDecode($mVar)
    {
        return json_decode($mVar);
    }
}