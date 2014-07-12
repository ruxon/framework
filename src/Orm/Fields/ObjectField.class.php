<?php

abstract class ObjectField
{
    protected $aParams = array();

    public function __construct($aParams = array())
    {
        $this->aParams = $aParams;
    }

    public function getTitle()
    {
        return (isset($this->aParams['Title']) ? $this->aParams['Title'] : false);
    }

    public function getType()
    {
        return (isset($this->aParams['Type']) ? $this->aParams['Type'] : false);
    }

    public function getField()
    {
        return (isset($this->aParams['Field']) ? $this->aParams['Field'] : false);
    }

    public function getAlias()
    {
        return (isset($this->aParams['Name']) ? $this->aParams['Name'] : false);
    }

    abstract public function get(Object $oObject);
    abstract public function set(Object $oObject, $mValue);

    /**
     * Возвращает тулкит
     *
     * @return Toolkit
     */
    protected function getToolkit()
	{
		return Toolkit::getInstance();
	}
}