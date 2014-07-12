<?php

class SessionFileDriver extends SessionDriver
{
	/**
	 * Конструктор
	 *
	 */
	public function __construct($aParams = array())
	{
        parent::__construct($aParams);
		$this->start();
	}

	/**
	 * Открывает сессию
	 *
	 * @return boolean
	 */
	public function start()
	{
		session_start();

		return true;
	}

	/**
	 * Закрывает сессию
	 *
	 * @return boolean
	 */
	public function end()
	{
		return true;
	}

	/**
	 * Возвращает ID сессии
	 *
	 * @return string
	 */
	public function getId()
	{
		return session_id();
	}

	/**
	 * Устанавливает значение
	 * сессии
	 *
	 * @param string $sName
	 * @param mixed $sValue
	 * @return boolean
	 */
	public function set($sName, $sValue)
	{
		$_SESSION[$sName] = $sValue;

		return true;
	}

	/**
	 * Возвращает значение сессии
	 *
	 * @param string $sName
	 * @return mixed|false
	 */
	public function get($sName)
	{
		if ($this->isExists($sName)) {
			return $_SESSION[$sName];
		} else {
			return false;
		}
	}

	/**
	 * Проверяет на существование
	 * значение в сессии
	 *
	 * @param string $sName
	 * @return boolean
	 */
	public function isExists($sName)
	{
		if (isset($_SESSION[$sName])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Удаляет значение сессии
	 *
	 * @param string $sName
	 * @return boolean
	 */
	public function delete($sName)
	{
		if ($this->isExists($sName)) {
			unset($_SESSION[$sName]);
		}

		return true;
	}

	/**
	 * Полностью очищает
	 * сессию
	 *
	 * @return boolean
	 */
	public function clear()
	{
		$_SESSION = array();

		return true;
	}
}

?>