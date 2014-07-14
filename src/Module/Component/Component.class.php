<?php

/**
 * Component: abstract class for a component
 *
 * @package Module
 * @subpackage Component
 * @version 6.1
 */
abstract class Component
{
    use EmailEventable;
    use SmsEventable;

    protected $sModuleAlias;

    protected $sModelAlias;

    protected $sComponentAlias;

    protected $aContainer = array();

    protected $oRequest;

    protected $oResponse;

    protected $aRequest = array();

    protected $oTemplate = false;

    /**
     * Events
     *
     * @var array
     */
    protected $aEvents;

    protected $execution_start = 0;

    public function  __construct($aConfig = array())
    {
        $this->execution_start = microtime(true);

        $module = Core::app()->getModuleById($this->sModuleAlias);
        $infoPath = empty($module['BasePath']) ? RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/components/'.$this->sComponentAlias : RX_PATH.'/'.$module['BasePath'].'/components/'.$this->sComponentAlias;

        $this->aContainer = Loader::loadConfigFile($infoPath, 'component');

        $this->init($aConfig);

        $this->setComponentResponse(new ComponentResponse());

        if (!$this->getComponentRequest()->getTemplate()) {
            $this->getComponentRequest()->setTemplate('Index');
        }
    }

    public function init($aConfig = array())
    {
        $aAllConfig = array();
        /* Инициализация параметров по-умолчанию */
        if (isset($this->aContainer['Params']) && is_array($this->aContainer['Params']) && count($this->aContainer['Params']) > 0) {
            /*if (count($aConfig)) {
                foreach ($aConfig as $cv => $cfg) {
                    if (!key_exists($cv, $this->aContainer['Params'])) {
                        unset($aConfig[$cv]);
                    }
                }
            }*/

            foreach ($this->aContainer['Params'] as $var => $val) {
                if (isset($aConfig[$var])) {
                    $aAllConfig[$var] = $aConfig[$var];
                } else if (isset($val['Default'])) {
                    $aAllConfig[$var] = $val['Default'];
                }
            }

        }

        $this->setComponentRequest(new ComponentRequest());
        $this->getComponentRequest()->import($aAllConfig);
        /* END OF: Инициализация параметров по-умолчанию */

        return true;
    }

    public function start()
    {
        $this->aRequest = $this->getComponentRequest()->export();
        $templateClass = Toolkit::getInstance()->theme->getComponentTemplateClass();
        $this->oTemplate = new $templateClass($this->sModuleAlias, $this->sComponentAlias, $this->getComponentRequest()->getTemplate());
    }

    public function run()
    {
        $this->start();

        $aResult = array();

        $this->end($aResult, true);
    }

    public function end($aResult, $bSuccess = true)
    {
        $this->getComponentResponse()->setIsSuccess($bSuccess);
        $this->getComponentResponse()->setResult($aResult);

        $this->getTemplate()->import($aResult);

        $end = microtime(true);
        $debug = '';
        if (RUXON_DEBUG)
        {
            $debug .= '<div style="padding: 5px; color: black; border: 1px solid red; margin: 10px auto; background-color: #fff; width: 350px;">';
            $debug .= 'Execution time: '. round(($end - $this->execution_start), 4) . " sec<br />";
            $debug .= '</div>';
        }

        //if (isset($this->aContainer['UseTemplate']) && $this->aContainer['UseTemplate']) {
        $this->getComponentResponse()->setHtml($this->fetch());
        //} else {
        //    $this->getComponentResponse()->setHtml('');
        //}

        if (RUXON_DEBUG)
        {
            //echo $debug;
        }
    }

    public function setComponentRequest(ComponentRequest $oRequest)
    {
        $this->oRequest = $oRequest;

        return true;
    }

    public function getComponentRequest()
    {
        return $this->oRequest;
    }

    public function setComponentResponse(ComponentResponse $oResponse)
    {
        $this->oResponse = $oResponse;

        return true;
    }

    public function getComponentResponse()
    {
        return $this->oResponse;
    }

    public function getResult($sAlias = false)
    {
        return $this->getComponentResponse()->getResult($sAlias);
    }

    public function getComponentLang($sGroup, $sAlias, $sSubAlias = '')
    {
        $aInput = $this->aContainer['ComponentLang'];
        if ($sSubAlias != '') {
            if (isset($aInput[$sGroup][$sAlias][$sSubAlias])) {
                return $aInput[$sGroup][$sAlias][$sSubAlias];
            }
        } else {
            if (isset($aInput[$sGroup][$sAlias])) {
                return $aInput[$sGroup][$sAlias];
            }
        }

        return false;
    }

    public function fetch($sTemplate = false)
    {
        return $this->getTemplate()->fetch();
    }

    public function display()
    {
        echo $this->fetch();
    }

    /**
     * Возвращает шаблон
     *
     * @return ComponentTemplate
     */
    public function getTemplate()
    {
        return $this->oTemplate;
    }

    /**
     * Возвращает тулкит
     *
     * @return Toolkit
     */
    protected function getToolkit()
    {
        return Toolkit::getInstance();
    }

    public function refresh()
    {
        Toolkit::getInstance()->response->refresh();
    }

    public function redirect($path, $params)
    {
        if (is_array($path))
        {
            $path_real = '';
            $cnt = count($path);

            switch ($cnt)
            {
                // this module, controller
                case 1:
                    $path_real .= $this->sModuleAlias.'/'.$this->sControllerAlias.'/'.$path[0];
                    break;

                // this module
                case 2:
                    $path_real .= $this->sModuleAlias.'/'.implode("/", $path);
                    break;

                // other
                default:
                    $path_real .= implode("/", $path);
            }

            $fullPath = Toolkit::i()->createUrl(strtolower($path_real), $params);

            header("Location: ".$fullPath);
            Core::app()->hardEnd();

        } else {
            $fullPath = Toolkit::i()->createUrl(strtolower($path), $params);

            header("Location: ".$fullPath);
            Core::app()->hardEnd();
        }
    }

    public function module_config($alias)
    {
        return Manager::getInstance()->getModule($this->sModuleAlias)->config($alias);
    }

    protected function mapper($alias = false)
    {
        return Manager::getInstance()->getMapper($alias ? $alias : $this->sMapperAlias);
    }

    public function t($category, $message, $params = [], $language = null)
    {
        $infoPath = empty($module['BasePath']) ? 'ruxon/modules/'.$this->sModuleAlias.'/component/'.$this->sComponentAlias.'/messages' : $module['BasePath'].'/component/'.$this->sComponentAlias.'/messages';

        return Core::app()->t($category, $message, $params, $language, $infoPath);
    }
}