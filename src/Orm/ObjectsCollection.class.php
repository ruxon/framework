<?php

/**
 * ObjectsCollection
 *
 * @package Object
 * @version 6.0
 */
class ObjectsCollection implements SimpleCollectionInterface
{
	protected $aData = array();

    protected $oQuery;

	protected $nCountAll;

    function __construct(DbFetcher $oQuery)
	{
		$this->oQuery    = $oQuery;
		$this->nCountAll = false;
	}

	public function export()
	{
        $aResult = array();

        if (count($this->aData)) {
            foreach ($this->aData as $itm) {
                $aResult[] = $itm->export();
            }
        }

		return $aResult;
	}

	public function sort($sField, $sDirection = 'ASC')
	{
		$aRes = array();
		$aResult = array();
		foreach ($this as $itm) {
			$aRes[$itm->get($sField)] = $itm;
		}

		ksort($aRes, SORT_STRING);

		foreach ($aRes as $itm) {
			$aResult[] = $itm;
		}

		if ($sDirection != 'ASC') {
			$aResult = array_reverse($aResult);
		}

		$this->dataset = array();
		foreach ($aResult as $itm) {
			$this->add($itm);
		}

		return $this;
	}

	public function find($aParams = array())
	{
		return $this;
	}

	public function countAll()
	{
		if ($this->nCountAll === false) {
			$oQuery = $this->oQuery;
			$oQuery->clearSelectFields();
			$oQuery->addSelectField('CountElements', 'CountElements', 'COUNT', $oQuery->getMainTableAlias().'.id');
			$oQuery->setLimit(1);
			$oQuery->setOffset(0);

			$this->nCountAll = 0;
			$this->nCountAll = $oQuery->fetchCell();
		}

		return $this->nCountAll;
	}

	public function toSimpleArray()
	{
		$aResult = array();
		foreach ($this as $itm) {
			$aResult[] = $itm->getId();
		}

		return $aResult;
	}

	public function toArray()
	{
		$aResult = array();
		foreach ($this as $itm) {
			$aResult[] = $itm->toArray();
		}

		return $aResult;
	}

	public function add($mItem, $nPos = false)
	{
		if ($nPos !== false) {
			$this->aData[$nPos] = $mItem;
		} else {
			$this->aData[] = $mItem;
		}

		return true;
	}

	public function at($nPos)
	{
		if (isset($this->aData[$nPos])) {
			return $this->aData[$nPos];
		}

		return false;
	}

	public function current()
	{
		return current($this->aData);
	}

	public function next()
	{
		return next($this->aData);
	}

	public function key()
	{
		return key($this->aData);
	}

	public function valid()
	{
		return (bool) current($this->aData);
	}

	public function rewind()
	{
		reset($this->aData);
	}

	public function count()
	{
		return count($this->aData);
	}

	public function offsetExists($offset)
	{
		return isset($this->aData[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->at($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->add($value, $offset);
	}

	public function offsetUnset($offset){}

    /**
	 * Возвращает Тулкит
	 *
	 * @return object
	 */
	protected function getToolkit()
	{
		return Toolkit::getInstance();
	}

	/**
	 * Возвращает ACL
	 *
	 * @return object
	 */
	protected function getAcl()
	{
		return $this->getToolkit()->getAcl();
	}

	/**
	 * Возвращает DB
	 *
	 * @return object
	 */
	protected function getDb()
	{
		return $this->getToolkit()->getDb();
	}
}