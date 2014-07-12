<?php

class Cache
{
    public static function factory($sEngine, $aParams = array())
	{
		$sEngineClass = 'Cache'.ucfirst(strtolower($sEngine)).'Driver';

		return new $sEngineClass($aParams);
	}
}