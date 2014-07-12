<?php

abstract class CacheDriver
{
    protected $aParams = array();
    
    abstract public function add($sKey, $mValue, $nExpire = false);
    abstract public function set($sKey, $mValue, $nExpire = false);
    abstract public function get($sKey);
    abstract public function delete($sKey);
    abstract public function flush();
    abstract public function increment($sKey, $nValue = 1);
    abstract public function decrement($sKey, $nValue = 1);

    public function __construct($aParams = array())
    {
        $this->aParams = $aParams;
    }

    protected function parseKeyPrefix($sKey)
    {
        if (!empty ($this->aParams['Prefix'])) {
            return $this->aParams['Prefix'] . $sKey;
        }

        return $sKey;
    }
}