<?php

class ShopPriceBehavior extends OrmBehavior
{
    public $module_alias = 'Shop';
    public $mapper_alias = 'ShopItemMapper';
    
    protected $priceObject = null;
    
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
    
    public function priceObject()
    {
        if ($this->priceObject === null)
        {
            $this->priceObject = $this->mapper()->findFirst(array(
                'Criteria' => array(
                    'CatalogueItemId' => $this->getOwner()->getId()
                )
            ));
        }
        
        return $this->priceObject;
    }
    
    public function getShopItemId()
    {
        return $this->priceObject()->getId();
    }
    
    public function getPrice()
    {
        return $this->priceObject()->getPrice();
    }
    
    public function getOldPrice()
    {
        return $this->priceObject()->getOldPrice();
    }
    
    public function beforeSave()
    {
        $this->getOwner()->setMinPrice($this->getOwner()->simpleGet('Price'));

        $this->priceObject()->setPrice($this->getOwner()->simpleGet('Price'));
        $this->priceObject()->setOldPrice($this->getOwner()->simpleGet('OldPrice'));
        
        return true;
    }
    
    public function afterSave()
    {
        if (!$this->priceObject()->getId())
        {
            $this->priceObject()->setCatalogueItemId($this->getOwner()->getId());
        }
        
        $this->priceObject()->save();
        
        return true;
    }
    
    public function beforeDelete()
    {
        if ($this->priceObject()->getId())
        {
            $this->priceObject()->delete();
        }
        
        return true;
    }
    
    public function mapper()
    {
        return Manager::getInstance()->getMapper($this->mapper_alias);
    }
}