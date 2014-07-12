<?php

class Response extends ToolkitBase
{
	protected $aHeaders = array();	

	protected $nStatus = 200;

	protected $sResponse = '';

	public function setResponseText($sResponse)
	{
		$this->sResponse = $sResponse;
		return true;
	}

	public function getResponseText()
	{
		return $this->sResponse;
	}

	public function setStatus($nStatus)
	{
		$this->nStatus = $nStatus;
		return true;
	}

	public function getStatus()
	{
		return $this->nStatus;
	}

	public function addHeader($sName, $sValue)
	{
		$this->aHeaders[$sName] = $sValue;

		return true;
	}	

	public function getHeaders()
	{
		switch($this->getStatus()) {
			case 404:
				header("HTTP/1.0 404 Not Found");
			break;

			case 403:
				header("HTTP/1.0 403 Forbidden");
			break;

			case 301:
				header("HTTP/1.1 301 Moved Permanently");
			break;

			case 200:
			default:
				header("HTTP/1.1 200 OK");
		}
		if (count($this->aHeaders) > 0) {
			foreach ($this->aHeaders as $name => $itm) {
				header($name.": ".$itm);
			}

			return true;
		}

		return false;
	}	

	public function send()
	{
		$this->sendHeaders();
		$this->sendText();
	}
    
    public function refresh()
    {
        $this->addHeader('Location', Toolkit::getInstance()->request->getUrl());
        $this->sendHeaders();
        exit();
    }


	protected function sendHeaders()
	{
		$this->getHeaders();
	}

	protected function sendText()
	{
		echo $this->getResponseText();
	}
}

?>