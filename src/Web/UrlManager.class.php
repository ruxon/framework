<?php

class UrlManager
{
	public static function createUrl($route, $params = array())
	{
        return Toolkit::i()->urlManager->createUrl($route, $params);
	}

    public static function getParams()
    {
        return Toolkit::i()->urlManager->getParams();
    }
}
