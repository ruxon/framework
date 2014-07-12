<?php

/**
 * Controller: abstract controller class
 * 
 * @package Module
 * @subpackage Controller
 * @version 6.1 
 */
abstract class Controller extends SimpleController
{
    use EmailEventable;
    use SmsEventable;

    protected $sTheme = false;

    protected $sLayout = '';
    
    /**
     * Model alias
     * 
     * @var string|false 
     */
    protected $sModelAlias = false;

    /**
     * Module alias
     * 
     * @var string|false 
     */
    protected $sModuleAlias = false;
    
    /**
     * Controller alias
     * 
     * @var string|false 
     */
    protected $sControllerAlias = false;

    /**
     * Mapper alias
     * 
     * @var string|false 
     */
    protected $sMapperAlias = false;

    /**
     * Constructor 
     */
    public function __construct($module = false, $controller = false)
    {
        if ($module) {
            $this->sModuleAlias = $module;
        }

        if ($controller) {
            $this->sControllerAlias = $controller;
        }

        if (!$this->sTheme) {
            $this->sTheme = Core::app()->theme()->getName();
        }

        if ($this->sLayout === '') {
            $this->sLayout = Core::app()->theme()->getLayout()->getName();
        }
        
        $this->load_config();
    }

    public function setThemeLayout($theme, $layout)
    {
        $this->sTheme = $theme;
        $this->sLayout = $layout;

        return true;
    }

    /**
     * Index action: default action for the controller
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionIndex($aParams = array())
    {
        return $this->view('Index');
    }

    /**
     * List action: list of elements
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionList($aParams = array())
    {
        $oData = $this->mapper()->find();
        $aParams = array(
            'Data' => $oData
        );

        return $this->view('List', $aParams);
    }

    /**
     * Create action: create an element
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionCreate($aParams = array())
    {
        return $this->view('Create');
    }

    /**
     * Update action: update an element
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionUpdate($aParams = array())
    {
        return $this->view('Update');
    }

    /**
     * Delete action: delete an element
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionDelete($aParams = array())
    {
        return $this->view('Delete');
    }

    /**
     * Detail action: show element details
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionDetail($aParams = array())
    {
        return $this->view('Detail');
    }

    /**
     * SimpleList action: simple elements list
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionSimpleList($aParams = array())
    {
        return $this->view('SimpleList');
    }

    /**
     * Return a module alias
     * 
     * @return string 
     */
    public function getModuleAlias()
    {
        return $this->sModuleAlias;
    }

    /**
     * Return a model alias
     * 
     * @return string 
     */
    public function getModelAlias()
    {
        return $this->sModelAlias;
    }

    /**
     * Return a controller alias
     * 
     * @return string 
     */
    public function getControllerAlias()
    {
        return !$this->sControllerAlias ? substr(get_called_class(), 0, strlen(get_called_class()) - 10) : $this->sControllerAlias;
    }

    /**
     * Return a object mapper instance
     * 
     * @return ObjectMapper 
     */
    protected function mapper($alias = false)
    {
        return Manager::getInstance()->getMapper($alias ? $alias : $this->sMapperAlias);
    }

    /**
     * Load a controller config
     * 
     * @return boolean 
     */
    protected function load_config()
    {
        /*$sFileName = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/controllers/'.$this->sControllerAlias.'Controller.inc.php';
        if (file_exists($sFileName)) {
            $this->aConfig = include($sFileName);
            
            $sBasePath = 'ruxon/modules/'.$this->sModuleAlias;
            
            $aLang = array();

            if (file_exists(RX_PATH.'/'.$sBasePath.'/messages/'.Core::app()->config()->getLang().'/messages.inc.php')) {
                $aLang = Core::require_file($sBasePath.'/messages/'.Core::app()->config()->getLang().'/messages.inc.php');
            } else if(file_exists(RX_PATH.'/'.$sBasePath.'/messages/'.Core::app()->config()->getDefaultLang().'/messages.inc.php')) {
                $aLang = Core::require_file($sBasePath.'/messages/'.Core::app()->config()->getDefaultLang().'/messages.inc.php');
            } else {
                $aLang = array();
            }
            
            $this->aConfig = Core::parseXmlI18n($this->aConfig, $aLang);
            
        } else {
            $this->aConfig = array();
        }

        return true;*/
    }

    /**
     * Return an action config
     * 
     * @param string $sActionAlias action alias
     * @return array|false 
     */
    protected function config($sActionAlias)
    {
        $sFileName = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/controllers/'.$this->sControllerAlias.'/action'.$sActionAlias.'.inc.php';
        if (file_exists($sFileName)) 
        {
            $aConfig = include($sFileName);
            
            $sBasePath = 'ruxon/modules/'.$this->sModuleAlias;
            
            $aLang = array();

            if (file_exists(RX_PATH.'/'.$sBasePath.'/messages/'.Core::app()->config()->getLang().'/messages.inc.php')) {
                $aLang = Core::require_file($sBasePath.'/messages/'.Core::app()->config()->getLang().'/messages.inc.php');
            } else if(file_exists(RX_PATH.'/'.$sBasePath.'/messages/'.Core::app()->config()->getDefaultLang().'/messages.inc.php')) {
                $aLang = Core::require_file($sBasePath.'/messages/'.Core::app()->config()->getDefaultLang().'/messages.inc.php');
            } else {
                $aLang = array();
            }
            
            $aConfig = Core::parseXmlI18n($aConfig, $aLang);
            
            return $aConfig;
        }

        return false;
    }
    
    public function module_config($alias)
    {
        return Manager::getInstance()->getModule($this->sModuleAlias)->config($alias);
    }
    
    public function getPath()
    {
        
    }
    
    public function getDbConnection($alias = 'default')
    {
        return Manager::getInstance()->getDb($alias);
    }
    
    public function module($alias = false)
    {
        return Manager::getInstance()->getModule($alias ? $alias : $this->sModuleAlias);
    }
    
    public function getTheme()
    {
        return $this->sTheme;
    }

    public function getLayout()
    {
        return $this->sLayout;
    }
    
    public function layout()
    {
        return Core::app()->theme()->getLayout();
    }

    public function view($sAction, $aParams = array(), $sController = false)
    { 

        if ($sController) {
            $oController = new $sController;
        } else {
            $oController = $this;
        }

        $bLayout = ($this->sLayout === false ? false : true);
        
        if (is_scalar($this->sLayout))
        {
            $data = Core::app()->theme()->layout()->export();
            Core::app()->theme()->init(Core::app()->theme()->getName(), $this->sLayout);
            Core::app()->theme()->layout()->import($data);
        }

        $sActionViewClass = Toolkit::getInstance()->theme->getActionViewClass();

        $oResponse = new ActionResponse($bLayout);
        $oView = new $sActionViewClass($oController, $sAction, $aParams);

        $oResponse->setHtml($oView->fetch());

        return $oResponse;
    }
    
    public function redirect($path, $params = array())
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
    
    public function checkActionAccess($action, $params = false)
    {
        $aConfig = $this->config($action);
        
        // Проверка доступа
        if (!empty($aConfig['Access']))
        {
            $bAllow = false;
            foreach ($aConfig['Access'] as $access)
            {
                if (Toolkit::getInstance()->auth->checkAccess($access, $params))
                {
                    $bAllow = true; break;
                }
            }

            if (!$bAllow)
            {
                echo 'Доступа нет';
                exit();
            }
        }
    }

    public function t($category, $message, $params = [], $language = null)
    {
        return Core::app()->t($category, $message, $params, $language, 'ruxon/modules/'.$this->getModuleAlias().'/messages');
    }
}