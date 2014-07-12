<?php

class ThemeLayoutCell
{
    protected $sName;

    public function __construct($sName)
    {
        $this->sName = $sName;
    }

    public function getName()
    {
        return $this->sName;
    }

    public function fetch()
    {
        return false;
    }
}