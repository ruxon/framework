<?php

class ActionView
{
    protected $oController = false;

    protected $sView = '';

    /**
     * Данные
     *
     * @var array
     */
    protected $aData = array();

    public function __construct($oController, $sView, $aParams = array())
    {
        $this->aData = $aParams;

        $this->sView = $sView;

        $this->oController = $oController;
    }

    public function getUrl()
    {
        return Core::app()->theme()->getUrl();
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
		ob_start();
        
        $viewTemplateFile = RX_PATH.'/themes/'.$this->getController()->getTheme().'/views/'.$this->getController()->getModuleAlias().'/'.$this->getController()->getControllerAlias().'/'.ucfirst($this->sView).'.tpl.php';
        $viewModuleFile = RX_PATH.'/ruxon/modules/'.$this->getController()->getModuleAlias().'/views/'.$this->getController()->getTheme().'/'.$this->getController()->getControllerAlias().'/'.ucfirst($this->sView).'.tpl.php';
        $viewDefaultModuleFile = RX_PATH.'/ruxon/modules/'.$this->getController()->getModuleAlias().'/views/default/'.$this->getController()->getControllerAlias().'/'.ucfirst($this->sView).'.tpl.php';

        if (file_exists($viewTemplateFile)) {
            include($viewTemplateFile);
        } else if (file_exists($viewModuleFile)) {
            include($viewModuleFile);
        } else if (file_exists($viewDefaultModuleFile)) {
            include($viewDefaultModuleFile);
        } else {
            throw new RxException('Шаблон не может быть загружен.');
        }
                
        $sResult = ob_get_contents();
		ob_end_clean();

		return $sResult;
    }

    public function getController()
    {
        return $this->oController;
    }

    public function getToolkit()
    {
        return Toolkit::getInstance();
    }

    public function widget($module, $component, $params = array())
    {
        return $this->getController()->widget($module, $component, $params);
    }
    
    public function renderPartial($file, $data)
    {
        $oView = new ActionView($this->oController, $file, $data);
        
        echo $oView->fetch();
    }
    
    public function getDbConnection($alias = 'default')
    {
        return Manager::getInstance()->getDb($alias);
    }
    
    public function module_config($alias)
    {
        return $this->getController()->module_config($alias);
    }

    public function t($category, $message, $params = [], $language = null)
    {
        return Core::app()->t($category, $message, $params, $language, 'ruxon/modules/'.$this->oController->getModuleAlias().'/messages');
    }
}