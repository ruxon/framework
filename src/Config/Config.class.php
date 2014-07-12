<?php

class Config
{
    protected $sPath;

    protected $sUrl;

    protected $sLang;

    protected $sDefaultLang;

    protected $sUploadsPath;

    protected $bDebugMode;

    protected $sTheme;

    protected $sThemeLayout;

    protected $sThemesUrl;

    protected $sThemesPath;

    protected $aDb;

    protected $aCache;

    protected $aPackages;

    protected $aFilters;

    protected $aEvents;

    protected $aExtensions;

    protected $aDefault;

    protected $aRoutes;

    protected $aModules;

    private static $instance = false;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public static function i()
    {
        return self::getInstance();
    }

    public function import($aData)
    {
        $this->sPath = $aData['Project']['Path'];
        $this->sUrl = $aData['Project']['Url'];

        $this->sLang = $aData['Project']['Language'];
        $this->sDefaultLang = $aData['Project']['DefaultLanguage'];
        $this->bDebugMode = $aData['Project']['DebugMode'];

        $aFilters = array_merge_recursive($aData['Core']['Filters'], $aData['Core']['Applications'][Core::app()->getName()]['Filters']);

        foreach ($aFilters as $key => $value) {
            if (is_numeric($key)) {
                $this->aFilters[$value] = array();
            } else {
                $this->aFilters[$key] = $value;
            }
        }
        
        $this->aEvents = array_merge($aData['Core']['Events'], $aData['Core']['Applications'][Core::app()->getName()]['Events']);
        $this->aExtensions = array_merge($aData['Core']['Extensions'], $aData['Core']['Applications'][Core::app()->getName()]['Extensions']);

        $this->aDb = $aData['Db'];
        $this->aCache = $aData['Cache'];

        $this->sThemesPath = $this->sPath.'/themes';
        $this->sThemesUrl = $this->sUrl.'/themes';

        $this->aDefault = isset($aData['Core']['Applications'][Core::app()->getName()]['Default']) ? $aData['Core']['Applications'][Core::app()->getName()]['Default'] : array();
        $this->aRoutes = isset($aData['Core']['Applications'][Core::app()->getName()]['Routes']) ? $aData['Core']['Applications'][Core::app()->getName()]['Routes'] : array();

        $this->aModules = $aData['Modules'];

        return true;

    }

    public function getPath()
    {
        return $this->sPath;
    }

    public function getUrl()
    {
        return $this->sUrl;
    }

    public function getLang()
    {
        return $this->sLang;
    }

    public function getDefaultLang()
    {
        return $this->sDefaultLang;
    }

    public function getUploadsPath()
    {
        return $this->sUploadsPath;
    }

    public function getDebugMode()
    {
        return $this->bDebugMode;
    }

    public function getTheme()
    {
        return $this->sTheme;
    }

    public function getThemeLayout()
    {
        return $this->sThemeLayout;
    }

    public function getPackages()
    {
        return $this->aPackages;
    }

    public function getFilters()
    {
        return $this->aFilters;
    }

    public function getEvents()
    {
        return $this->aEvents;
    }

    public function getExtensions()
    {
        return $this->aExtensions;
    }

    public function getDb()
    {
        return $this->aDb;
    }

    public function getDbById($id)
    {
        return !empty($this->aDb[$id]) ? $this->aDb[$id] : false;
    }

    public function getCache()
    {
        return $this->aCache;
    }

    public function getThemesPath()
    {
        return $this->sThemesPath;
    }

    public function getThemesUrl()
    {
        return $this->sThemesUrl;
    }

    public function getDefault()
    {
        return $this->aDefault;
    }

    public function getRoutes()
    {
        return $this->aRoutes;
    }

    public function getModules()
    {
        return $this->aModules;
    }
}