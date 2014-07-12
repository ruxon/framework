<?php

abstract class DbBuilder
{
    protected $aParams;

    public function __construct()
    {

    }

    public function init($aParams = array())
    {
        $this->aParams = $aParams;

        return true;
    }

    abstract public function execute($mQuery);

    /**
	 * Добавляет префикс к таблицам
	 *
	 * @param string $sSql
	 * @return string
	 */
	protected function parsePrefix($sSql)
	{
		return str_replace("#__", $this->aParams['Prefix'], $sSql);
	}

	/**
	 * Добавляет префикс
	 *
	 * @param string $sSql
	 * @return string
	 */
	protected function addPrefix($sSql)
	{
		return $this->aParams['Prefix'].$sSql;
	}

    protected function toValidVar($sVar)
    {
        if (get_magic_quotes_gpc()) {
			$sVar = stripslashes($sVar);
		}

        if (!is_object($sVar))
        {
            $sVar = "'".str_replace("'", "\'", $sVar)."'";
        }

		return $sVar;
    }
}