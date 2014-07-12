<?php

class BelongsToObjectRelation extends ObjectRelation
{
    public function get(Object $oObject, $aParams = array())
    {
        if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);
        $sGetter = 'get'.$this->aParams['Field'];
        $nId = call_user_func(array($oObject, $sGetter));

        if (!count($aParams)) {
            $oData = $oObject->simpleGet($this->getAlias());

            if ($oData && $oData->getId() == $nId) {
                return $oData;
            } else {
                $oData = $oMapper->findById($nId, $aParams);
                $oObject->simpleSet($this->getAlias(), $oData);

                return $oData;
            }
        } else {
            return $oMapper->findById($nId, $aParams);
        }
    }

    public function set(Object $oObject, $mValue)
    {

        if ($mValue)
        {
            $oObject->aForSaveRelationsDataBefore[$this->aParams['Alias']] = array(
                'SetOne' => $mValue
            );
        }

       /*if (is_object($oValue))
        {
            if (!$oValue->getId()) {
                $oValue->save();
            }

            $oObject->simpleSet($this->getAlias(), $oValue);
        } else if (is_array($oValue))
        {

            if (!empty($oValue['Id']))
            {
                $val = $this->mapper()->findById($oValue['Id']);
                $val->import($oValue);

            } else {
                $val = $this->mapper()->create($oValue);
            }

            $val->save();

            $oObject->simpleSet($this->getAlias(), $val);
        }


        $sSetter = 'set'.$this->getField();

        call_user_func_array(array($oObject, $sSetter), array($oObject->getId()));

        return true;*/

        return true;
    }

    public function eagerFetching(DbFetcher $oQuery, ObjectMapper $oParentMapper, $sPrefix, $aParams = array(), $aLocalParams = array())
    {
    	if (isset($this->aParams['ToModuleAlias']))
        {
            Core::import('Modules.'.$this->aParams['ToModuleAlias']);
        }
        
        $oMapper = Manager::getInstance()->getMapper($this->aParams['ToMapperAlias']);

        //echo '<pre>', print_r($oMapper, true), '</pre>'; die();

        $aContainer = $oMapper->getContainer();

        if ($oMapper) {
            $oParentFields = $oParentMapper->getFields();

            $oQuery->addJoinTable($aContainer['TableName'], $sPrefix.'_'.$this->getAlias(), DbFetcher::JOIN_LEFT, $sPrefix.'.'.$oParentFields->get($this->getField())->getField().' = '.$sPrefix.'_'.$this->getAlias().'.id');

            $oFields = $oMapper->getFields();
            foreach ($oFields as $alias => $field) {
                if ($field->getType() != 'Object' && $field->getField()) {
                    $oQuery->addSelectField($sPrefix.'_'.$this->getAlias().'.'.$field->getField(), $sPrefix.'_'.$this->getAlias().'_'.$field->getField());
                }
            }

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