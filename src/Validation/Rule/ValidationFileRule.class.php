<?php

class ValidationFileRule extends ValidationRule
{
	protected $aExtensions = array('doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'gif', 'png');

	protected $nMaxFileSize = 1024;

	protected $sAlias;

	protected $sTitle;

	protected $sErrorText = '';

	public function __construct($aParams)
	{
		$this->sAlias   = $aParams[0];
		$this->sTitle   = $aParams[1];
		if (isset($aParams[2])) {
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
		if (isset($_FILES[$this->getAlias()]) && $_FILES[$this->getAlias()]['name'] != '') {
			$sName = $_FILES[$this->getAlias()]['name'];
			$sExtension = strtolower(substr($sName, strrpos($sName, ".") + 1));
			$nSize = $_FILES[$sFieldName]['size'];
			if (array_search($sExtension, $this->aExtensions) !== false) {
				if ($nSize <= $this->nMaxFileSize) {
					return true;
				}
			}

			return str_replace("{Field}", $this->getTitle(), $this->getErrorText());
		}

		return true;
	}
}

?>
