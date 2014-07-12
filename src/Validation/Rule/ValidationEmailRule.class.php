<?php

class ValidationEmailRule extends ValidationRule
{
	protected $sAlias;

	protected $sTitle;

	protected $sErrorText = '"{Field}" incorrectly.';

	public function __construct($aParams)
	{
		$this->sAlias   = $aParams[0];
		$this->sTitle   = $aParams[1];
		if (isset($aParams[2]) && is_string($aParams[2])) {
			$this->sErrorText = $aParams[2];
		}
	}

	public function getAlias()
	{
		return $this->sAlias;
	}

	public function getTitle()
	{
		return $this->sTitle;
	}

	public function getErrorText()
	{
		return $this->sErrorText;
	}

	public function check($sValue)
	{
		if (!preg_match("%^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$%", $sValue)) {

            return Core::app()->t('Ruxon', $this->getErrorText(), [
                'Field' => $this->getTitle()
            ], null, 'ruxon/framework/messages');
		}

		return true;
	}
}

?>
