<?php

abstract class Object extends Ruxon
{
    /**
     * Системное имя модуля
     *
     * @var string
     */
    protected $sModuleAlias;

    /**
     * Системное имя модели
     *
     * @var string
     */
	protected $sModelAlias;

    /**
     * Данные для сохранения
     * связанных объектов ПОСЛЕ сохранения
     * основного объекта
     *
     * @var array
     */
	public $aForSaveRelationsData = array();

    /**
     * Данные для сохранения
     * связанных объектов ДО сохранения
     * основного объекта
     *
     * @var array
     */
    public $aForSaveRelationsDataBefore = array();

    /**
     * Черновик
     *
     * @var boolean
     */
	public $bIsDirty = true;

    /**
     * Новый объект
     *
     * @var boolean
     */
	public $bIsNew = true;

    
    public $bIsLoaded = false;
    /**
     * Данные
     *
     * @var array
     */
	protected $aData = array();

    protected $aContainer = false;

    protected $sMapperAlias = '';
    
    protected $aErrors = array();
    
    protected $_c = null;

    /**
     * Конструктор
     *
     * @param mixed $mData
     */
    public function __construct($mData = false)
    {
        if (!$this->sModelAlias) {
            $this->sModelAlias = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
        }

        if (!$this->sMapperAlias) {
            $this->sMapperAlias = $this->sModelAlias.'Mapper';
            if (!class_exists($this->sMapperAlias)) {
                $this->sMapperAlias = '\ruxon\modules\\'.$this->sModuleAlias.'\models\mappers\\'.$this->sMapperAlias;
            }
        }

        $this->initDefaultValues();
        $this->attachBehaviors();

		if ($mData !== false) {
			if (is_array($mData)) {
				$this->import($mData);
			} else {
				$this->load(intval($mData));
			}
		}
        
        $this->bIsLoaded = true;
    }
    
    /**
     * Новый объект или нет
     *
     * @return boolean
     */
    public function isNew()
	{
		return $this->bIsNew;
	}

    /**
     * Содержит ли объект
     * несохраненные данные
     *
     * @return boolean
     */
	public function isDirty()
	{
		return $this->bIsDirty;
	}
    
    public function attachBehaviors($bh = array())
    {
        $behaviors = $this->mapper()->behaviors();
        parent::attachBehaviors($behaviors);
    }
    
    public function import($aData = array())
    {
        if (is_array($aData)) {
            
            $id = 0;
            if (isset($this->aData['Id']))
            {
                $id = $this->aData['Id'];
            }
            
            foreach ($this->mapper()->fields() as $alias => $field) 
            {
                if (isset($aData[$alias]))
                {
                    //echo $alias, '=', $aData[$alias], "\n";
                    //$this->aData[$alias] = $aData[$alias];
                    $setter = 'set'.$alias;
                    call_user_func(array($this, $setter), $aData[$alias]);
                } 
                /*else if (isset($aData[$alias]) && !$aData[$alias])
                {
                    if (!isset($field['AllowEmpty']) || $field['AllowEmpty'] == true)
                    {
                        $this->aData[$alias] = $aData[$alias];
                    }
                }*/
            }
            
            foreach ($this->mapper()->relations() as $alias => $rel) 
            {
                if (isset($aData[$alias]))
                {
                    $setter = 'set'.$alias; 
                    //echo $alias, '=', $aData[$alias], "\n";
                    call_user_func(array($this, $setter), $aData[$alias]);
                } 
                /*else if (isset($aData[$alias]) && !$aData[$alias])
                {
                    if (!isset($field['AllowEmpty']) || $field['AllowEmpty'] == true)
                    {
                        $this->aData[$alias] = $aData[$alias];
                    }
                }*/
            }
            
            
            if ($id)
            {
                $this->aData['Id'] = $id;
            }

            if (isset($this->aData['Id']) && $this->aData['Id']) {
                //$this->bIsDirty = false;
                $this->bIsNew = false;
            }

            return true;
        }

        return false;
    }
    
    public function setErrors($errors)
    {
        $this->aErrors = $errors;
        
        return true;
    }
    
    public function getErrors()
    {
        return $this->aErrors;
    }

    public function merge($aData = array())
    {
        if (is_array($aData)) {
            $this->aData = array_merge($this->aData, $aData);

            return true;
        }

        return false;
    }

    public function export()
    {
        $aRealData = $this->aData;
        $aData = array();
        foreach ($this->mapper()->fields() as $alias => $field) 
        {
            if (!isset($field['AllowEmpty']) || $field['AllowEmpty'] == true)
            {
                $aData[$alias] = '';
            } else if (isset($aRealData[$alias]) && !$aRealData[$alias]) {
                unset($aRealData[$alias]);
            }
        }
        
        return array_merge($aData, $aRealData);
    }

