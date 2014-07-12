<?php

abstract class ObjectMapper extends Ruxon
{
    use EmailEventable;
    use SmsEventable;

    /**
     * Свойства
     *
     * @var array
     */
	protected $aFields = array();

    /**
     * Реальные свойства
     *
     * @var array
     */
	protected $aRealFields = array();

    /**
     * Связи
     *
     * @var array
     */
	protected $aRelations = array();

    protected $oFields;

    protected $oRelations;

    protected $sModuleAlias = '';

    protected $sModelAlias = false;

    protected $aContainer = false;

    protected $sDbConnectionAlias = 'default';

    protected $scopeActive = false;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->init();
        $this->attachBehaviors($this->behaviors());
    }

    public function tableName()
    {
        return $this->getContainer()['TableName'];
    }

    /**
     *
     * @param Object $oObject
     * @param <type> $sName
     * @param <type> $aParams
     * @return <type>
     */
    public function getValue(Object $oObject, $sName, $aParams = array())
    {
        $aData = $oObject->export();

        /**
         * Если свойство существует, и тип свойства не равен Object
         */
        if ($this->isFieldExists($sName) && $this->aFields[$sName]['Type'] != 'Object') {

            /**
             * Если свойство зависит от языка, и язык явно не указан
             */
            if (isset($this->aFields[$sName]['Params']['WithLang']) && $this->aFields[$sName]['Params']['WithLang'] == true) {

                /**
                 * Ищем значение для текущего языка
                 */
				if ($this->isFieldExists($sName.'_'.Core::app()->config()->getLang())) {
					return $this->get($oObject, $sName.'_'.Core::app()->config()->getLang());

				/**
                 * Если не нашли, то берем значение для языка по-умолчанию
                 */
				} else {
					return $this->get($oObject, $sName.'_'.Core::app()->config()->getDefaultLang());
				}
            /**
             * Если свойство не зависит от языка
             */
            } else {
                return $this->oFields->at($sName)->get($oObject);
            }


        /**
         * Если свойство существует, и тип свойтва Object (Связь)
         */
        } else if ($this->isFieldExists($sName) && $this->aFields[$sName]['Type'] == 'Object') {
           // Relations

            return $this->oRelations->at($sName)->get($oObject, $aParams);
        }

        return false;
    }

    /**
     *
     * @param Object $oObject
     * @param <type> $sName
     * @param <type> $mValue
     * @return <type>
     */
    public function setValue(Object $oObject, $sName, $mValue = '')
    {
        /**
         * Если свойство существует, и тип свойства не равен Object
         */
        if ($this->isFieldExists($sName) && $this->aFields[$sName]['Type'] != 'Object') {

            /**
             * Если свойство зависит от языка, и язык явно не указан
             */
            if (isset($this->aFields[$sName]['Params']['WithLang']) && $this->aFields[$sName]['Params']['WithLang'] == true) {

                /**
                 * Ищем значение для текущего языка
                 */
				if ($this->isFieldExists($sName.'_'.Core::app()->config()->getLang())) {
					return $this->set($oObject, $sName.'_'.Core::app()->config()->getLang(), $mValue);

				/**
                 * Если не нашли, то берем значение для языка по-умолчанию
                 */
				} else {
					return $this->set($oObject, $sName.'_'.Core::app()->config()->getDefaultLang(), $mValue);
				}
            /**
             * Если свойство не зависит от языка
             */
            } else {
                return $this->oFields->at($sName)->set($oObject, $mValue);
            }


        /**
         * Если свойство существует, и тип свойтва Object (Связь)
         */
        } else if ($this->isFieldExists($sName) && $this->aFields[$sName]['Type'] == 'Object') {
           // Relations

            return $this->oRelations->at($sName)->set($oObject, $mValue);
        }

        return false;
    }

    public function fields()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['Fields'])) {
            return $aContainer['Fields'];
        }

        return array();
    }

    public function fieldTitle($field)
    {
        if (!empty($this->getFields()[$field])) {
            return $this->getFields()[$field]->getTitle();
        }

        return false;
    }

    public function behaviors()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['Behaviors'])) {
            return $aContainer['Behaviors'];
        }

        return array();
    }

    public function relations()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['Relations'])) {
            return $aContainer['Relations'];
        }

        return array();
    }

    public function events()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['Events'])) {
            return $aContainer['Events'];
        }

        return array();
    }

    public function scopes()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['Scopes'])) {
            return $aContainer['Scopes'];
        }

        return array();
    }

    public function validation()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['Validation'])) {
            return $aContainer['Validation'];
        }

        return array();
    }

    public function defaultScope()
    {
        $aContainer = $this->getContainer();

        if (isset($aContainer['DefaultScope'])) {
            return $aContainer['DefaultScope'];
        }

        return array();
    }

    /**
     * Проверяет существование свойства
     *
     * @param string $sName
     * @return boolean
     */
	public function isFieldExists($sName)
	{
		if (isset($this->aFields[$sName])) {
			return true;
		}

		return false;
	}

    public function isRealFieldExists($sName)
	{
		if (isset($this->aRealFields[$sName])) {
			return true;
		}

		return false;
	}

    public function getFieldByRealName($sName)
    {
        if (isset($this->aRealFields[$sName])) {
			return $this->aRealFields[$sName];
		}

		return false;
    }

    public function __call($sMethod, $aParams = array())
    {
        $aParams = !count($aParams) ? array() : $aParams[0];
        $sName = substr($sMethod, 3);

        $res = false;

        // getter
        if (strpos($sMethod, 'get') === 0) {
            $res = $this->get($sName, $aParams);

            // setter
        } elseif (strpos($sMethod, 'set') === 0) {
            $res = $this->set($sName, $aParams);

            // attach event handler
        } elseif (strpos($sMethod, 'on') === 0) {
            $res = $this->on(substr($sMethod, 2), $aParams);
        } elseif (!empty($this->scopes()[$sMethod])) {
            $this->scopeActive = $this->scopes()[$sMethod];

            $res = $this;
        }

        if (count($this->aBehaviors) && !$res)
        {
            return $this->getInBehaviors($sMethod);
        } else if ($res) {
            return $res;
        }
    }

    /**
     *
     * @param <type> $aParams
     * @return ObjectsCollection
     */
    public function find($aParams = array())
    {
        $paramsDefault = $this->scopeActive ? $this->scopeActive : $this->defaultScope();
        $aParams = ArrayHelper::merge($paramsDefault, $aParams);
        $this->scopeActive = false;

        $sClassName = $this->sModelAlias;

        $oQuery = new DbFetcher($this->tableName(), $sClassName, $this->getDbConnectionAlias());

        // Eager fetching
        if (isset($aParams['With'])) {
            if (is_string($aParams['With'])) {
                $aParams['With'] = array($aParams['With']);
            }
        }

        foreach ($this->oFields as $alias => $field) {
            if ($field->getType() != 'Object' && $field->getField()) {
                $oQuery->addSelectField($sClassName.'.'.$field->getField(), $field->getField());
            }
        }

        // Условия выборки
		if (isset($aParams['Criteria']) && is_object($aParams['Criteria'])) {
			$oQuery->addCriteria($aParams['Criteria']->renderWhere());
		}

		if (isset($aParams['Criteria']) && is_array($aParams['Criteria']) && count($aParams['Criteria']) > 0) {
			$oCriteria = new CriteriaGroup('AND');
			foreach ($aParams['Criteria'] as $k => $itm) {
				if (is_object($itm)) {
					$oCriteria->addElement($itm);
				} else if (is_array($itm)) {
					$oCriteria->addElement(new CriteriaElement($k, $itm['Type'], $itm['Value']));
				} else {
					$oCriteria->addElement(new CriteriaElement($k, '=', $itm));
				}

			}
			$oQuery->addCriteria($this->parseFindCriteria($oCriteria->renderWhere()));
		}

        if (isset($aParams['Criteria']) && is_string($aParams['Criteria'])) {
            $oQuery->addCriteria($this->parseFindCriteria($aParams['Criteria']));
        }

        // Лимит выборки
		if (isset($aParams['Limit']) && $aParams['Limit'] > 0) {
			$oQuery->setLimit($aParams['Limit']);
		}

		// Смещение выборки
		if (isset($aParams['Offset'])) {
			$oQuery->setOffset($aParams['Offset']);
		}

		// Сортировка
		if (isset($aParams['Order']) && is_array($aParams['Order']) && count($aParams['Order']) > 0) {
			foreach ($aParams['Order'] as $k => $itm) {
				$oQuery->addOrder($this->parseFindCriteria($k), $itm);
                //$oQuery->addOrder($k, $itm);
			}
		}

        if (isset($aParams['Group']) && is_array($aParams['Group']) && count($aParams['Group']) > 0) {
			foreach ($aParams['Group'] as $k => $itm) {
                $oQuery->addGroup($itm);
			}
		}

        if (isset($aParams['With']) && is_array($aParams['With']) && count($aParams['With'])) {
            foreach ($aParams['With'] as $k => $rel) {
                $oRelation = false;
                $aLocalParams = array();

                if (is_array($rel)) {
                    $oRelation = $this->oRelations->get($k);
                    $aLocalParams = $rel;

                } else if (is_string($rel)) {
                    $oRelation = $this->oRelations->get($rel);
                }

                if ($oRelation) {
                    $oQuery = $oRelation->eagerFetching($oQuery, $this, $sClassName, $aParams, $aLocalParams);
                }

            }
        }

		$oResult = new ObjectsCollection($oQuery);
		$aResult = $oQuery->fetchAll();

        $classNameWithNamespaces = '\ruxon\modules\\'.$this->sModuleAlias.'\models\\'.$sClassName;

		foreach ($aResult as $itm) {

            $oItem = class_exists($sClassName) ? new $sClassName($this->convRealFieldToFields($itm)) : new $classNameWithNamespaces($this->convRealFieldToFields($itm));

            if (isset($aParams['With']) && is_array($aParams['With']) && count($aParams['With'])) {
                $this->parseJoinFields($oItem, $itm, $aParams['With'], $sClassName.'_');
            }

			$oResult->add($oItem);
		}

		return $oResult;
    }

    /**
     *
     * @param <type> $aParams
     * @return sClassName
     */
    public function findFirst($aParams = array())
    {
        $paramsDefault = $this->scopeActive ? $this->scopeActive : $this->defaultScope();
        $aParams = ArrayHelper::merge($paramsDefault, $aParams);
        $this->scopeActive = false;

        $sClassName = $this->sModelAlias;

        $oQuery = new DbFetcher($this->tableName(), $sClassName, $this->getDbConnectionAlias());

        // Eager fetching
        if (isset($aParams['With'])) {
            if (is_string($aParams['With'])) {
                $aParams['With'] = array($aParams['With']);
            }
        }

        foreach ($this->oFields as $alias => $field) {
            if ($field->getType() != 'Object' && $field->getField()) {
                $oQuery->addSelectField($sClassName.'.'.$field->getField(), $field->getField());
            }
        }

		// Условия выборки
		if (isset($aParams['Criteria']) && is_object($aParams['Criteria'])) {
			$oQuery->addCriteria($aParams['Criteria']->renderWhere());
		}

		if (isset($aParams['Criteria']) && is_array($aParams['Criteria']) && count($aParams['Criteria']) > 0) {
			$oCriteria = new CriteriaGroup('AND');
			foreach ($aParams['Criteria'] as $k => $itm) {
				if (is_object($itm)) {
					$oCriteria->addElement($itm);
				} else if (is_array($itm)) {
					$oCriteria->addElement(new CriteriaElement($k, $itm['Type'], $itm['Value']));
				} else {
					$oCriteria->addElement(new CriteriaElement($k, '=', $itm));
				}

			}
			$oQuery->addCriteria($this->parseFindCriteria($oCriteria->renderWhere()));
		}

		// Лимит выборки
        $oQuery->setLimit(1);

		// Смещение выборки
        $oQuery->setOffset(0);

		// Сортировка
		if (isset($aParams['Order']) && is_array($aParams['Order']) && count($aParams['Order']) > 0) {
			foreach ($aParams['Order'] as $k => $itm) {
				$oQuery->addOrder($this->parseFindField($k), $itm);
			}
		}

        if (isset($aParams['Group']) && is_array($aParams['Group']) && count($aParams['Group']) > 0) {
			foreach ($aParams['Group'] as $k => $itm) {
                $oQuery->addGroup($itm);
			}
		}

        if (isset($aParams['With']) && is_array($aParams['With']) && count($aParams['With'])) {
            foreach ($aParams['With'] as $k => $rel) {
                $oRelation = false;
                $aLocalParams = array();

                if (is_array($rel)) {
                    $oRelation = $this->oRelations->get($k);
                    $aLocalParams = $rel;

                } else if (is_string($rel)) {
                    $oRelation = $this->oRelations->get($rel);
                }

                if ($oRelation) {
                    $oQuery = $oRelation->eagerFetching($oQuery, $this, $sClassName, $aParams, $aLocalParams);
                }

            }
        }

        $classNameWithNamespaces = '\ruxon\modules\\'.$this->sModuleAlias.'\models\\'.$sClassName;

		$aResult = $oQuery->fetch();
		$oResult = class_exists($sClassName) ? new $sClassName($this->convRealFieldToFields($aResult)) :  new $classNameWithNamespaces($this->convRealFieldToFields($aResult));

        if (isset($aParams['With']) && is_array($aParams['With']) && count($aParams['With'])) {
            $this->parseJoinFields($oResult, $aResult, $aParams['With'], $sClassName.'_');
        }

		return $oResult;
    }

    /**
     *
     * @param <type> $sFunction
     * @param <type> $sAlias
     * @param <type> $mFunctionParams
     * @param <type> $aParams
     * @return <type>
     */
    public function findStat($sFunction, $sAlias, $mFunctionParams = false, $aParams = array())
    {
        $paramsDefault = $this->scopeActive ? $this->scopeActive : $this->defaultScope();
        $aParams = ArrayHelper::merge($paramsDefault, $aParams);
        $this->scopeActive = false;

        $sClassName = $this->sModelAlias;

        $oQuery = new DbFetcher($this->tableName(), $sClassName, $this->getDbConnectionAlias());

        // Eager fetching
        if (isset($aParams['With'])) {

        }

        $oQuery->addSelectField($mFunctionParams, $sAlias, $sFunction, $mFunctionParams);

		// Условия выборки
		if (isset($aParams['Criteria']) && is_object($aParams['Criteria'])) {
			$oQuery->addCriteria($aParams['Criteria']->renderWhere());
		}

		if (isset($aParams['Criteria']) && is_array($aParams['Criteria']) && count($aParams['Criteria']) > 0) {
			$oCriteria = new CriteriaGroup('AND');
			foreach ($aParams['Criteria'] as $k => $itm) {
				if (is_object($itm)) {
					$oCriteria->addElement($itm);
				} else if (is_array($itm)) {
					$oCriteria->addElement(new CriteriaElement($k, $itm['Type'], $itm['Value']));
				} else {
					$oCriteria->addElement(new CriteriaElement($k, '=', $itm));
				}

			}
			$oQuery->addCriteria($this->parseFindCriteria($oCriteria->renderWhere()));
		}

		// Лимит выборки
        $oQuery->setLimit(1);

		// Смещение выборки
        $oQuery->setOffset(0);

		// Сортировка
		if (isset($aParams['Order']) && is_array($aParams['Order']) && count($aParams['Order']) > 0) {
			foreach ($aParams['Order'] as $k => $itm) {
				$oQuery->addOrder($this->parseFindField($k), $itm);
			}
		}

        if (isset($aParams['Group']) && is_array($aParams['Group']) && count($aParams['Group']) > 0) {
			foreach ($aParams['Group'] as $k => $itm) {
                $oQuery->addGroup($itm);
			}
		}

        if (isset($aParams['With']) && is_array($aParams['With']) && count($aParams['With'])) {
            foreach ($aParams['With'] as $k => $rel) {
                $oRelation = false;
                $aLocalParams = array();

                if (is_array($rel)) {
                    $oRelation = $this->oRelations->get($k);
                    $aLocalParams = $rel;

                } else if (is_string($rel)) {
                    $oRelation = $this->oRelations->get($rel);
                }

                if ($oRelation) {
                    $oQuery = $oRelation->eagerFetching($oQuery, $this, $sClassName, $aParams, $aLocalParams);
                }

            }
        }

		$mResult = $oQuery->fetchCell();

		return $mResult;
    }

    public function count($aParams = array())
    {
        return $this->findStat('count', 'count', $this->sModelAlias.'.id', $aParams);
    }

    /**
     *
     * @param <type> $sField
     * @param <type> $mValue
     * @param array $aParams
     * @return <type>
     */
    public function findByField($sField, $mValue, $aParams = array())
    {
        $aParams['Criteria'][$sField] = $mValue;

        return $this->find($aParams);
    }

    /**
     *
     * @param <type> $sField
     * @param <type> $mValue
     * @param array $aParams
     * @return <type>
     */
    public function findFirstByField($sField, $mValue, $aParams = array())
    {
        $aParams['Criteria'][$sField] = $mValue;

        return $this->findFirst($aParams);
    }

    /**
     *
     * @param <type> $nElementId
     * @param <type> $aParams
     * @return <type>
     */
    public function findById($nElementId, $aParams = array())
    {
        return $this->findFirstByField('Id', $nElementId, $aParams);
    }

    /**
     *
     * @param <type> $aIds
     * @param <type> $aParams
     * @return <type>
     */
    public function findByIds($aIds, $aParams = array())
    {
        $aParams['Criteria']['Id'] = array(
            'Type' => Criteria::IN,
            'Value' => $aIds
        );

        return $this->find($aParams);
    }

    public function create($aData = array())
    {
        $sClassName = $this->sModelAlias;
        $classNameWithNamespaces = '\ruxon\modules\\'.$this->sModuleAlias.'\models\\'.$sClassName;

        if (isset($aData['Id']) && $aData['Id']) {
            $oObject = class_exists($sClassName) ? new $sClassName($aData['Id']) : new $classNameWithNamespaces($aData['Id']);
            $oObject->import($aData);
        } else {
            $oObject = class_exists($sClassName) ? new $sClassName($aData) : new $classNameWithNamespaces($aData);
        }

        return $oObject;
    }

    public function validate(Object $oObject)
    {
        $scenario = $oObject->isNew() ? 'Create' : 'Update';

        $validator = new Validator();
        $aData = $oObject->export();
        foreach ($this->fields() as $alias => $field)
        {
            if (!empty($field['Validation']))
            {
                $val_group = new ValidationRulesGroup();

                foreach ($field['Validation'] as $v_type => $v_params)
                {
                    if (!empty($v_params['Scenario']) && $v_params['Scenario'] != $scenario)
                    {
                        continue;
                    } else if (!empty($v_params['Scenario'])) {
                        unset($v_params['Scenario']);
                    }

                    if ($v_type == 'Required' && $v_params === false)
                    {
                        continue;
                    }

                    if (!is_array($v_params)) $v_params = array($v_params);
                    $class_name = 'Validation'.$v_type.'Rule';
                    $all_params = array_merge(array($alias, @$field['Title']), $v_params);
                    $val_group->add(new $class_name($all_params));
                }

                $validator->addGroup($val_group);
            }
        }

        if (($res = $validator->forceCheck($aData)) === true)
        {
            return true;
        } else {
            $oObject->setErrors($res);
        }

        return false;
    }

    public function save(Object $oObject)
    {
        if ($this->validate($oObject))
        {
            if ($this->beforeSave($oObject))
            {
                if (!$oObject->isNew())
                {
                    $res = $this->update($oObject);
                } else {
                    $res = $this->insert($oObject);
                }

                $this->afterSave($oObject);
                return $res;
            }
        }

        return false;
    }

    public function insert(Object $oObject)
    {
        if ($oObject->isNew())
        {
            $this->saveForRelationData($oObject, true);

            $aContainer = $this->getContainer();
            $sClassName = $this->sModelAlias;

            $oQuery = new DbUpdater(DbUpdater::TYPE_INSERT, $this->tableName(), $sClassName, $this->getDbConnectionAlias());

            $aInput = array();
            $aData = $oObject->export();
            unset($aData['Id']);

            foreach ($this->fields() as $alias => $field)
            {
                if (!empty($field['Virtual']) && $field['Virtual'])
                {
                    unset($aData[$alias]);
                }
            }

            foreach ($aData as $k => $itm) {
                if (!key_exists($k, $this->fields()))
                {
                    unset($aData[$k]);
                } else {
                    $aInput[$this->parseUpdateField($k)] = $itm;
                }
            }

            $oQuery->setElement($aInput);

            $res = $oQuery->insert();
            $oObject->setId($res);

            $this->saveForRelationData($oObject);

            return $res;
        } else {
            return false;
        }
    }

    public function update(Object $oObject)
    {
        if (!$oObject->isNew())
        {
            $this->saveForRelationData($oObject, true);

            $aContainer = $this->getContainer();
            $sClassName = $this->sModelAlias;

            $oQuery = new DbUpdater(DbUpdater::TYPE_UPDATE, $this->tableName(), $sClassName, $this->getDbConnectionAlias());
            $aInput = array();
            $aData = $oObject->export();
            unset($aData['Id']);

            foreach ($this->fields() as $alias => $field)
            {
                if (!empty($field['Virtual']) && $field['Virtual'])
                {
                    unset($aData[$alias]);
                }
            }

            foreach ($aData as $k => $itm) {

                if (!key_exists($k, $this->fields()))
                {
                    unset($aData[$k]);
                } else {
                    $aInput[$this->parseUpdateField($k)] = $itm;
                }
            }

            $oQuery->setElement($aInput);
            $oCriteria = new CriteriaElement('Id', Criteria::EQUAL, $oObject->getId());
            $oQuery->addCriteria($this->parseUpdateCriteria($oCriteria->renderWhere()));

            $oQuery->update();

            $this->saveForRelationData($oObject);

            return $oObject->getId();
        } else {
            return false;
        }
    }

    public function delete(Object $oObject)
    {
        if (!$oObject->isNew()) {

            if ($this->beforeDelete($oObject))
            {

                $sClassName = $this->sModelAlias;

                $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $this->tableName(), $sClassName, $this->getDbConnectionAlias());
                $oCriteria = new CriteriaElement('Id', Criteria::EQUAL, $oObject->getId());

                $oQuery->addCriteria($this->parseUpdateCriteria($oCriteria->renderWhere()));

                return $oQuery->delete();
            }
        }

        return false;
    }

    public function deleteById($nId)
    {
        $aParams = array(
            'Criteria' => array(
                'Id' => $nId
            )
        );

        return $this->deleteByParams($aParams);
    }

    public function deleteByIds($aIds)
    {
        $aParams = array(
            'Criteria' => array(
                'Id' => array(
                    'Type' => Criteria::IN,
                    'Value' => $aIds
                )
            )
        );

        return $this->deleteByParams($aParams);
    }

    public function deleteByParams($aParams = array())
    {
        $aContainer = $this->getContainer();
        $sClassName = $this->sModelAlias;

        $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $this->tableName(), $sClassName, $this->getDbConnectionAlias());

        // Условия выборки
		if (isset($aParams['Criteria']) && is_object($aParams['Criteria'])) {
			$oQuery->addCriteria($aParams['Criteria']->renderWhere());
		}

		if (isset($aParams['Criteria']) && is_array($aParams['Criteria']) && count($aParams['Criteria']) > 0) {
			$oCriteria = new CriteriaGroup('AND');
			foreach ($aParams['Criteria'] as $k => $itm) {
				if (is_object($itm)) {
					$oCriteria->addElement($itm);
				} else if (is_array($itm)) {
					$oCriteria->addElement(new CriteriaElement($k, $itm['Type'], $itm['Value']));
				} else {
					$oCriteria->addElement(new CriteriaElement($k, '=', $itm));
				}

			}
			$oQuery->addCriteria($this->parseUpdateCriteria($oCriteria->renderWhere()));
		}

        return $oQuery->delete();
    }

    public function getFields()
    {
        return $this->oFields;
    }

    public function getRelations()
    {
        return $this->oRelations;
    }

    /**
     * Возвращает контейнер модели
     *
     * @return array
     */
    public function getContainer()
	{
        if ($this->aContainer === false) {

            $path = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/model.inc.php';
            $path2 = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/model.json';

            if (file_exists($path)) {
                $this->aContainer = include($path);
            } elseif (file_exists($path2)) {
                $this->aContainer = (array) json_decode(file_get_contents($path2));
            } else {
                $this->aContainer = array();
            }

            $this->aContainer['Object'] = $this->sModelAlias;

            $path = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/fields.inc.php';
            $path2 = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/fields.json';

            if (file_exists($path)) {
                $this->aContainer['Fields'] = include($path);
            } elseif (file_exists($path2)) {
                $this->aContainer['Fields'] = (array) json_decode(file_get_contents($path2));
            } else {
                $this->aContainer['Fields'] = array();
            }

            $path = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/relations.inc.php';
            $path2 = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/relations.json';

            if (file_exists($path)) {
                $this->aContainer['Relations'] = include($path);
            } elseif (file_exists($path2)) {
                $this->aContainer['Relations'] = (array) json_decode(file_get_contents($path2));
            } else {
                $this->aContainer['Relations'] = array();
            }

            $path = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/behaviors.inc.php';
            $path2 = RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/config/models/'.$this->sModelAlias.'/behaviors.json';

            if (file_exists($path)) {
                $this->aContainer['Behaviors'] = include($path);
            } elseif (file_exists($path2)) {
                $this->aContainer['Behaviors'] = (array) json_decode(file_get_contents($path2));
            } else {
                $this->aContainer['Behaviors'] = array();
            }
        }

        return $this->aContainer;
	}

    public function parseFindField($sField, $sPrefix = false)
    {
        $fields = $this->fields();

        if (!$sPrefix) {
            $sPrefix = $this->sModelAlias;
        }

        if (count($fields)) {
            $aId = $fields['Id'];
            unset($fields['Id']);
            $fields['Id'] = $aId;

            foreach ($fields as $alias => $field) {
                if (!empty($field['Field'])) {
                    $sField = StringHelper::replace($this->sModelAlias.'.'.$alias, $sPrefix.'.'.$field['Field'], $sField);
                    $sField = StringHelper::replace($this->tableName().'.'.$alias, $sPrefix.'.'.$field['Field'], $sField);
                    $sField = StringHelper::replace($alias, $sPrefix.'.'.$field['Field'], $sField);
                }
            }
        }

        return $sField;
    }

    public function parseUpdateField($sField)
    {

        $aContainer = $this->getContainer();


        if (is_array($aContainer) && count($aContainer)) {
            if ($this->fields()) {
                /*if (isset($aContainer['Fields'][$sField]) && isset($aContainer['Fields'][$sField]['WithLang']) && $aContainer['Fields'][$sField]['WithLang']) {
                    if (isset($aContainer['Fields'][$sField.'_'.Core::app()->config()->getLang()])) {
                        $sField = $sField.'_'.Core::app()->config()->getLang();
                    } else {
                        $sField = $sField.'_'.Core::app()->config()->getDefaultLang();
                    }
                }*/

                foreach ($this->fields() as $alias => $field) {
                    if (!empty($field['Field']) && $alias == $sField) {
                        //$sField = StringHelper::replace($this->sModelAlias.'.'.$alias, $field['Field'], $sField);
                        //$sField = StringHelper::replace($this->tableName().'.'.$alias, $field['Field'], $sField);
                        $sField = StringHelper::replace($alias, $field['Field'], $sField);
                        //echo $alias.':'.$field['Field']; echo "\n";
                    }
                }
                //die();
            }
        }

        //echo $sField, "\n";

        return $sField;
    }

    public function parseFindCriteria($sQuery, $sPrefix = false)
    {
        //echo 'before: ', $sQuery, '<br />';
        $aContainer = $this->getContainer();

        if (!$sPrefix) {
            $sPrefix = $this->sModelAlias;
        }

        $aContainer['Fields'] = $this->fields();


        if (is_array($aContainer) && count($aContainer)) {
            if ($this->fields()) {
                $aId = $aContainer['Fields']['Id'];
                unset($aContainer['Fields']['Id']);
                $aContainer['Fields']['Id'] = $aId;
                foreach ($aContainer['Fields'] as $alias => $field) {
                    if (!empty($field['Field'])) {
                        $sQuery = StringHelper::replace($this->sModelAlias.'.'.$alias, $sPrefix.'.'.$field['Field'], $sQuery);
                        $sQuery = StringHelper::replace($this->tableName().'.'.$alias, $sPrefix.'.'.$field['Field'], $sQuery);
                        $sQuery = StringHelper::replace($alias, $sPrefix.'.'.$field['Field'], $sQuery);
                    }
                }
            }
        }

        return $sQuery;
    }

    public function parseUpdateCriteria($sQuery)
    {
        $aContainer = $this->getContainer();
        $aContainer['Fields'] = $this->fields();

        if (is_array($aContainer) && count($aContainer)) {
            if (count($aContainer['Fields'])) {
                $aId = $aContainer['Fields']['Id'];
                unset($aContainer['Fields']['Id']);
                $aContainer['Fields']['Id'] = $aId;
                foreach ($aContainer['Fields'] as $alias => $field) {
                    if (!empty($field['Field'])) {
                        $sQuery = StringHelper::replace($this->sModelAlias.'.'.$alias, $field['Field'], $sQuery);
                        $sQuery = StringHelper::replace($this->tableName().'.'.$alias, $field['Field'], $sQuery);
                        $sQuery = StringHelper::replace($alias, $field['Field'], $sQuery);
                    }
                }
            }
        }


        return $sQuery;
    }

    public function parseJoinFields(Object $oObject, $aData, $aWith, $sPrefix)
    {
        foreach ($aWith as $k => $rel) {
            $oRelation = false;
            $aLocalParams = array();

            if (is_array($rel)) {
                $oRelation = $this->oRelations->get($k);
                $aLocalParams = $rel;

            } else if (is_string($rel)) {
                $oRelation = $this->oRelations->get($rel);
            }

            if ($oRelation) {
                $oMapper = Manager::getInstance()->getMapper($oRelation->getMapper());
                $aCont = $oMapper->getContainer();
                $sClassName2 = $aCont['Object'];
                $aData = $oMapper->convRealFieldToFields($aData, $sPrefix.$oRelation->getAlias().'_');

                $oObject2 = new $sClassName2($aData);

                if (isset($aWith[$oRelation->getAlias()]['With']) && is_array($aWith[$oRelation->getAlias()]['With']) && count($aWith[$oRelation->getAlias()]['With'])) {
                    $this->parseJoinFields($oObject2, $aData, $aWith[$oRelation->getAlias()]['With'], $sPrefix.$oRelation->getAlias().'_');
                }
            }


        }

        return $oObject;
    }

    public function convRealFieldToFields($aData, $sPrefix = '')
    {
        $aResult = array();

        foreach ($this->fields() as $field => $data) {
            if (isset($data['Field']) && isset($aData[$sPrefix.$data['Field']])) {
                $aResult[$field] = $aData[$sPrefix.$data['Field']];
            }
        }

        return $aResult;
    }

    public function t($category, $message, $params = [], $language = null)
    {
        return Core::app()->t($category, $message, $params, $language, 'ruxon/modules/'.$this->sModuleAlias.'/messages');
    }

    protected function saveForRelationData($oObject, $before = false)
    {
        $saveForRelation = $before ? $oObject->aForSaveRelationsDataBefore : $oObject->aForSaveRelationsData;

        $objectId = $before ? 0 : (int) $oObject->getId();

        if (!empty($saveForRelation))
        {
            $relations = $this->relations();

            foreach ($saveForRelation as $rel => $val)
            {
                $relation = $relations[$rel];

                foreach ($val as $action => $data)
                {
                    $oRelMapper = Manager::getInstance()->getMapper($relation['ToMapperAlias']);
                    $aRelContainer = $oRelMapper->getContainer();
                    $sRelClassName = $aRelContainer['Object'];


                    switch ($action)
                    {
                        case 'Delete':
                            // Удаляем все что есть
                            $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $relation['TableName'], $sRelClassName, $this->getDbConnectionAlias());
                            $oCriteria = new CriteriaElement($relation['Field2'], Criteria::EQUAL, $objectId);
                            $oQuery->addCriteria($this->parseUpdateCriteria($oCriteria->renderWhere()));
                            $oQuery->delete();
                            break;

                        case 'Add':
                            // сохраняем в базу

                            if (is_array($data))
                            {
                                foreach ($data as $data_val)
                                {
                                    $oQuery = new DbUpdater(DbUpdater::TYPE_INSERT, $relation['TableName'], $sRelClassName, $this->getDbConnectionAlias());
                                    $oQuery->setElement(array(
                                        $relation['Field'] => $data_val,
                                        $relation['Field2'] => $objectId
                                    ));

                                    $oQuery->insert();
                                }
                            }
                            break;

                        // Has Many
                        case 'Set':
                            if (is_array($data))
                            {
                                $result = new SimpleCollection;

                                foreach ($data as $itm)
                                {

                                    if (!empty($itm['Id']))
                                    {
                                        $obj = $oRelMapper->findById($itm['Id']);
                                    } else {
                                        $obj = $oRelMapper->create();
                                    }



                                    unset($itm['Id']);

                                    $obj->import($itm);
                                    $obj->set($relation['Field'], $objectId);
                                    $obj->save();


                                    $result->add($obj);
                                }
                            }
                            break;

                            // Belongs To
                            case 'SetOne':

                                if (is_array($data))
                                {

                                    if (!empty($data['Id']))
                                    {
                                        $obj = $oRelMapper->findById($data['Id']);
                                    } else {
                                        $obj = $oRelMapper->create($data);
                                    }
                                    unset($data['Id']);

                                    $obj->import($data);
                                    $id = $obj->save();


                                    $oObject->set($relation['Field'], $id);
                                    $oObject->set($rel, $obj);
                                } else if (is_object($data))
                                {
                                    if (!$data->getId())
                                    {
                                        $data->save();
                                    }

                                    $oObject->set($rel, $data);
                                }
                            break;
                    }
                }
            }

            if ($before)
            {
                $oObject->aForSaveRelationsDataBefore = array();
            } else {
                $oObject->aForSaveRelationsData = array();
            }

            return true;
        }

        return false;
    }

    protected function init()
    {
        if (!$this->sModelAlias) {
            $name = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
            $this->sModelAlias = substr($name, 0, strlen($name) - 6);
        }


        $this->oFields = new ObjectFieldsCollection();
        $this->oRelations = new ObjectRelationsCollection();

		if (count($this->fields())) {
			foreach ($this->fields() as $alias => $field) {
				$this->initField($alias, $field['Type'], $field, isset($field['Default']) ? $field['Default'] : '');
			}
		}

		if (count($this->relations())) {
			foreach ($this->relations() as $alias => $relation) {
				$this->initRelation($alias, $relation);
			}
		}

		return false;
    }

    /**
     * Инициализирует свойство
     *
     * @param string $sName
     * @param string $sType
     * @param array $aParams
     * @param mixed $mDefaultValue
     * @return boolean
     */
    protected function initField($sName, $sType, $aParams = array(), $mDefaultValue = '')
	{
		if (array_search($sName, $this->aFields) === false) {

			$this->aFields[$sName] = array(
				'Type' => $sType,
				'Params' => $aParams
			);

			if (isset($aParams['Field'])) {
				$this->aRealFields[$aParams['Field']] = $sName;
			}

            $aParams['Name'] = $sName;

            //if (key_exists($sType, Vars::getInstance()->get('DataModel', 'Fields')->getData())) {
                $sClassName = $sType.'ObjectField';
                $this->oFields->add(new $sClassName($aParams), $sName);
            //}

			return true;
		}

		return false;
	}

    /**
     * Инициализирует связь
     *
     * @param string $sName
     * @param array $aRelation
     * @return boolean
     */
	protected function initRelation($sName, $aRelation)
	{
		if (isset($aRelation['Alias']) && isset($aRelation['Type'])) {
			//$sAlias = $aRelation['Alias'];
			//unset($aRelation['Alias']);

            $sType = $aRelation['Type'];

            $aParams = $aRelation;
            $aParams['Name'] = $sName;

			$this->aRelations[$sType][$sName] = $aRelation;

            //if (key_exists($sType, Vars::getInstance()->get('DataModel', 'Relations')->getData())) {
                $sClassName = $sType.'ObjectRelation';
                $this->oRelations->add(new $sClassName($aParams), $sName);
            //}


			$this->initField($sName, 'Object');

			return true;
		}

		return false;
	}

    public function beforeSave($oObject)
    {
        if ($oObject->isNew() && $this->isFieldExists('UserCreationId') && !$oObject->getUserCreationId())
        {
            $oObject->setUserCreationId(Toolkit::getInstance()->auth->getUserId());
        }

        if ($this->isFieldExists('DateModification'))
        {
            $oObject->setDateModification(date("Y-m-d H:i:s"));
        }

        if ($oObject->isNew())
        {
            if ($this->isFieldExists('DateCreation'))
            {
                $oObject->setDateCreation(date("Y-m-d H:i:s"));
            }

            if ($this->isFieldExists('Pos') && !$oObject->getPos())
            {
                $maxPos = $this->findStat('MAX', 'fst', 'pos');
                $oObject->setPos($maxPos + 1);
            }
        }

        return true;
    }

    public function afterSave($oObject) {}

    public function beforeDelete($oObject) { return true; }

    protected function getDbConnectionAlias()
    {
        return $this->sDbConnectionAlias;
    }

    /**
     * Возвращает тулкит
     *
     * @return Toolkit
     */
    protected function getToolkit()
	{
		return Toolkit::getInstance();
	}

    /**
     * Возвращает шаблон
     *
     * @return Template
     */
    protected function getTemplate()
	{
		return $this->getToolkit()->getTemplate();
	}
}