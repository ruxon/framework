<?php

class OrmBehavior extends Behavior
{
    protected function getDbConnection()
	{
		return Manager::getInstance()->getDb($this->getDbConnectionAlias());
	}
    
    protected function getDbConnectionAlias()
    {
        return 'default';
    }
}