<?php

class AdminAccessFilter extends Filter
{
	public function run(FilterChain $oFilterChain)
	{
        if (Toolkit::getInstance()->auth->checkAdminAccess())
        {
            $oFilterChain->next();
            
        } else {
            header("Location: ".Toolkit::getInstance()->auth->loginUrl);
            Core::app()->hardEnd();
        }
	}
}