<?php

/**
 * Client
 *
 * @package Client
 * @version 6.0
 */
class Client extends ToolkitBase
{
    public $assetsModule = 'Ruxon';

    protected $aScriptFiles = array();
    protected $aCssFiles = array();
    protected $aPackages = array();
    
    protected $aAjaxScriptFiles = array();
    protected $aAjaxCssFiles = array();

    public function registerScriptFile($sPath)
    {
        if (ArrayHelper::search($sPath, $this->aScriptFiles) === false) {
            array_push($this->aScriptFiles, $sPath);

            return true;
        }

        return false;
    }

    public function registerCssFile($sPath)
    {
        if (ArrayHelper::search($sPath, $this->aCssFiles) === false) {
            array_push($this->aCssFiles, $sPath);

            return true;
        }

        return false;
    }

    public function registerPackage($sName)
    {
        if (ArrayHelper::search($sName, $this->aPackages) === false) {
            array_push($this->aPackages, $sName);

            return true;
        }

        return false;
    }
    
    public function renderScriptFiles()
    {
        $sResult = '';

        if(count($this->aScriptFiles)) {
            foreach ($this->aScriptFiles as $file) {
                $file = str_replace("~/", Core::app()->config()->getThemesUrl().'/'.Core::app()->theme()->getName().'/', $file);
                $sResult .= '<script type="text/javascript" src="'.$file.'"></script>';
                $sResult .= "\n\t";
            }
        }

        return $sResult;
    }
    
    public function renderCssFiles()
    {
        $sResult = '';

        if(count($this->aCssFiles)) {
            foreach ($this->aCssFiles as $file) {
                $file = str_replace("~/", Core::app()->config()->getThemesUrl().'/'.Core::app()->theme()->getName().'/', $file);
                $sResult .= '<link href="'.$file.'" type="text/css" rel="stylesheet" />';
                $sResult .= "\n\t";
            }
        }

        return $sResult;
    }

    public function renderPackages()
    {
        if (Core::app()->checkInstalledModule($this->assetsModule))
        {
            Core::import("Modules.".$this->assetsModule);
            $assetsUrl = Manager::getInstance()->getModule($this->assetsModule)->publishAssets();
            $assetsPath = Manager::getInstance()->getModule($this->assetsModule)->pathAssets();
            $sResult = '';
            
            if(count($this->aPackages)) {
                foreach ($this->aPackages as $package) {

                    $aPackage = Loader::loadConfigFile($assetsPath.'/'.$package, 'package');

                    if (isset($aPackage['Includes']) && count($aPackage['Includes'])) {
                        foreach ($aPackage['Includes'] as $file) {
                            $sExt = mb_substr($file, mb_strrpos($file, ".") + 1);

                            $file_path = str_replace("~/", $assetsPath.'/'.$package.'/', $file);
                            $file = str_replace("~/", $assetsUrl.'/'.$package.'/', $file);

                            switch ($sExt) {
                                case 'js':
                                    $sResult .= '<script type="text/javascript" src="'.$file.'"></script>';
                                    $sResult .= "\n\t";
                                break;

                                case 'css':
                                    $sResult .= '<link href="'.$file.'" type="text/css" rel="stylesheet" />';
                                    $sResult .= "\n\t";
                                break;

                                case 'tpl':
                                    $sResult .= file_get_contents($file_path);
                                    $sResult .= "\n\t";
                                break;
                            }
                        }
                    }
                }
            }
            
            return $sResult;
        }
    }

    public function renderAll()
    {
        $sResult = '';

        $sResult .= $this->renderPackages();
        $sResult .= $this->renderScriptFiles();
        $sResult .= $this->renderCssFiles();

        return $sResult;
    }
    
    public function registerAjaxScriptFile($sPath)
    {
        if (ArrayHelper::search($sPath, $this->aAjaxScriptFiles) === false) {
            array_push($this->aAjaxScriptFiles, $sPath);

            return true;
        }

        return false;
    }

    public function registerAjaxCssFile($sPath)
    {
        if (ArrayHelper::search($sPath, $this->aAjaxCssFiles) === false) {
            array_push($this->aAjaxCssFiles, $sPath);

            return true;
        }

        return false;
    }
    
    public function renderAjaxScriptFiles()
    {
        $sResult = '';

        if(count($this->aAjaxScriptFiles)) {
            foreach ($this->aAjaxScriptFiles as $file) {
                $file = str_replace("~/", Core::app()->config()->getThemesUrl().'/'.Core::app()->theme()->getName().'/', $file);
                $sResult .= '<script type="text/javascript" src="'.$file.'"></script>';
                $sResult .= "\n\t";
            }
        }

        return $sResult;
    }
    
    public function renderAjaxCssFiles()
    {
        $sResult = '';

        if(count($this->aAjaxCssFiles)) {
            foreach ($this->aAjaxCssFiles as $file) {
                $file = str_replace("~/", Core::app()->config()->getThemesPath().'/'.Core::app()->theme()->getName().'/', $file);
                $sResult .= '<style type="text/css">'.  file_get_contents($file).'</style>';
                $sResult .= "\n\t";
            }
        }

        return $sResult;
    }
}