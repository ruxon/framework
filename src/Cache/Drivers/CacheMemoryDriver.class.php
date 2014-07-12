<?php

class CacheMemoryDriver extends CacheDriver
{
    protected $aData = array();
    
    protected $aDataExpire = array();

    public function __construct($aParams = array())
    {
        parent::__construct($aParams);

        $this->flush();
    }

    public function add($sKey, $mValue, $nExpire = false)
    {
        if (!$this->exists($sKey)) {
            return $this->set($sKey, $mValue, $nExpire);
        }

        return false;
    }

    public function set($sKey, $mValue, $nExpire = false)
    {
        $this->aData[$this->parseKeyPrefix($sKey)] = $mValue;

        if ($nExpire) {
            $this->aDataExpire[$this->parseKeyPrefix($sKey)] = time();
        } else {
            $this->aDataExpire[$this->parseKeyPrefix($sKey)] = 0;
        }

        return true;
    }

    public function get($sKey)
    {
        if ($this->exists($sKey)) {
            if (!$this->expire($sKey)) {
                return $this->aData[$this->parseKeyPrefix($sKey)];
            } else {
                $this->delete($sKey);

                return false;
            }
        }

        return false;
    }

    public function exists($sKey)
    {
        if (isset($this->aData[$this->parseKeyPrefix($sKey)])) {
            return true;
        }

        return false;
    }

    public function expire($sKey)
    {
        if (isset($this->aData[$this->parseKeyPrefix($sKey)])) {
            if ($this->aDataExpire[$this->parseKeyPrefix($sKey)] == 0) {
                return false;
            } else {
                if ($this->aDataExpire[$this->parseKeyPrefix($sKey)] > time()) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    public function delete($sKey)
    {
        if ($this->exists($sKey)) {
            unset($this->aData[$this->parseKeyPrefix($sKey)]);
            unset($this->aDataExpire[$this->parseKeyPrefix($sKey)]);

            return true;
        }

        return false;
    }

    public function flush()
    {
        $this->aData = array();
        $this->aDataExpire = array();

        return true;
    }

    public function increment($sKey, $nValue = 1)
    {
        if ($this->exists($sKey)) {
            $this->aData[$this->parseKeyPrefix($sKey)] = $this->aData[$this->parseKeyPrefix($sKey)] + $nValue;

            return true;
        }

        return false;
    }

    public function decrement($sKey, $nValue = 1)
    {
        if ($this->exists($sKey)) {
            $this->aData[$this->parseKeyPrefix($sKey)] = $this->aData[$this->parseKeyPrefix($sKey)] - $nValue;

            return true;
        }

        return false;
    }
}