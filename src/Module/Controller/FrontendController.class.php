<?php

/**
 *  FrontendController: default controller for frontend part of web application
 * 
 * @package Module
 * @subpackage Controller
 * @version 6.1
 */
class FrontendController extends Controller
{

    public static function getPageParams()
    {
        return false;
    }
    
    public function actionIndex($aParams = array())
    {
        $aConfig = $this->config('Index');
        if (empty($_GET['path']) && isset($aConfig['default']))
        {
            return call_user_func(array($this, 'action'.$aConfig['default']['Action']));
        } 
        else
        {
            $path = substr($_GET['path'], 1);
            
            $action = '';
            $bSuccess = false;
            if (!empty($aConfig['other']))
            {
                foreach ($aConfig['other'] as $rule) {
                    $aRs = array();
                    if (preg_match("#^".$rule['Pattern']."$#", $path, $aRs)) {
                        $bSuccess = true;
                        $action = $rule['Action'];

                        if (isset($rule['Params'])) 
                        {
                            foreach ($rule['Params'] as $key => $value) {
                                if (strpos($value, "$") === 0) {
                                    $aParams[$key] = $aRs[str_replace("$", "", $value)];
                                } else {
                                    $aParams[$key] = $value;
                                }
                            }
                        }
                        break;
                    }
                }
            }
            
            if ($bSuccess) {
                return call_user_func(array($this, 'action'.$action), $aParams);
            } else {
                $this->result404();
            } 
        }
    }
    
    public function result404()
    {
        Core::import('Modules.Main');
        $oController = new SiteController();
        $oResult = $oController->run('404', array());
        Core::app()->response()->setStatus(404);
        Core::app()->theme()->getLayout()->setContent($oResult->getHtml());
        Core::app()->response()->setResponseText(Core::app()->theme()->fetch());
        Core::app()->end();
        Core::app()->hardEnd();
    }

    public function result403()
    {
        Core::import('Modules.Main');
        $oController = new SiteController();
        $oResult = $oController->run('403', array());
        Core::app()->response()->setStatus(403);
        Core::app()->theme()->getLayout()->setContent($oResult->getHtml());
        Core::app()->response()->setResponseText(Core::app()->theme()->fetch());
        Core::app()->end();
        Core::app()->hardEnd();
    }
}
