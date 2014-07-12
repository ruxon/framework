<?php

class ValidationCompareRule extends ValidationRule
{
    protected $sTitle;
    
	protected $sAlias;

	protected $sAlias2;

	protected $sErrorText = '';

	public function __construct($aParams)
	{
		$this->sAlias     = $aParams[0];
		$this->sTitle     = $aParams[1];
		$this->sAlias2   = $aParams[2];
		$this->sErrorText = $aParams[3];
	}

	public function getAlias()
	{
		return $this->sAlias;
	}

	public function getAlias2()
	{
		return $this->sAlias2;
	}

	public function getErrorText()
	{
		return $this->sErrorText;
	}

	public function check($sValue, $sValue2)
	{
		if ($sValue == '' && $sValue2 == '') {
			return true;
		} else if ($sValue != $sValue2) {
			return $this->getErrorText();
		}

		return true;
	}
}

?>
