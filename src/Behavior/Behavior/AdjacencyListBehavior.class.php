<?php

class AdjacencyListBehavior extends OrmBehavior
{
    protected $childs = null;
    
    protected $parent = null;
    
    public function hasChilds()
    {
        if ($this->childs == null)
            $this->_loadChilds ();
               
        return $this->childs->count() ? true : false;
    }
    
    public function getChilds()
    {
        if ($this->childs == null)
            $this->_loadChilds ();
        
        return $this->childs;
    }
    
    public function getParent()
    {
        if ($this->getOwner()->getParentId())
        {
            if ($this->parent == null)
                $this->_loadParent ();
            
            return $this->parent;
        }
        
        return false;
    }
    
    protected function _loadChilds()
    {
        $this->childs = $this->getOwner()->mapper()->find(array(
            'Criteria' => array(
                'ParentId' => $this->getOwner()->getId()
            ),
            'Order' => array(
                'Pos' => 'ASC'
            )
        ));
    }
    
    protected function _loadParent()
    {
        $this->parent = $this->getOwner()->mapper()->findFirst(array(
            'Criteria' => array(
                'Id' => $this->getOwner()->getParentId()
            )
        ));
    }
}