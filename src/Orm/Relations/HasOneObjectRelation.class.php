<?php

class HasOneObjectRelation extends ObjectRelation
{
    public function get(Object $oObject, $aParams = array())
    {
        if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);

        $aParams['Criteria'][$this->aParams['Field']] = $oObject->getId();

        return $oMapper->findFirst($aParams);
    }
}