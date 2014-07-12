<?php

class Behavior
{
    public $owner = null;
    public $enabled = false;
    
    public function init() {}
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    public function attach($owner)
    {
        $this->enabled=true;
        $this->owner=$owner;
        
        return true;
    }
    
    public function detach($owner)
    {
        $this->owner = null;
        $this->enabled = false;
    }
}