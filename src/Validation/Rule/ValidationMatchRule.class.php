<?php

class ValidationMatchRule extends ValidationRule
{
	protected $sAlias;
    
    protected $sTitle;

	protected $sPattern;

	protected $sErrorText = '';

	public function __construct($aParams)
	{
		$this->sAlias   = $aParams[0];
		$this->sTitle = $aParams[1];
        $this->sPattern = $aParams[2];
        
		if (isset($aParams[3])) {
			$this->sErrorText = $aParams[3];
		}
	}

	public function getAlias()
	{
		return $this->sAlias;
	}

	public function getPattern()
	{
		return $this->sPattern;
	}

	public function getErrorText()
	{
		return $this->sErrorText;
	}

	public function check($sValue)
	{
		if (preg_match($this->getPattern(), $sValue)) {
			return true;
		}

		if ($this->getErrorText()) {
			return $this->getErrorText();
		} else {
			return false;
		}
	}
}

?>
