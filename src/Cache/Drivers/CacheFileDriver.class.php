<?php

class CacheFileDriver extends CacheDriver
{
    protected $sPath;

    public function __construct($aParams = array())
    {
        $this->sPath = RX_PATH.'/runtime/cache_data/';

        if (!is_dir($this->sPath))
            mkdir ($this->sPath, 0777, true);

        parent::__construct($aParams);
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
        if (!$nExpire) {
            $nExpire = 31536000;
        }
        $nExpire += time();

        $filename = $this->sPath.$this->parseKeyPrefix($sKey).'.cache';

        file_put_contents($filename, $mValue);
        touch($filename, $nExpire);

        return true;
    }

    public function get($sKey)
    {
        if ($this->exists($sKey)) {
            if (!$this->expire($sKey)) {

                $filename = $this->sPath.$this->parseKeyPrefix($sKey).'.cache';

                return file_get_contents($filename);
            } else {
                $this->delete($sKey);

                return false;
            }
        }

        return false;
    }

    public function exists($sKey)
    {
        $filename = $this->sPath.$this->parseKeyPrefix($sKey).'.cache';
        if (file_exists($filename)) {
            return true;
        }

        return false;
    }

    public function expire($sKey)
    {
        $filename = $this->sPath.$this->parseKeyPrefix($sKey).'.cache';
        if (filemtime($filename) < time())
        {
            return true;
        }

        return false;
    }

    public function delete($sKey)
    {
        if ($this->exists($sKey)) {
            $filename = $this->sPath.$this->parseKeyPrefix($sKey).'.cache';
            unlink($filename);

            return true;
        }

        return false;
    }

    public function flush()
    {
        rrmdir($this->sPath, false);

        return true;
    }

    public function increment($sKey, $nValue = 1)
    {
        if ($this->exists($sKey)) {

            $nValue = $this->get($sKey) + 1;

            return $this->set($sKey, $nValue);
        }

        return false;
    }

    public function decrement($sKey, $nValue = 1)
    {
        if ($this->exists($sKey)) {

            $nValue = $this->get($sKey) - 1;

            return $this->set($sKey, $nValue);
        }

        return false;
    }
}