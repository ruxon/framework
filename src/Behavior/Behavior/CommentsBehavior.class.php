<?php

class CommentsBehavior extends OrmBehavior
{
    public $module_alias = 'Comments';
    public $mapper_alias = 'CommentsItemMapper';
    
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
        if (!$this->getOwner()->getComments()->getId())
        {
            // Создаем галерею

            $oCategory = new CommentsCategory();
            $oCategory->setName($this->getOwner()->getName());
            $categoryId = $oCategory->save();

            $this->getOwner()->setCommentsId($categoryId);
        }

        return true;
    }
    
    public function beforeDelete()
    {
        if ($this->getOwner()->getComments()->getId())
        {
            $mapper = Manager::getInstance()->getMapper($this->mapper_alias);
            $aContainer = $mapper->getContainer();

            $categoryId = $this->getOwner()->getCommentsId();

            // Удаляем все записи о комментах из базы
            $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $aContainer['Object'], $this->getDbConnectionAlias());
            $oCriteria = new CriteriaElement('category_id', Criteria::EQUAL, $categoryId);
            $oQuery->addCriteria($mapper->parseUpdateCriteria($oCriteria->renderWhere()));
            $oQuery->delete();

            if ($this->getOwner()->getCommentsId())
                $this->getOwner()->getComments()->delete();
        }

        return true;
    }
}