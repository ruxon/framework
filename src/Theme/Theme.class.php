<?php

/**
 * Theme
 *
 * @package Theme
 * @version 6.0
 */
class Theme extends ToolkitBase
{
    protected $sName;

    protected $sPath;

    protected $sUrl;

    protected $oLayout;

    protected $layoutClass = 'ThemeLayout';

    protected $actionViewClass = 'ActionView';

    protected $componentTemplateClass = 'ComponentTemplate';

    public function __construct($aParams)
    {
        $this->init($aParams['theme'], $aParams['layout']);
    }

    public function getPath()
    {
        return $this->sPath;
    }

    public function getUrl()
    {
        return $this->sUrl;
    }

    public function getName()
    {
        return $this->sName;
    }

    public function getLayout()
    {
        return $this->oLayout;
    }
    
    public function layout()
    {
        return $this->oLayout;
    }

    public function fetchLayout()
    {
        return $this->getLayout()->fetch();
    }

    public function fetch()
    {
        return $this->fetchLayout();
    }

    public function init($sName, $sLayout = 'index')
    {
        $this->sName = $sName;
        $this->sPath = Core::app()->config()->getThemesPath().'/'.$sName;
        $this->sUrl  = Core::app()->config()->getThemesUrl().'/'.$sName;

        $classLayout = $this->layoutClass;

        $this->oLayout = new $classLayout($sLayout);

        $aTheme = Loader::loadConfigFile(Core::app()->config()->getThemesPath().'/'.$sName, 'theme');

        if ($aTheme && isset($aTheme['Client']['Packages']) && count($aTheme['Client']['Packages'])) {
            foreach ($aTheme['Client']['Packages'] as $itm) {
                Core::app()->client()->registerPackage($itm);
            }
        }

        if ($aTheme && isset($aTheme['Client']['ScriptFiles']) && count($aTheme['Client']['ScriptFiles'])) {
            foreach ($aTheme['Client']['ScriptFiles'] as $itm) {
                Core::app()->client()->registerScriptFile($itm);
            }
        }

        if ($aTheme && isset($aTheme['Client']['CssFiles']) && count($aTheme['Client']['CssFiles'])) {
            foreach ($aTheme['Client']['CssFiles'] as $itm) {
                Core::app()->client()->registerCssFile($itm);
            }
        }

        return true;
    }

    public function getActionViewClass()
    {
        return $this->actionViewClass;
    }

    public function getComponentTemplateClass()
    {
        return $this->componentTemplateClass;
    }
}