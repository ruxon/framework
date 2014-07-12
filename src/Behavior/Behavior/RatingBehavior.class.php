<?php

class RatingBehavior extends OrmBehavior
{
    public $module_alias = 'Rating';
    public $mapper_alias = 'RatingItemMapper';
    
    public function init() 
    {
        if (Core::app()->checkInstalledModule($this->module_alias))
            Core::import('Modules.'.$this->module_alias);
    }
    
    public function attach($owner)
    {
        if (Core::app()->checkInstalledModule($this->module_alias))
        {
            parent::attach($owner);
            
            return true;
        }
    }
    
    public function beforeSave()
    {
        if (!$this->getOwner()->getRatingId())
        {
            // Создаем галерею

            $name = $this->getOwner()->getName() ? $this->getOwner()->getName() : time();

            $oCategory = new RatingCategory();
            $oCategory->setName($name);
            $categoryId = $oCategory->save();

            $this->getOwner()->setRatingId($categoryId);
        }

        return true;
    }
    
    public function beforeDelete()
    {
        if ($this->getOwner()->getRatingId())
        {
            $mapper = Manager::getInstance()->getMapper($this->mapper_alias);
            $aContainer = $mapper->getContainer();

            $categoryId = $this->getOwner()->getRatingId();

            // Удаляем все записи о рейтинге из базы
            $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $aContainer['Object'], $this->getDbConnectionAlias());
            $oCriteria = new CriteriaElement('category_id', Criteria::EQUAL, $categoryId);
            $oQuery->addCriteria($mapper->parseUpdateCriteria($oCriteria->renderWhere()));
            $oQuery->delete();

            if ($this->getOwner()->getRatingId())
                $this->getOwner()->getRating()->delete();
        }

        return true;
    }
    
    public function checkUserRating()
    {
        if ($this->getOwner()->getId() && Toolkit::getInstance()->auth->isAuth()) {
            
            if ($this->getOwner()->getUserCreationId() == Toolkit::getInstance()->auth->getUserId()) {
                return false;
            } else {            
                return $this->getUserRatingData()->count() ? false : true;
            }
        }
        
        return false;
    }
    
    public function getUserRatingData()
    {
        $categoryId = $this->getOwner()->getRatingId();
        $mapper = Manager::getInstance()->getMapper($this->mapper_alias);
        
        return $mapper->find(array(
            'Criteria' => array(
                'user_modification_id' => Toolkit::getInstance()->auth->getUserId(),
                'CategoryId' => $categoryId
            )
        ));
    }
}