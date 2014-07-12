<?php

/**
 * CrtiteriaGroup: Группа условий
 *
 * @package Db
 * @subpackage Criteria
 * @version 5.1.0
 */
class CriteriaGroup extends Criteria
{
	/**
	 * Тип группы условий
	 *
	 * @var string
	 */
	private $sType;

	/**
	 * Конструктор
	 *
	 * @param string $sType Тип группы
	 */
	public function __construct($sType = self::TYPE_AND)
	{
		$this->sType = $sType;
	}

	/**
	 * Возвращает сформированную
	 * строку условия
	 *
	 * @return string
	 */
	public function renderWhere()
	{
		$sResult = '(';
		$nCount = count($this->aCriteria);
		foreach($this->aCriteria as $n => $itm){
			$sResult .= $itm->renderWhere();
			if ($n != ($nCount - 1)) {
				$sResult .= ' '.$this->sType.' ';
			}
		}
		$sResult .= ')';
		return $sResult;
	}
}

?>