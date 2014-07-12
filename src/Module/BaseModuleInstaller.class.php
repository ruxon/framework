<?php

/**
 * BaseModuleInstaller: default class for a module installer
 *
 * @package Module
 * @version 8.0
 */
abstract class BaseModuleInstaller extends Ruxon
{
    protected $sModuleAlias = '';

    public function __construct($aData = array())
    {
        if (!$this->sModuleAlias) {
            $name = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
            $this->sModuleAlias = substr($name, 0, strlen($name) - 15);
        }

        parent::__construct($aData);
    }

    /**
     * Install: module install action
     *
     * @return boolean
     */
    public function install()
    {
        $module = Core::app()->getModuleById($this->sModuleAlias);

        if (!$module)
        {
            // 1. add row to the modules table
            $moduleDir = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias;

            $info = Loader::loadConfigFile($moduleDir, 'module');

            $module = array(
                'Name' => $info['Name'],
                'Description' => $info['Description'],
                'Version' => $info['Version'],
                'DbRevision' => '-1'
            );

            Core::app()->updateModuleById($this->sModuleAlias, $module);

            $sClassName = $this->sModuleAlias.'Module';
            $classNameWithNamespaces = '\ruxon\modules\\'.$this->sModuleAlias.'\classes\\'.$sClassName;
            Manager::getInstance()->setModule($this->sModuleAlias, class_exists($classNameWithNamespaces) ? new $classNameWithNamespaces : new $sClassName);

            // 2. create table structure
            $migrator = new MysqlDbMigrator($this->sModuleAlias);
            $migrator->migrateTo('last');


            // 3. copy default config to config folder
            $file1 = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/module.inc.php';
            $file2 = RX_PATH.'/ruxon/config/modules/'.$this->sModuleAlias.'.inc.php';
            if (file_exists($file1) && !file_exists($file2))
            {
                copy($file1, $file2);
            }

            return true;
        }

        return false;
    }

    public function update()
    {
        // 1. Check and apply new db migrations
        $migrator = new MysqlDbMigrator($this->sModuleAlias);
        $migrator->migrateTo('last');
    }

    /**
     * Uninstall: module uninstall action
     *
     * @return boolean
     */
    public function uninstall()
    {
        // 1. remove tables
        $migrator = new MysqlDbMigrator($this->sModuleAlias);
        $migrator->migrateTo(-1);

        // 2. remove row from the modules table
        Core::app()->deleteModuleById($this->sModuleAlias);

        // 3. Delete config
        $file = RX_PATH.'/ruxon/config/modules/'.$this->sModuleAlias.'.inc.php';
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }
}