<?php

class Toolkit
{
	private static $instance = false;

	protected $aToolkits = array();

    private function __construct() {}

    private function __clone() {}

    public static function i()
    {
        return self::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new Toolkit();
        }

        return self::$instance;
    }

	public function addToolkit($sName, ToolkitBase $oToolkit)
	{
		$this->aToolkits[$sName] = $oToolkit;

		return true;
	}

	public function getToolkit($sName)
	{
		return (isset($this->aToolkits[$sName])? $this->aToolkits[$sName] : false);
	}

	public function  __call($name, $arguments)
	{
		if (count($this->aToolkits)) {
			foreach ($this->aToolkits as $tool) {
				$aMethods = get_class_methods($tool);
				if (method_exists($tool, $name)) {
					return call_user_func_array(array($tool, $name), $arguments);
				}
			}
		}

		return false;
	}
    
    public function __get($name) 
    {
        if (count($this->aToolkits)) 
        {	
            if (isset($this->aToolkits[$name]))
            {
                return $this->aToolkits[$name];
            }
		}

		return false;
    }
}