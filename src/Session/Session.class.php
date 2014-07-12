<?php

class Session
{
	/**
	 * Фабрика
	 *
	 * @param string $sEngine Драйвер
	 * @param array $aParams Параметры
	 * @return object
	 */
	public static function factory($sEngine, $aParams = array())
	{
		$sEngine = ucfirst(strtolower($sEngine));
		Core::require_file('ruxon/packages/Session/src/Drivers/Session'.$sEngine.'Driver.class.php');
		$sEngineClass = 'Session'.$sEngine.'Driver';

		return new $sEngineClass($aParams);
	}
}

?>