    public function load($nElementId)
    {
        $oObject = $this->mapper()->findById($nElementId);

        if ($oObject->getId()) {
            //$this->import($oObject->export());
            
            foreach ($oObject->export() as $alias => $value) {
                $this->aData[$alias] = $value;
            }
            $this->bIsNew = false;
            $this->bIsDirty = false;

            return true;
        }

        return false;
    }   
    
    public function onBeforeSave()
    {
        $this->raiseBehaviorEvent('beforeSave');
    }
    
    public function onAfterSave()
    {
        $this->raiseBehaviorEvent('afterSave');
    }
    
    public function onBeforeDelete()
    {
        $this->raiseBehaviorEvent('beforeDelete');
    }

    public function save($bLoadAfter = false)
    {
        $this->onBeforeSave();

        $nElementId = $this->mapper()->save($this);

        if ($nElementId) {
            $this->bIsNew = false;
            $this->bIsDirty = false;
            $this->aData['Id'] = $nElementId;
        }

        if ($bLoadAfter) {
            $this->refresh();
        }
        
        $this->onAfterSave();

        return $nElementId;
    }
    
    public function refresh()
    {
        if (!$this->bIsNew) {
            return $this->load(intval($this->aData['Id']));
        }

        return false;
    }

    public function delete()
    {
        $this->onBeforeDelete();
        
        return $this->mapper()->delete($this);
    }

    public function get($sField, $aParams = array())
    {
        return $this->mapper()->getValue($this, $sField, $aParams);
    }

    public function set($sField, $sValue)
    {
        $this->bIsDirty = true;
        return $this->mapper()->setValue($this, $sField, $sValue);
    }

    public function __call($sMethod, $aParams = array())
    {
        //$aContainer = $this->getContainer();

        $aParams = !count($aParams) ? array() : $aParams[0];

		$sName = substr($sMethod, 3);
        
        $res = false;

		if (strpos($sMethod, 'get') === 0) {
			$res = $this->get($sName, $aParams);
		} elseif (strpos($sMethod, 'set') === 0) {
			$res = $this->set($sName, $aParams);
		} 
        
        if (count($this->aBehaviors) && !$res)
        {
            return $this->getInBehaviors($sMethod);
        } else if ($res) {
            return $res;
        }
        
        
        
        /* elseif (strpos($sMethod, 'addTo') === 0) {
            $sName = substr($sMethod, 5);
            $this->addToCollection($sName, $aParams);
		} elseif (strpos($sMethod, 'delFrom') === 0) {
            $sName = substr($sMethod, 7);
			$this->delFromCollection($sName, $aParams);
		}*/

		return false;
    }

    public function simpleSet($sField, $mData)
    {
        $this->aData[$sField] = $mData;

        return true;
    }

    public function simpleGet($sField)
    {
        return (isset($this->aData[$sField]) ? $this->aData[$sField] : false);
    }

    /**
     * Возвращает контейнер модели
     *
     * @return array
     */
    public function container()
	{
        return $this->mapper()->getContainer();
	}

    /**
     * Возвращает маппер
     *
     * @return ObjectMapper
     */
	public function mapper()
	{
        return Manager::getInstance()->getMapper($this->sMapperAlias);
	}

    protected function initDefaultValues()
    {
        $aContainer = $this->container();

        foreach ($aContainer['Fields'] as $field => $data) {
            if (isset($data['Default'])) {
                $this->aData[$field] = $data['Default'];
            }
        }
    }
    
    public function getDbCriteria()
    {
        if($this->_c===null)
        {
            if(($c=$this->defaultScope())!==array())
                $this->_c = new Criteria($c);
        }
        
        return $this->_c;
    }
    
    public function getTableAlias()
    {
        $aContainer = $this->container();
        
        return $aContainer['TableName'];
    }
    
    public function module()
    {
        return Manager::getInstance()->getModule($this->sModuleAlias);
    }

    public function thumbUrl($field, $nWidth, $square = false, $nHeight = 0, $bucket = 'images')
    {
        if(!$this->isNew())
        {
            $oImage = new FileImageBase($this->get($field), $bucket);

            if ($nWidth > 0 && $nHeight == 0)
            {
                return $oImage->resizeToWidth($nWidth, $square);
            } else if ($nHeight > 0 && $nWidth == 0) {
                return $oImage->resizeToHeight($nHeight, $square);
            } else
            {
                return $oImage->resize($nWidth, $nHeight, $square);
            }
        }
    }
}