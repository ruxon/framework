<?php

class HtmlHelper
{
	public static function div()
	{
		return false;
	}
    
    public static function span($sValue, $aParams = array())
    {
        return '<span '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').'>'.$sValue.'</span>';
    }

	public static function divFor()
	{
		return false;
	}   
    
    public static function link($sUrl, $sText = '', $aParams = array())
    {
        if (!$sText) $sText = $sUrl;
        
        $params_str = '';
        if (count($aParams)) 
        {
            foreach ($aParams as $k => $par) {
                $params_str .= $k . '="'.$par.'" ';
            }
        }
        
        return '<a href="'.$sUrl.'" '.$params_str.'>'.$sText.'</a>';
    }
    
    public static function button($sUrl, $sText = '', $aParams = array())
    {
        if (!$sText) $sText = $sUrl;
        
        $params_str = '';
        if (count($aParams)) 
        {
            foreach ($aParams as $k => $par) {
                $params_str .= $k . '="'.$par.'" ';
            }
        }
        
        return '<button '.$params_str.' onclick="location.href=\''.$sUrl.'\'">'.$sText.'</button>';
    }

    public static function formButton($type, $title, $params = array())
    {
        return '<input type="'.$type.'" value="'.$title.'" '.FormHelper::parseParamsString($params).' />';
    }
    
    public static function ajaxLink($sUrl, $sText = '', $aParams = array())
    {
        return self::link('#!'.$sUrl, $sText, $aParams); 
    }
    
    public static function ajaxButton($sUrl, $sText = '', $aParams = array())
    {
        return self::button('#!'.$sUrl, $sText, $aParams); 
    }
    
    public static function image($sSrc, $sAlt = '', $aParams = array())
    {
        $params_str = '';
        if (count($aParams)) 
        {
            foreach ($aParams as $k => $par) {
                $params_str .= $k . '="'.$par.'" ';
            }
        }
        
        return '<img src="'.$sSrc.'" alt="'.$sAlt.'" '.$params_str.'/>';
    }
    
    public static function siteAjaxLink($sText, $sUrl, $aParams = array())
    {
        $res = self::link($sUrl, $sText, $aParams); 
        
        return $res;
    }
}