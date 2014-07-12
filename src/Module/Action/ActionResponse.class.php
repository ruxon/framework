<?php

class ActionResponse
{
    protected $mResult;

	protected $sHtml;

	protected $oErrors;

	protected $bIsSuccess;

	protected $bIsFail;

    protected $bLayout = true;

	public function __construct($bLayout = true)
	{
        $this->bLayout = $bLayout;
	}

	public function getIsSuccess()
	{
		return $this->bIsSuccess;
	}

	public function getIsFail()
	{
		return $this->bIsFail;
	}

	public function setIsSuccess($bIsSuccess = true)
	{
		$this->bIsSuccess = $bIsSuccess;

		return true;
	}

	public function setIsFail($bIsFail = true)
	{
		$this->bIsFail = $bIsFail;

		return true;
	}

	public function setResult($mResult)
	{
		$this->mResult = $mResult;

		return true;
	}

    public function getLayout()
    {
        return $this->bLayout;
    }

	public function getResult($sAlias = false)
	{
		if ($this->getIsSuccess() && !$this->getIsFail() && $this->mResult) {
            if (!$sAlias) {
               return $this->mResult;
            } else {
                if (is_array($sAlias)) {
                    if (count($sAlias)) {
                        $mValue = $this->mResult;
                        foreach ($sAlias as $val) {
                            if (isset($mValue[$val])) {
                                $mValue = $mValue[$val];
                            } else {
                                $mValue = false;
                                break;
                            }
                        }

                        return $mValue;
                    }
                } else {
                    if (isset($this->mResult[$sAlias])) {
                        return $this->mResult[$sAlias];
                    }
                }
            }
		}

		return false;
	}

	public function setHtml($sHtml)
	{
		$this->sHtml = $sHtml;

		return true;
	}

	public function getHtml()
	{
        return $this->sHtml;
	}

	public function setErrors(ActionResponseErrorsCollection $oErrors)
	{
		$this->oErrors = $oErrors;

		return true;
	}

	public function getErrors()
	{
		if ($this->getIsFail() && $this->oErrors) {
			return $this->oErrors;
		}

		return false;
	}

	public function display()
	{
		echo $this->getHtml();

		return true;
	}
    
    public function __toString() 
    {
        return $this->getHtml();
    }
}