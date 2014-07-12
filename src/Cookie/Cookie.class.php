<?php

class Cookie
{
    protected $sName;

    protected $sValue = '';

    protected $sDomain = '';

    protected $nExpire = 0;

    protected $sPath = '/';

    protected $bSecure = false;

    protected $bHttpOnly = false;

    public function __construct($sName, $sValue = '', $nExpire = 0, $sPath = '/', $sDomain = '', $bSecure = false, $bHttpOnly = false)
    {
        $this->setName($sName);
        $this->setValue($sValue);
        $this->setExpire($nExpire);
        $this->setPath($sPath);
        $this->setDomain($sDomain);
        $this->setSecure($bSecure);
        $this->setHttpOnly($bHttpOnly);
    }

    public function getName()
    {
        return $this->sName;
    }

    public function setName($sName)
    {
        $this->sName = $sName;

        return true;
    }

    public function getValue()
    {
        return $this->sValue;
    }

    public function setValue($sValue = '')
    {
        $this->sValue = $sValue;

        return true;
    }

    public function getExpire()
    {
        return $this->nExpire;
    }

    public function setExpire($nExpire = 0)
    {
        $this->nExpire = $nExpire;

        return true;
    }

    public function getPath()
    {
        return $this->sPath;
    }

    public function setPath($sPath = '/')
    {
        $this->sPath = $sPath;

        return true;
    }

    public function getDomain()
    {
        return $this->sDomain;
    }

    public function setDomain($sDomain = '')
    {
        $this->sDomain = $sDomain;

        return true;
    }

    public function getSecure()
    {
        return $this->bSecure;
    }

    public function setSecure($bSecure = false)
    {
        $this->bSecure = $bSecure;

        return true;
    }

    public function getHttpOnly()
    {
        return $this->bHttpOnly;
    }

    public function setHttpOnly($bHttpOnly = false)
    {
        $this->bHttpOnly = $bHttpOnly;

        return true;
    }

    public function save()
    {
        if(version_compare(PHP_VERSION,'5.2.0','>=')) {
			setcookie($this->getName(), $this->getValue(), $this->getExpire(), $this->getPath(), $this->getDomain(), $this->getSecure(), $this->getHttpOnly());
        } else {
			setcookie($this->getName(), $this->getValue(), $this->getExpire(), $this->getPath(), $this->getDomain(), $this->getSecure());
        }
    }

    public function remove()
    {
        if(version_compare(PHP_VERSION,'5.2.0','>=')) {
			setcookie($this->getName(), null, 0, $this->getPath(), $this->getDomain(), $this->getSecure(), $this->getHttpOnly());
        } else {
			setcookie($this->getName(), null, 0, $this->getPath(), $this->getDomain(), $this->getSecure());
        }
    }
}