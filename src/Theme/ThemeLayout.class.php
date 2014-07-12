<?php

class ThemeLayout
{
    protected $sName;

    protected $oCells;

    /**
     * Данные
     *
     * @var array
     */
    protected $aData = array();

    public function __construct($sName)
    {
        $this->sName = $sName;
    }

    public function getUrl()
    {
        return $this->getTheme()->getUrl();
    }

    public function getName()
    {
        return $this->sName;
    }

    public function setCells(ThemeLayoutCellsCollection $oCells)
    {
        $this->oCells = $oCells;

        return true;
    }

    public function getTheme()
    {
        return Core::app()->theme();
    }
    
    public function getCells()
    {
        return $this->oCells;
    }

    public function getCell($sName)
    {
        if ($this->hasCell($sName)) {
            return $this->oCells->get($sName);
        }

        return false;
    }

    public function hasCell($sName)
    {
        return $this->oCells->exists($sName);
    }

    public function set($sName, $mValue)
    {
        $this->aData[$sName] = $mValue;

        return true;
    }

    public function get($sName)
    {
        if ($this->exists($sName)) {
            return $this->aData[$sName];
        }

        return false;
    }

    public function exists($sName)
    {
        if (isset($this->aData[$sName])) {
            return true;
        }

        return false;
    }

    public function export()
    {
        return $this->aData;
    }
    
    public function import($data)
    {
        $this->aData = $data;
        
        return true;
    }

    public function __call($sName, $aParams = array())
    {
        $sFunc = mb_substr($sName, 0, 3);
        $sField = mb_substr($sName, 3);

        switch ($sFunc) {
            case 'get':
                return $this->get($sField);
            break;

            case 'set':
                return $this->set($sField, $aParams[0]);
            break;
        }

        return false;
    }

    public function fetch()
    {
        $sResult = '';
		ob_start();
        
        $name = ucfirst($this->getName());
        if(!include($this->getTheme()->getPath().'/layouts/'.$name.'.tpl.php')) {
            throw new RxException('Шаблон "'.$this->getTheme()->getName().'/'.$name.'" не может быть загружен.');
        }

        $sResult = ob_get_contents();
		ob_end_clean();

		return $sResult;
    }
    
    public function renderPartial($name)
    {
        $sResult = '';
		ob_start();
        
        $name = ucfirst($name);
        if(!include($this->getTheme()->getPath().'/layouts/'.$name)) {
            throw new RxException('Шаблон "'.$this->getTheme()->getName().'/'.$name.'" не может быть загружен.');
        }

        $sResult = ob_get_contents();
		ob_end_clean();

		return $sResult;
    }

    public function fetchCells()
    {
        if ($this->oCells->count()) {
            return $this->oCells->fetch();
        }

        return false;
    }

    public function getToolkit()
    {
        return Toolkit::getInstance();
    }

    public function component($sModuleAlias, $sComponentAlias, $aParams = array())
    {
        Core::import('Components.'.$sModuleAlias.'.'.$sComponentAlias);


        $sFullClassName = 'ruxon\modules\\'.$sModuleAlias.'\components\\'.$sComponentAlias.'\classes\\'.$sComponentAlias.'Component';
        $sClassName = $sModuleAlias.$sComponentAlias.'Component';

        $oComponent = class_exists($sFullClassName) ? new $sFullClassName : new $sClassName;
        $oComponent->init($aParams);

        $oComponent->run();

        $oResponse = new ActionResponse(true);

        $oResponse->setHtml($oComponent->fetch());

        return $oResponse;
    }

    public function widget($sModuleAlias, $sComponentAlias, $aParams = array())
    {
        echo $this->component($sModuleAlias, $sComponentAlias, $aParams)->getHtml();
    }
    
    public function getPage()
    {
        return FrontController::getInstance()->getPage();
    }
    
    public function getSite()
    {
        return FrontController::getInstance()->getSite();
    }

    public function showPanel()
    {
        /*if (Toolkit::getInstance()->auth->isAdmin())
        {
            $this->widget('Main', 'Panel');
        }*/
    }

    public function t($category, $message, $params = [], $language = null)
    {
        return Core::app()->t($category, $message, $params, $language, 'themes/'.$this->getTheme()->getName().'/messages');
    }
}