<?php

class HasManyObjectRelation extends ObjectRelation
{
    public function get(Object $oObject, $aParams = array())
    {
        if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);

        $nLimit = 0;
        $nOffset = 0;

        $aLocCriteria = array(
            $this->aParams['Field'] => $oObject->getId()
        );

        $aLocOrder = array(
            'Pos' => 'ASC'
        );
        
        if (count($aParams) > 0) {
            if (isset($aParams['Criteria'])) {
                foreach ($aParams['Criteria'] as $key => $val) {
                    $aLocCriteria[$key] = $val;
                }
            }

            if (isset($aParams['Order'])) {
                $aLocOrder = $aParams['Order'];
            }

            if (isset($aParams['Limit'])) {
                $nLimit = intval($aParams['Limit']);
            }

            if (isset($aParams['Offset'])) {
                $nOffset = intval($aParams['Offset']);
            }
        }
        
        $aDefaultParams = isset($this->aParams['Params']) ? $this->aParams['Params'] : array();

        return $oMapper->find(ArrayHelper::merge($aDefaultParams, array(
            'Order' => $aLocOrder,
            'Criteria' => $aLocCriteria,
            'Limit' => $nLimit,
            'Offset' => $nOffset
        )));
    }
    
    public function set(Object $oObject, $mValue)
    {

        if (is_array($mValue) && count($mValue))
        {
            if (isset($this->aParams['ToModuleAlias']))
            {
                Core::import('Modules.'.$this->aParams['ToModuleAlias']);
            }

            if (is_array($mValue))
            {
                $oObject->aForSaveRelationsData[$this->aParams['Alias']] = array(
                    'Set' => $mValue
                );
            }

            /*$result = new SimpleCollection;

            $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);
            
            foreach ($mValue as $k => $itm)
            {
                if (!empty($itm['Id']))
                {
                    $obj = $oMapper->findById($itm['Id']);
                } else {
                    $obj = $oMapper->create();
                }
                
                unset($itm['Id']);
                
                $obj->import($itm);
                $obj->set($this->aParams['Field'], $oObject->getId());
                $obj->save();
                
                //Core::p($obj);
                
                $result->add($obj);
            }
            
            $oObject->simpleSet($this->getAlias(), $result);*/
        }

        return true;
    }
}