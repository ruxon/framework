<?php

class ValidationUniqRule extends ValidationRule
{
	protected $sAlias;
    
    protected $sTitle;

	protected $aCriteria;

	protected $sModule;

	protected $sModel;

	protected $sErrorText = 'Such "{Field}" already exists in our database.';

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
    
    public function getTitle()
	{
		return $this->sTitle;
	}

	public function getAlias()
	{
		return $this->sAlias;
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
        $aCriteria = array();
        $aCriteria[$this->sAlias] = $sValue;
		$aCriteria['Id'] = array(
			'Type' => '<>',
			'Value' => $nElementId
		);
        if (count($this->getCriteria()))
        {
            foreach ($this->getCriteria() as $k => $itm) {
                $aCriteria[$k] = $itm;
            }
        }
         
		$nRes = $this->mapper()->count(array('Criteria' => $aCriteria));
		if ($nRes > 0) {
            return Core::app()->t('Ruxon', $this->getErrorText(), [
                'Field' => $this->getTitle()
            ], null, 'ruxon/framework/messages');
		}

		return true;
	}

	protected function mapper()
    {
        return Manager::getInstance()->getMapper($this->sModel);
    }
}

?>
