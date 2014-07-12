<?php

abstract class Core
{
	protected static $oApplication = false;

    public static function init(Application $oApp)
    {
        if (!self::$oApplication) {
            self::$oApplication = $oApp;

            $aConfig = array();

            $aConfig['Core'] = include(RX_PATH.'/ruxon/config/core.inc.php');
            $aConfig['Project'] = include(RX_PATH.'/ruxon/config/project.inc.php');
            $aConfig['Db'] = include(RX_PATH.'/ruxon/config/db.inc.php');
            $aConfig['Cache'] = include(RX_PATH.'/ruxon/config/cache.inc.php');
            $aConfig['Modules'] = file_exists(RX_PATH.'/ruxon/config/modules.inc.php') ? include(RX_PATH.'/ruxon/config/modules.inc.php') : array();

            Config::getInstance()->import($aConfig);

            return true;
        }

        return false;
    }

    public static function app()
    {
        return self::$oApplication;
    }
    
    public static function loadFramework()
    {
        return Loader::loadFramework();
    }

    public static function import($mPath)
    {
        return Loader::import($mPath);
    }

    public static function require_file($sPath, $bAbs = false, $bWithNamespace = false)
    {
        return Loader::require_file($sPath, $bAbs, $bWithNamespace);
    }

    public static function log()
    {
        return false;
    }

    public static function t()
    {
        return false;
    }

    public static function p($mData, $sFileName = false, $sLine = false)
    {
        $sResult = '<p>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~</p>';
        if ($sFileName && $sLine) {
            $sResult .= '<p><strong>File: '.$sFileName.'</strong><br />';
            $sResult .= '<strong>Line: '.$sLine.'</strong></p>';
        }
        $sResult .= '<pre>'.print_r($mData, true).'</pre>';
        $sResult .= '<p>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~</p>';

        echo $sResult;
    }

    public static function toolkit()
    {
        return Toolkit::getInstance();
    }

    /**
     * WTF???
     * 
     * @param <type> $aInput
     * @param <type> $aLang
     * @return <type>
     */
    public static function parseXmlI18n($aInput, $aLang)
    {
        $aSearch = array();
        $aReplace = array();
        
        if (is_array($aLang) && count($aLang)) {        
            foreach ($aLang as $k => $val) {
                $aSearch[] = '{$'.$k.'}';
                $aReplace[] = $val;
            }

            foreach ($aInput as $k => $val) {
                if (!is_array($val)) {
                    $aInput[$k] = str_replace($aSearch, $aReplace, $val);
                } else {
                    $aInput[$k] = Core::parseXmlI18n($aInput[$k], $aLang);
                }
            }
        }

        return $aInput;
    }
}