<?php

class SystemToolkit extends ToolkitBase
{
	public function getRequest()
	{
		return Core::app()->request();
	}

	public function getResponse()
	{
		return Core::app()->response();
	}

	public function getSession()
	{
		return Core::app()->session();
	}   

	public function getTheme()
	{
		return Core::app()->theme();
	}	   
}