<?php

/**
 * Behavior attach files archive to the any object
 * 
 * @version 7.2 
 * @author John Doe <john.doe@example.com>
 */
class FilesBehavior extends OrmBehavior
{
    public $module_alias = 'Files';
    public $mapper_alias = 'FilesItemMapper';
    
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
        $mapper = Manager::getInstance()->getMapper($this->mapper_alias);
        $aContainer = $mapper->getContainer();

        if (!$this->getOwner()->getFilesArchiveId())
        {
            // Создаем галерею

            $oCategory = new FilesCategory();
            $oCategory->setName($this->getOwner()->getName());
            $oCategory->setAlias(StringHelper::translit($this->getOwner()->getName()));
            $oCategory->setIsSystem(1);
            $oCategory->setIsActive(1);
            $categoryId = $oCategory->save();

            $this->getOwner()->setFilesArchiveId($categoryId);
        } else {
            $categoryId = $this->getOwner()->getFilesArchiveId();
        }

        // Удаляем все записи о файлах из базы
        $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $aContainer['Object'], $this->getDbConnectionAlias());
        $oCriteria = new CriteriaElement('category_id', Criteria::EQUAL, $categoryId);
        $oQuery->addCriteria($mapper->parseUpdateCriteria($oCriteria->renderWhere()));
        $oQuery->delete();

        // Добавляем файлы
        $images = $this->getOwner()->getFiles();
        $images_names = $this->getOwner()->getFilesNames();
        if (is_array($images) && count($images))
        {
            foreach ($images as $k => $image) 
            {
                // Добавляем файлы
                $oImage = new FilesItem();
                $oImage->setName($images_names[$k]);
                $oImage->setAlias($image);
                $oImage->setCategoryId($categoryId);
                $oImage->setPos($k + 1);
                $oImage->setIsActive(1);
                $oImage->save();
            }
        }

        // done

        return true;
    }
    
    public function beforeDelete()
    {
        if ($this->getOwner()->getFilesArchiveId())
        {
            $mapper = Manager::getInstance()->getMapper($this->mapper_alias);
            $aContainer = $mapper->getContainer();

            $categoryId = $this->getOwner()->getFilesArchiveId();

            // Удаляем все записи о файлах из базы
            $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $aContainer['Object'], $this->getDbConnectionAlias());
            $oCriteria = new CriteriaElement('category_id', Criteria::EQUAL, $categoryId);
            $oQuery->addCriteria($mapper->parseUpdateCriteria($oCriteria->renderWhere()));
            $oQuery->delete();

            $this->getOwner()->getGallery()->delete();
        }

        return true;
    }
    
}