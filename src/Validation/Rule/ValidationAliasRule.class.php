<?php

class ValidationAliasRule extends ValidationRule
{
	protected $sAlias;

	protected $sTitle;

	protected $aCriteria;

	protected $sModule;

	protected $sModel;

	protected $sErrorText = 'Field "{Field}" has an invalid format.';

	public function __construct($aParams)
	{
		$this->sAlias     = $aParams[0];
		$this->sTitle     = $aParams[1];
		$this->sModule    = $aParams[2];
		$this->sModel     = $aParams[3];
        
        if (isset($aParams[4])) {
            $this->aCriteria  = $aParams[4];
        }
		if (isset($aParams[5])) {
			$this->sErrorText = $aParams[5];
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

	public function getModule()
	{
		return $this->sModule;
	}

	public function getModel()
	{
		return $this->sModel;
	}

	public function getCriteria()
	{
		return $this->aCriteria;
	}

	public function getErrorText()
	{
		return $this->sErrorText;
	}

	public function check($sValue, $nElementId)
	{
    	$oMatchValidator = new ValidationMatchRule(array($this->getAlias(), $this->getTitle(), "/^([A-Za-z]{1})([A-Za-z0-9_-]+)$/i"));
		$oUniqValidator  = new ValidationUniqRule(array($this->getAlias(), $this->getTitle(), $this->getModule(), $this->getModel(), $this->getCriteria()));

        
		if (strlen($sValue) >= 3 && $oMatchValidator->check($sValue) === true && $oUniqValidator->check($sValue, $nElementId) === true) {
			return true;
		}

        return Core::app()->t('Ruxon', $this->getErrorText(), [
            'Field' => $this->getTitle()
        ], null, 'ruxon/framework/messages');
	}
}

?>
