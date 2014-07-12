<?php

class ManyToManyObjectRelation extends ObjectRelation
{
    public function get(Object $oObject, $aParams = array())
    {
        if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);
        $aContainer = $oMapper->getContainer();
        $sTable = $aContainer['TableName'];
        $sClassName = $aContainer['Object'];

        $oQuery = new DbFetcher($sTable, 'a');
        $oQuery->addJoinTable($this->aParams['TableName'], 'b', 'LEFT JOIN', 'a.id = b.'.$this->aParams['Field']);

        $oQuery->addSelectField('a.*');

        // Условия выборки
        $oCriteria = new CriteriaGroup('AND');
        $oCriteria->addElement(new CriteriaElement($this->aParams['Field2'], '=', (int)$oObject->getId()));
        $oQuery->addCriteria($oCriteria->renderWhere());

        if (isset($this->aParams['Order']))
        {
            foreach ($this->aParams['Order'] as $k => $dir)
            {
                $oQuery->addOrder($k, $dir);
            }

        } else {
            $oQuery->addOrder('Pos', 'ASC');
        }


        $oResult = new ObjectsCollection($oQuery);
        $aResult = $oQuery->fetchAll();

        foreach ($aResult as $itm) {
            $oResult->add(new $sClassName($this->convRealFieldToFields($itm, '', $aContainer)));
        }

        return $oResult;
    }
    
    public function set(Object $oObject, $mValue)
    {
        if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        if (is_array($mValue))
        {
            $oObject->aForSaveRelationsData[$this->aParams['Alias']] = array(
                'Delete' => 'all',
                'Add' => $mValue
            );
        }
        
        
        
        /*if (!$oObject->isNew())
        {
            $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapper']);
            $aContainer = $oMapper->getContainer();
            $sClassName = $aContainer['Object'];

            if (is_array($mValue))
            {
                // Удаляем все что есть
                $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $sClassName, $this->getDbConnectionAlias());
                $oCriteria = new CriteriaElement($this->aParams['Field'], Criteria::EQUAL, (int)$oObject->getId());

                $oQuery->addCriteria($this->parseUpdateCriteria($oCriteria->renderWhere()));

                return $oQuery->delete();

                if (count($mValue))
                {
                    foreach ($mValue as $val) 
                    {
                        // сохраняем в базу
                        $oQuery = new DbUpdater(DbUpdater::TYPE_INSERT, $aContainer['TableName'], $sClassName, $this->getDbConnectionAlias());
                        $oQuery->setElement(array(
                            $this->aParams['Field'] => (int)$oObject->getId(),
                            $this->aParams['Field2'] => $val
                        ));

                        return $oQuery->insert();
                    }
                }
            }

            return true;
        }*/
        
        return false;
    }
    
    public function eagerFetching(DbFetcher $oQuery, ObjectMapper $oParentMapper, $sPrefix, $aParams = array(), $aLocalParams = array())
    {
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);

        if ($oMapper) {

            $oQuery->addJoinTable($this->aParams['TableName'], $this->getAlias(), DbFetcher::JOIN_LEFT, $sPrefix.'.id'.' = '.$this->getAlias().'.'.$this->aParams['Field2']);

            // Условия выборки
            if (isset($aLocalParams['Criteria']) && is_object($aLocalParams['Criteria'])) {
                $oQuery->addCriteria(call_user_func(array($aLocalParams['Criteria'], 'renderWhere')));
            }

            if (isset($aLocalParams['Criteria']) && is_array($aLocalParams['Criteria']) && count($aLocalParams['Criteria']) > 0) {
                $oCriteria = new CriteriaGroup(Criteria::TYPE_AND);
                foreach ($aLocalParams['Criteria'] as $k => $itm) {
                    if (is_object($itm)) {
                        $oCriteria->addElement($itm);
                    } else if (is_array($itm)) {
                        $oCriteria->addElement(new CriteriaElement($k, $itm['Type'], $itm['Value']));
                    } else {
                        $oCriteria->addElement(new CriteriaElement($k, '=', $itm));
                    }

                }
                $oQuery->addCriteria($oMapper->parseFindCriteria($oCriteria->renderWhere(), $sPrefix.'_'.$this->getAlias()));
            }

            if (isset($aLocalParams['Criteria']) && is_string($aLocalParams['Criteria'])) {
                $oQuery->addCriteria($oMapper->parseFindCriteria($aLocalParams['Criteria'], $sPrefix.'_'.$this->getAlias()));
            }

            // Сортировка
            if (isset($aLocalParams['Order']) && is_array($aLocalParams['Order']) && count($aLocalParams['Order']) > 0) {
                foreach ($aLocalParams['Order'] as $k => $itm) {
                    $oQuery->addOrder($oMapper->parseFindField($k, $sPrefix.'_'.$this->getAlias()), $itm);
                }
            }

            if (isset($aLocalParams['With']) && is_array($aLocalParams['With']) && count($aLocalParams['With'])) {
                // TODO: Вложенные JOINы
            }
            
        }

        return $oQuery;
    }
}