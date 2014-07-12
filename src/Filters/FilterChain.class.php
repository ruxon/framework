<?php

class FilterChain
{
	protected $aFilters = array();

	protected $nIndex = -1;
	
	public function registerFilter(FilterInterface $oFilter)
	{
		$this->aFilters[] = $oFilter;
		
		return true;
	}
	
	public function next()
	{
		$this->nIndex++;

		if (isset($this->aFilters[$this->nIndex])) {
			$this->aFilters[$this->nIndex]->run($this);
		}		
	}

	public function process()
	{
		$this->next();
	}
}