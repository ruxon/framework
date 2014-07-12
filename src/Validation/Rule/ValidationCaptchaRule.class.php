<?php

class ValidationCaptchaRule extends ValidationRule
{
	protected $sAlias;

	protected $sErrorText = 'Invalid characters entered the picture.';

	public function __construct($aParams)
	{
		$this->sAlias   = $aParams[0];
		if (isset($aParams[1])) {
			$this->sErrorText = $aParams[1];
		}
	}

	public function getAlias()
	{
		return $this->sAlias;
	}

	public function getErrorText()
	{
		return $this->sErrorText;
	}

	public function check($sValue)
	{
		if (isset($_SESSION['rx_captcha']) && $_SESSION['rx_captcha'] == $sValue) {
			unset($_SESSION['rx_captcha']);

			return true;
		}

		unset($_SESSION['rx_captcha']);

        return Core::app()->t('Ruxon', $this->getErrorText(), [], null, 'ruxon/framework/messages');
	}
}

?>
