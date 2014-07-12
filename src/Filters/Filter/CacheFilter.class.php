<?php

class CacheFilter extends Filter
{
	public function run(FilterChain $oFilterChain)
	{
        $aDb = Core::app()->config()->getCache();
        
        foreach ($aDb as $k => $val)
        {
            Manager::getInstance()->getCache()->add(Cache::factory($val['Driver'], $val['Params']), $k);
        }

        $oFilterChain->next();
	}
}