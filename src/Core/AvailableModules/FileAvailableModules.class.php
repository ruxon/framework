<?php


class FileAvailableModules extends AvailableModules
{
    protected $modules = [];

    public function __construct($params = array())
    {
        parent::__construct($params);

        $this->modules = include RX_PATH. '/ruxon/config/modules.inc.php';
    }

    public function checkInstalledModule($module)
    {
        return $this->getModuleById($module) ? true : false;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function getModuleById($id)
    {
        return !empty($this->modules[$id]) ? $this->modules[$id] : false;
    }

    public function updateModuleById($id, $data)
    {
        $this->modules[$id] = (array) $data;

        $this->saveModulesInfo();

        return true;
    }

    public function deleteModuleById($id)
    {
        if (!empty($this->modules[$id]))
        {
            unset($this->modules[$id]);

            return $this->saveModulesInfo();
        }

        return false;
    }

    protected function saveModulesInfo()
    {
        $path = RX_PATH.'/ruxon/config/modules.inc.php';

        file_put_contents($path, "<?php \n\nreturn ".var_export($this->modules, true).";");

        return true;
    }
}