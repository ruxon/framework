<?php

/**
 * Request
 *
 * @package Request
 * @version 6.0
 */
class Request extends ToolkitBase
{
    const C_REQUEST = 'REQUEST';

    const C_GET = 'GET';
    
    const C_POST = 'POST';

    const C_COOKIES = 'COOKIE';

    const C_SERVER = 'SERVER';

    protected $bCsrfPostValidation = false;

    protected $bCsrfAjaxValidation = false;

    protected $sCsrfTokenName = 'RUXON_CSRF_TOKEN';

    protected $cleanUrl;

    public function exists($sName, $sPlace = self::C_REQUEST)
    {
        $aInput = array();
		switch ($sPlace) {
			case 'GET':
				$aInput = $_GET;
			break;

			case 'POST':
				$aInput = $_POST;
			break;

			case 'REQUEST':
				$aInput = $_REQUEST;
			break;

            case 'COOKIE':
				$aInput = $_COOKIE;
			break;

            case 'SERVER':
				$aInput = $_SERVER;
			break;
		}

        if (isset($aInput[$sName])) {
            return true;
        }

        return false;
    }

    public function get($sName, $sPlace = self::C_REQUEST)
    {
        if ($this->exists($sName, $sPlace)) {
            $aInput = array();
            switch ($sPlace) {
                case 'GET':
                    $aInput = $_GET;
                break;

                case 'POST':
                    $aInput = $_POST;
                break;

                case 'REQUEST':
                    $aInput = $_REQUEST;
                break;

                case 'COOKIE':
                    $aInput = $_COOKIE;
                break;

                case 'SERVER':
                    $aInput = $_SERVER;
                break;
            }
		
			return $aInput[$sName];
		}

		return false;
    }

    public function getInt($sName, $sPlace = self::C_REQUEST)
    {
        return intval($this->get($sName, $sPlace));
    }

    public function export($sPlace = self::C_REQUEST)
    {
        $aInput = array();
		switch ($sPlace) {
			case 'GET':
				$aInput = $_GET;
			break;

			case 'POST':
				$aInput = $_POST;
			break;

			case 'REQUEST':
				$aInput = $_REQUEST;
			break;

            case 'COOKIE':
				$aInput = $_COOKIE;
			break;

            case 'SERVER':
				$aInput = $_SERVER;
			break;
		}

		return $aInput;
    }

    public function exportJson($sPlace = self::C_REQUEST)
    {
        return JsHelper::jsonEncode($this->export($sPlace));
    }

    public function exportUrl($sPlace = self::C_REQUEST)
    {
        return UrlHelper::urlencode($this->export($sPlace));
    }

    public function isAjaxRequest()
    {
        if ($this->get('HTTP_X_REQUESTED_WITH', self::C_SERVER) == 'XMLHttpRequest') {
            return true;
        }

        return false;
    }

    public function isPostRequest()
    {
        if ($this->get('REQUEST_METHOD', self::C_SERVER) == 'POST') {
            return true;
        }

        return false;
    }

    public function isSecure()
    {
        if ($this->get('HTTPS', self::C_SERVER) == 'on') {
            return true;
        }
        
        return false;
    }

    public function getCookies()
    {
        return new CookiesCollection($_COOKIE);
    }

    public function cookies()
    {
        return $this->getCookies();
    }

    public function getQueryString()
    {
        return $this->get('QUERY_STRING', self::C_SERVER);
    }

    public function getUrl()
    {
        return $this->get('REQUEST_URI', self::C_SERVER);
    }
    
    public function getCleanUrl()
    {
        if (!$this->cleanUrl) {
            $this->cleanUrl = $this->getUrl();
            if (strpos($this->cleanUrl, "?") !== false)
            {
                $this->cleanUrl = substr($this->cleanUrl, 0, strpos($this->cleanUrl, "?"));
            }
        }
        
        return $this->cleanUrl;
    }

    public function setCleanUrl($value)
    {
        $this->cleanUrl = $value;

        return true;
    }

    public function getServerName()
    {
        return $this->get('SERVER_NAME', self::C_SERVER);
    }

    public function getServerPort()
    {
        return $this->get('SERVER_PORT', self::C_SERVER);
    }

    public function getReferrer()
    {
        return $this->get('HTTP_REFERER', self::C_SERVER);
    }

    public function getUserAgent()
    {
        return $this->get('HTTP_USER_AGENT', self::C_SERVER);
    }

    public function getUserHost()
    {
        return $this->get('REMOTE_HOST', self::C_SERVER);
    }

    public function getUserAddress()
    {
        return $this->get('REMOTE_ADDR', self::C_SERVER);
    }
}