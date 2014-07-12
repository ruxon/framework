<?php

class ComposeToolsFilter extends Filter
{
	public function run(FilterChain $oFilterChain)
	{
        if (count($this->getParams()))
        {
            foreach ($this->getParams() as $key => $val) 
            {
                $class_name = $val['class'];
                unset($val['class']);

                Toolkit::getInstance()->addToolkit($key, new $class_name($val));
            }
        }

		$oFilterChain->next();
	}
}