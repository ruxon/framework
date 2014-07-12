<?php

class DbUpdater
{
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';

    protected $sMainTable;
    protected $sMainTableAlias;
    protected $sType;
    protected $aElement = array();
    protected $sCriteria = '';
    
    protected $sDbConnection = 'default';

    public function __construct($sType, $sTable, $sAlias, $sDbConnection = 'default')
    {
        $this->sDbConnection = $sDbConnection;
        
        $this->clear($sType, $sTable, $sAlias);
    }

    public function clear($sType, $sTable, $sAlias)
    {
        $this->sMainTable = $sTable;
        $this->sMainTableAlias = $sAlias;
        $this->sType = $sType;
        $this->aElement = array();
        $this->sCriteria = '';
    }

    public function getType()
    {
        return $this->sType;
    }

    public function getMainTableAlias()
    {
        return $this->sMainTableAlias;
    }

    public function addCriteria($sCriteria)
    {
        if ($this->sCriteria) {
            $this->sCriteria = $this->sCriteria.' '.Criteria::TYPE_AND.' '.$sCriteria;
        } else {
            $this->sCriteria = $sCriteria;
        }

        return $this;
    }

    public function getCriteria()
    {
        return $this->sCriteria;
    }

    public function setElement($aElement)
    {
        $this->aElement = $aElement;

        return true;
    }

    public function getElement()
    {
        return $this->aElement;
    }

    public function toArray()
    {
        $aResult = array();

        $aResult['Type'] = $this->getType();
        $aResult['MainTable'] = array(
            'Name' => $this->sMainTable,
            'Alias' => $this->sMainTableAlias
        );

        if ($this->sCriteria) {
            $aResult['Criteria'] = $this->sCriteria;
        }

        if (count($this->aElement)) {
            $aResult['Element'] = $this->aElement;
        }

        return $aResult;
    }
    
    public function insert()
    {
        return $this->getDbConnection()->insert($this);
    }

    public function update()
    {
        return $this->getDbConnection()->update($this);
    }

    public function delete()
    {
        return $this->getDbConnection()->delete($this);
    }

    protected function getDbConnection()
	{
		return Manager::getInstance()->getDb($this->sDbConnection);
	}
}