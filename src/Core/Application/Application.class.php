<?php

abstract class Application
{
    protected $sAppName;
    
    protected $installedModules = null;

    protected $aModules = array();

    public function getName()
    {
        return $this->sAppName;
    }

    public function start()
    {
        $this->aModules = Config::i()->getModules();

		$this->loadCommonExtensions();
		$this->init();

        $oFilterChain = new FilterChain();
		$this->composeFilters($oFilterChain)->process();
	}

    public function run()
    {
		$this->start();
        $this->process();
		$this->end();
    }

    public function process()
    {
        return true;
    }

    public function getRules()
    {
        return $this->config()->getRoutes();
    }

    public function getDefault()
    {
        return $this->config()->getDefault();
    }

    public function end()
    {
		return true;
    }

    public function config()
    {
        return Config::getInstance();
    }
    
    public function checkInstalledModules($modules = array())
    {
        return Toolkit::i()->modules->checkInstalledModules($modules);
    }
    
    public function checkInstalledModule($module)
    {
        return Toolkit::i()->modules->checkInstalledModule($module);
    }

    public function getModules()
    {
        return Toolkit::i()->modules->getModules();
    }

    public function getModuleById($id)
    {
        return !empty($this->aModules[$id]) ? $this->aModules[$id] : false;
    }

    public function updateModuleById($id, $data)
    {
        $this->aModules[$id] = (array) $data;

        $this->saveModulesInfo();

        return true;
    }

    public function deleteModuleById($id)
    {
        return Toolkit::i()->modules->deleteModuleById($id);
    }

    public function t($category, $message, $params = [], $language = null, $basePath = null)
    {
        return Toolkit::i()->i18n->translate($category, $message, $params, $language, $basePath);
    }

    protected function saveModulesInfo()
    {
        return Toolkit::i()->modules->saveModulesInfo();
    }

    protected function loadCommonExtensions()
    {
        $aExtensions = $this->config()->getExtensions();

        if (count($aExtensions)) {
            foreach ($aExtensions as $ext) {
                Core::import(array('Extensions', $ext));
            }
        }
    }

	protected function init()
	{

	}

	protected function composeFilters(FilterChain $oFilterChain)
	{
        $aFilters = $this->config()->getFilters();

        if (count($aFilters)) {
            foreach ($aFilters as $filter => $params) {
                $sFilterClass = $filter.'Filter';
                $oFilterChain->registerFilter(new $sFilterClass($params));
            }
        }

		return $oFilterChain;
	}

	protected function getMapper($sModuleAlias, $sModelAlias)
	{
		return Toolkit::getInstance()->getModulesManager()->getMapper($sModuleAlias, $sModelAlias);
	}

    protected function getToolkit()
    {
        return Toolkit::getInstance();
    }
}