<?php

abstract class ToolkitBase
{
    public function __construct($aParams = array()) 
    {
        if (count($aParams))
        {
            foreach ($aParams as $key => $val) 
            {
                $this->$key = $val;
            }
        }
    }
}