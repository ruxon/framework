<?php

class DbFetcher
{
    const SELECT_ALL = '*';
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'ASC';

    const JOIN_LEFT = 'LEFT JOIN';
    const JOIN_RIGHT = 'RIGHT JOIN';
    const JOIN_INNER = 'INNER JOIN';

    protected $sMainTable;
    protected $sMainTableAlias;
    protected $aJoins = array();
    protected $mSelectFields = array();
    protected $sCriteria;
    protected $aOrder = array();
    protected $aGroup = array();
    protected $nLimit = 0;
    protected $nOffset = 0;
    
    protected $sDbConnection = 'default';

    public function __construct($sTable, $sAlias, $sDbConnection = 'default')
    {
        $this->sDbConnection = $sDbConnection;
        
        $this->clear($sTable, $sAlias);
    }

    public function clear($sTable, $sAlias)
    {
        $this->sMainTable = $sTable;
        $this->sMainTableAlias = $sAlias;

        $this->aJoins = array();
        $this->mSelectFields = array();
        $this->sCriteria = '';
        $this->aOrder = array();
        $this->nLimit = 0;
        $this->nOffset = 0;
    }

    public function addJoinTable($sTable, $sAlias, $sType, $sCondition)
    {
        $this->aJoins[] = array(
            'Name' => $sTable,
            'Alias' => $sAlias,
            'Type' => $sType,
            'Condition' => $sCondition
        );

        return true;
    }

    public function getMainTableAlias()
    {
        return $this->sMainTableAlias;
    }

    public function addSelectField($sField, $sAlias = '', $sFunction = false, $mFunctionParams = false)
    {
        if ($sField == self::SELECT_ALL && $sFunction === false) {
            $this->mSelectFields = self::SELECT_ALL;
        } else {
            if (is_array($this->mSelectFields)) {
                $this->mSelectFields[] = array(
                    'Name' => $sField,
                    'Alias' => $sAlias,
                    'Function' => $sFunction,
                    'FunctionParams' => $mFunctionParams
                );
            }
        }

        return $this;
    }

    public function clearSelectFields()
    {
        $this->mSelectFields = array();

        return $this;
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

    public function addOrder($sField, $sDirection = self::ORDER_ASC)
    {
        $this->aOrder[$sField] = $sDirection;

        return $this;
    }
    
    public function addGroup($sField)
    {
        $this->aGroup[] = $sField;

        return $this;
    }

    public function getOrder()
    {
        return $this->aOrder;
    }

    public function getCriteria()
    {
        return $this->sCriteria;
    }

    public function setLimit($nLimit)
    {
        $this->nLimit = $nLimit;

        return $this;
    }

    public function setOffset($nOffset)
    {
        $this->nOffset = $nOffset;

        return $this;
    }

    public function toArray()
    {
        $aResult = array();

        $aResult['Type'] = 'SELECT';
        $aResult['MainTable'] = array(
            'Name' => $this->sMainTable,
            'Alias' => $this->sMainTableAlias
        );

        if (count($this->aJoins)) {
            $aResult['Join'] = $this->aJoins;
        }

        $aResult['SelectFields'] = array();
        if (is_array($this->mSelectFields) && count($this->mSelectFields)) {
            $aResult['SelectFields'] = $this->mSelectFields;
        } else {
            $aResult['SelectFields'][] = array(
                'Name' => self::SELECT_ALL,
                'Alias' => '',
                'Finction' => '',
                'FunctionParams' => ''
            );
        }

        if ($this->sCriteria) {
            $aResult['Criteria'] = $this->sCriteria;
        }

        if ($this->nLimit) {
            $aResult['Limit'] = $this->nLimit;
        }

        if ($this->nOffset) {
            $aResult['Offset'] = $this->nOffset;
        }

        if (count($this->aOrder)) {
            $aResult['Order'] = array();
            foreach ($this->aOrder as $field => $direction) {
                $aResult['Order'][] = array(
                    'Field' => $field,
                    'Direction' => $direction
                );
            }
        }
        
        if (count($this->aGroup)) {
            $aResult['Group'] = $this->aGroup;
        }

        return $aResult;
    }

    public function fetch()
    {
        return $this->getDbConnection()->fetchRow($this);
    }

    public function fetchAll()
    {
        return $this->getDbConnection()->fetchArray($this);
    }

    public function fetchCell()
    {
        return $this->getDbConnection()->fetchCell($this);
    }

    protected function getDbConnection()
	{
		return Manager::getInstance()->getDb($this->sDbConnection);
	}
}