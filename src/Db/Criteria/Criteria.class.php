<?php

/**
 * Criteria: Условия в запросах
 *
 * @package Db
 * @subpackage Criteria
 * @version 5.1.0
 */
class Criteria
{

	/**
     * Константа, определяющая тип сравнения "=" (равно)
     */
    const EQUAL = "=";

    /**
     * Константа, определяющая тип сравнения "<>" (не равно)
     */
    const NOT_EQUAL = '<>';

    /**
     * Константа, определяющая тип сравнения ">" (больше)
     */
    const GREATER = '>';

    /**
     * Константа, определяющая тип сравнения "<" (меньше)
     */
    const LESS = '<';

    /**
     * Константа, определяющая тип сравнения ">=" (больше либо равно)
     */
    const GREATER_EQUAL = '>=';

    /**
     * Константа, определяющая тип сравнения "<=" (меньше либо равно)
     */
    const LESS_EQUAL = '<=';

    /**
     * Константа, определяющая оператор "IN"
     */
    const IN = 'IN';

    /**
     * Константа, определяющая оператор "IN"
     */
    const NOT_IN = 'NOT IN';

    /**
     * Константа, определяющая оператор "LIKE"
     */
    const LIKE = 'LIKE';

    /**
     * Константа, определяющая оператор "NOT LIKE"
     */
    const NOT_LIKE = 'NOT LIKE';

    /**
     * Константа, определяющая оператор "BETWEEN"
     */
    const BETWEEN = 'BETWEEN';

    /**
     * Константа, определяющая оператор "NOT BETWEEN"
     */
    const NOT_BETWEEN = 'NOT BETWEEN';

	/**
	 * Константа, определяющая тип группы "AND" (И)
	 */
	const TYPE_AND = "AND";

	/**
	 * Константа, определяющая тип группы "OR" (ИЛИ)
	 */
	const TYPE_OR = "OR";

    /**
     * Условия
     *
     * @var array
     */
	protected $aCriteria;

	/**
	 * Конструктор
	 */
	public function __construct()
	{
   		$this->aCriteria = array();
	}

	/**
	 * Добавляет элемент условия
	 *
	 * @param object $oCriteria
	 * @return true
	 */
	public function addElement($oCriteria)
	{
		$this->aCriteria[] = $oCriteria;

		return true;
	}
    
    public function addCondition()
    {
        
    }
    
    public function mergeWith($criteria)
    {
        $this->addElement($criteria);
    }
}

?>