<?php

abstract class ObjectRelation
{
    protected $aParams = array();
    
    public function __construct($aParams = array())
    {
        $this->aParams = $aParams;
    }

    public function getModule()
    {
        return (isset($this->aParams['ToModule']) ? $this->aParams['ToModule'] : false);
    }

    public function getMapper()
    {
        return (isset($this->aParams['ToMapperAlias']) ? $this->aParams['ToMapperAlias'] : false);
    }

    public function getField()
    {
        return (isset($this->aParams['Field']) ? $this->aParams['Field'] : false);
    }

    public function getAlias()
    {
        return (isset($this->aParams['Alias']) ? $this->aParams['Alias'] : false);
    }

    abstract public function get(Object $oObject, $aParams = array());
    //abstract public function set(Object $oObject, $mValue);

    /**
     * Возвращает тулкит
     *
     * @return Toolkit
     */
    protected function getToolkit()
	{
		return Toolkit::getInstance();
	}
    
    public function convRealFieldToFields($aData, $sPrefix = '', $aContainer)
    {
        $aResult = array();

        foreach ($aContainer['Fields'] as $field => $data) {
            if (isset($data['Field']) && isset($aData[$sPrefix.$data['Field']])) {
                $aResult[$field] = $aData[$sPrefix.$data['Field']];
            }
        }

        return $aResult;
    }

    public function mapper()
    {
        return Manager::getInstance()->getMapper($this->getMapper());
    }
}