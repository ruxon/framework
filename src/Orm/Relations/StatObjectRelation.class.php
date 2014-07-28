<?php

class StatObjectRelation extends ObjectRelation
{
    public function get(Object $oObject, $aParams = array())
    {
        if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);

        $aLocCriteria = array(
            $this->aParams['Field'] => $oObject->getId()
        );

        if (!empty($aParams)) {
            if (isset($aParams['Criteria'])) {
                foreach ($aParams['Criteria'] as $key => $val) {
                    $aLocCriteria[$key] = $val;
                }
            }
        }


        $nRelation = $oMapper->findStat('count', 'c', isset($this->aParams['FunctionParams']) ? $this->aParams['FunctionParams'] : '*', array(
            'Criteria' => $aLocCriteria,
        ));

        return $nRelation;
    }

    public function eagerFetching(DbFetcher $oQuery, ObjectRelation $oRelation, ObjectMapper $oParentMapper, $sPrefix, $aParams = array(), $aLocalParams = array())
    {
        return $oQuery;
    }
}