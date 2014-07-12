<?php

class ValidationRequiredRule extends ValidationRule
{
	protected $sAlias;

	protected $sTitle;

	protected $sErrorText = '"{Field}" cannot be blank.';

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
		if (
            (is_numeric($sValue) && $sValue > 0) || 
            (is_array($sValue) && count($sValue) > 0) || 
            (is_string($sValue) && strlen($sValue) > 0)) {
			return true;
		}

        return Core::app()->t('Ruxon', $this->sErrorText, [
            'Field' => $this->getTitle()
        ], null, 'ruxon/framework/messages');
	}
}

?>
