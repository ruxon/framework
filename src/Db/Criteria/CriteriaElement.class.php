<?php

/**
 * CriteriaElement: Элемент условия в запросах
 *
 * @package Db
 * @subpackage Criteria
 * @version 5.1.0
 */
class CriteriaElement extends Criteria
{
	/**
	 * Условие
	 *
	 * @var string
	 */
	private $sCriteria;

	/**
	 * Конструктор
	 *
	 * @param string $sCriteria Свойство
	 * @param string $sType Тип условия
	 * @param mixed $mValue Значение
	 * @param boolean $bNotQuoted Не экранировать значение?
	 */
	public function __construct($sCriteria, $sType, $mValue, $bNotQuoted = false)
	{
		switch ($sType){
			case self::BETWEEN:
			case self::NOT_BETWEEN:
				$this->sCriteria = "".$sCriteria." ".$sType." ".$this->quoted($mValue[0])." ".self::TYPE_AND." ".$this->quoted($mValue[1]);
			break;

			case self::IN:
			case self::NOT_IN:
				$this->sCriteria = "".$sCriteria." ".$sType." (".$this->parseValues($mValue, $bNotQuoted).")";
			break;

			default:
				if (!$bNotQuoted) {
					$this->sCriteria = "".$sCriteria." ".$sType." ".$this->quoted($mValue);
				} else {
					$this->sCriteria = "".$sCriteria." ".$sType." ".$mValue;
				}
		}

	}

	/**
	 * Возвращает условие
	 * 
	 * @return string
	 */
	public function renderWhere()
	{
		return $this->sCriteria;
	}

	/**
	 * Обрабатывает массив значений
	 *
	 * @param array $aValues Значения
	 * @param boolean $bNotQuoted Не экранировать значения?
	 * @return string
	 */
	private function parseValues($aValues, $bNotQuoted = false)
	{
		$aResult = array();
		foreach ($aValues as $itm) {
			if (!$bNotQuoted) {
				$aResult[] = $this->quoted($itm);
			} else {
				$aResult[] = $itm;
			}
		}

		return implode(", ", $aResult);
	}

	/**
	 * Экранирует значение
	 *
	 * @param string $sValue Значение
	 * @return string Экранированное значение
	 */
	private function quoted($sValue)
	{
		return "'".$sValue."'";
	}
}

?>