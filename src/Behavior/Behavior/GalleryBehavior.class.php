<?php

/**
 * Behavior attach image gallery to the any object
 * 
 * @version 7.2 
 * @author John Doe <john.doe@example.com>
 */
class GalleryBehavior extends OrmBehavior
{
    public $module_alias = 'Gallery';
    public $mapper_alias = 'GalleryItemMapper';
    public $category_mapper_alias = 'GalleryCategoryMapper';
    
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
        $categoryMapper = Manager::getInstance()->getMapper($this->category_mapper_alias);
        $aContainer = $mapper->getContainer();

        if (!$this->getOwner()->getGalleryId())
        {
            // Создаем галерею

            $oCategory = new GalleryCategory();
            $oCategory->setName($this->getOwner()->getName());
            $oCategory->setAlias(StringHelper::translit($this->getOwner()->getName()));
            $oCategory->setIsSystem(1);
            $oCategory->setIsActive(1);
            $categoryId = $oCategory->save();

            $this->getOwner()->setGalleryId($categoryId);
        } else {
            $categoryId = $this->getOwner()->getGalleryId();
            $oCategory = $categoryMapper->findById($categoryId);
        }

        // Удаляем все записи о фотках из базы
        $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $aContainer['Object'], $this->getDbConnectionAlias());
        $oCriteria = new CriteriaElement('category_id', Criteria::EQUAL, $categoryId);
        $oQuery->addCriteria($mapper->parseUpdateCriteria($oCriteria->renderWhere()));
        $oQuery->delete();

        // Добавляем фотки
        $images = $this->getOwner()->getImages();
        $images_names = $this->getOwner()->getImagesNames();
        if (is_array($images) && count($images))
        {
            foreach ($images as $k => $image) 
            {
                // Добавляем фотки
                $oImage = new GalleryItem();
                $oImage->setName($images_names[$k]);
                $oImage->setAlias($image);
                $oImage->setCategoryId($categoryId);
                $oImage->setPos($k + 1);
                $oImage->setIsActive(1);
                $oImage->save();
            }
            
            // set cover
            $oCategory->setCover($images[0]);
            $oCategory->save();
        }

        // done

        return true;
    }
    
    public function beforeDelete()
    {
        if ($this->getOwner()->getGalleryId())
        {
            $mapper = Manager::getInstance()->getMapper($this->mapper_alias);
            $aContainer = $mapper->getContainer();

            $categoryId = $this->getOwner()->getGalleryId();

            // Удаляем все записи о фотках из базы
            $oQuery = new DbUpdater(DbUpdater::TYPE_DELETE, $aContainer['TableName'], $aContainer['Object'], $this->getDbConnectionAlias());
            $oCriteria = new CriteriaElement('category_id', Criteria::EQUAL, $categoryId);
            $oQuery->addCriteria($mapper->parseUpdateCriteria($oCriteria->renderWhere()));
            $oQuery->delete();

            $this->getOwner()->getGallery()->delete();
        }

        return true;
    }
    
}