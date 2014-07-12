<?php

class AjaxOnlyFilter extends Filter
{
	public function run(FilterChain $oFilterChain)
	{
        if(Core::app()->request()->isAjaxRequest()) {
			$oFilterChain->next();
        } else {
			throw new RxException('Your request is not valid.', 400);
        }
	}
}