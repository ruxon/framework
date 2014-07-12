<?php

abstract class StorageBase extends ToolkitBase
{
    public function __construct($aParams)
    {
        parent::__construct($aParams);
        
        $this->init();
    }
}