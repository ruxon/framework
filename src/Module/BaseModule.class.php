<?php

/**
 * BaseModule: default class for a module
 *
 * @package Module
 * @version 8.0
 */
abstract class BaseModule
{
    use EmailEventable;
    use SmsEventable;

    protected $sModuleAlias;

    protected $aConfig = array();

    protected $aInfo = array();

    protected $_moduleObject;

    public function __construct()
    {
        if (!$this->sModuleAlias) {
            $name = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
            $this->sModuleAlias = substr($name, 0, strlen($name) - 6);
        }

        $this->load_info();
        $this->load_config();
        $this->init();
    }


    protected function load_config()
    {
        $path = RX_PATH.'/ruxon/config/modules/'.$this->sModuleAlias.'.inc.php';

        if (file_exists($path)) {
            $this->aConfig = include($path);
        } else {
            $this->aConfig = array();
        }

        return true;
    }

    protected function load_info()
    {
        $this->aInfo = Loader::loadConfigFile(RX_PATH.'/ruxon/modules/'.$this->sModuleAlias, 'module');

        return true;
    }

    protected function init()
    {
        $this->_moduleObject = (object) Core::app()->getModuleById($this->sModuleAlias);

        return false;
    }

    public function getId()
    {
        return $this->_moduleObject->getId();
    }

    public function info($sKey = false)
    {
        if ($sKey) {
            if (isset($this->aInfo[$sKey])) {
                return $this->aInfo[$sKey];
            } else {
                return false;
            }
        } else {
            return $this->aInfo;
        }
    }

    public function saveInfo($aInfo = array())
    {
        $path = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/module.json';

        file_put_contents($path, json_encode($aInfo));
        $this->aInfo = $aInfo;

        return true;
    }

    public function config($sKey = false)
    {
        if ($sKey) {
            if (isset($this->aConfig[$sKey])) {
                return $this->aConfig[$sKey];
            } else {
                return false;
            }
        } else {
            return $this->aConfig;
        }
    }

    public function saveConfig($aConfig = array())
    {
        $path = RX_PATH.'/ruxon/config/modules/'.$this->sModuleAlias.'.inc.php';

        file_put_contents($path, "<?php \n\nreturn ".var_export($aConfig, true).";");
        $this->aConfig = $aConfig;

        return true;
    }

    public function dbRevision()
    {
        return $this->_moduleObject->DbRevision;
    }

    public function saveDbRevision($value)
    {
        $this->_moduleObject->DbRevision = $value;
        Core::app()->updateModuleById($this->sModuleAlias, $this->_moduleObject);

        return true;
    }


    public function data_menu()
    {
        return false;
    }

    public function system_menu()
    {
        return false;
    }

    public function rules()
    {
        return false;
    }

    public function createUrl($path, $params = array())
    {
        return Toolkit::i()->urlManager->createUrl($path, $params);
    }

    public function getModelsList()
    {
        if (!is_dir(RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models'))
            return false;

        return rx_dir_list(RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models');
    }
}