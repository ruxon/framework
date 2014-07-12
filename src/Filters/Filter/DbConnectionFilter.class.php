<?php

class DbConnectionFilter extends Filter
{
	public function run(FilterChain $oFilterChain)
	{
        $aDb = Core::app()->config()->getDb();
        
        foreach ($aDb as $k => $val)
        {
            $oDb = new Db($val['ConnectionString'], $val['Username'], $val['Password'], $val['Params']);
            $oDb->open();
            Manager::getInstance()->getDb()->add($oDb, $k);
        }

        $oFilterChain->next();
	}
}