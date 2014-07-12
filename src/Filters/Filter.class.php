<?php

abstract class Filter implements FilterInterface
{
    protected $aParams = array();

    public function __construct($aParams = array())
    {
        $this->aParams = $aParams;
    }

    protected function getParams()
    {
        return $this->aParams;
    }
}