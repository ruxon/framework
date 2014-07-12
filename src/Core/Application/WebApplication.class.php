<?php

class WebApplication extends Application
{
    protected $oTheme;

    protected $oAuth;

    protected $oRequest;

    protected $oResponse;

    protected $oSession;

    protected $oClient;
    
    protected $sAppName = 'WebApplication';

    private static $instance = false;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new WebApplication();
        }

        return self::$instance;
    }

    public function start()
    {
        parent::start();

        ob_start();
    }

    public function end()
    {
		$this->response()->send();
    }
    
    public function hardEnd()
    {
		exit();
    }

    public function getTheme()
    {
        return $this->oTheme;
    }

    public function theme()
    {
        return Toolkit::getInstance()->theme;
    }

    public function getAuth()
    {
        return Toolkit::getInstance()->auth;
    }

    public function auth()
    {
        return $this->getAuth();
    }

    public function getRequest()
    {
        return Toolkit::getInstance()->request;
    }

    public function request()
    {
        return $this->getRequest();
    }


    public function getResponse()
    {
        return Toolkit::getInstance()->response;
    }

    public function response()
    {
        return $this->getResponse();
    }

    public function getSession()
    {
        return Toolkit::getInstance()->session;
    }

    public function session()
    {
        return $this->getSession();
    }

    public function getClient()
    {
        return Toolkit::getInstance()->client;
    }

    public function client()
    {
        return $this->getClient();
    }

    public function process()
    {
        $sUrl = $this->getToolkit()->getRequest()->getUrl();
        $sUrl = substr($sUrl, 1);
        $nStatusCode = 200;

        if ($sUrl) {

            if (strrpos($sUrl, "/") === mb_strlen($sUrl) - 1) {
                $sUrl = substr($sUrl, 0, strrpos($sUrl, "/"));
            }

            $aRules = $this->getRules();

            $aParams = array();

            $bSuccess = false;

            foreach ($aRules as $rule) {
                $aRs = array();
                if (preg_match("#^".$rule['Pattern']."#", $sUrl, $aRs)) {
                    $bSuccess = true;

                    foreach ($rule['Params'] as $key => $value) {
                        if (strpos($value, "$") === 0) {
                            $aParams[$key] = $aRs[str_replace("$", "", $value)];
                        } else {
                            $aParams[$key] = $value;
                        }
                    }
                    break;
                }
            }

            if ($bSuccess)
            {
                $url = implode("/", $aParams);
                foreach ($aParams as $key => $value)
                {
                    $aParams[$key] = str_replace(" ", "", ucwords(str_replace("_", " ", mb_strtolower($value))));
                }

                $cleanUrl = substr($sUrl, 0, strlen($url));
                $cleanUrlParams = substr($sUrl, strlen($url) + 1);

                Toolkit::i()->urlManager->parseUrlParams($cleanUrlParams);
                Toolkit::i()->request->setCleanUrl($cleanUrl);

                if (isset($_GET[$sUrl]))
                {
                    unset($_GET[$sUrl]);
                }

                if (count($_GET))
                {
                    $aParams = array_merge($aParams, $_GET);
                }

                // Exec action
            } else {

                $aParams = array(
                    'Module' => 'App',
                    'Controller' => 'Index',
                    'Action' => '404'
                );

                $nStatusCode = 404;

                // 404 page not found
            }
        } else {
            // Main page

            $aParams = $this->getDefault();
        }

        $this->executeRequest($nStatusCode, $aParams);
    }

    protected function executeRequest($nStatusCode, $aParams)
    {
        $sModule = $aParams['Module'];
        $sController = 'ruxon\modules\\'.$sModule.'\controllers\\'.$aParams['Controller'].'Controller';
        $sAction = $aParams['Action'];

        Core::import('Modules.'.$sModule);

        if (!class_exists($sController))
        {
            $sController = $aParams['Controller'].'Controller';
        }

        Core::app()->theme()->getLayout()->setMetaTitle($aParams['Action'].' - '.$aParams['Controller'].' - '.$aParams['Module']);

        $oController = new $sController($sModule, $aParams['Controller']);

        unset($aParams['Action']);
        unset($aParams['Controller']);
        unset($aParams['Module']);

        $oResult = $oController->run($sAction, $aParams);

        Core::app()->response()->setStatus($nStatusCode);

        if (is_object($oResult))
        {
            if ($oResult->getLayout())
            {
                Core::app()->theme()->getLayout()->setContent($oResult->getHtml());

                $fullcontent = Core::app()->theme()->fetch();

                Core::app()->response()->setResponseText($fullcontent);

            } else {
                $res = Toolkit::getInstance()->client->renderAjaxScriptFiles();
                $res .= Toolkit::getInstance()->client->renderAjaxCssFiles();
                Core::app()->response()->setResponseText($res.$oResult->getHtml());
            }
        }

        return true;
    }
}