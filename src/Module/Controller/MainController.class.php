<?php

/**
 * MainController: default controller for an module
 * 
 * @package Module
 * @subpackage Controller
 * @version 6.1
 */
abstract class MainController extends Controller
{
    public function actionConfig($aParams = array())
    {
        $aConfig = $this->config('Config');
        
        $mResult = false;

        if ($aConfig) {
            if (!isset($aConfig['View']) || (isset($aConfig['View']) && $aConfig['View'] != false)) {
                $mResult = $this->view(array(), $aConfig['View']);
            } else if (isset($aConfig['Component'])) {
                $sComponentModule = $aConfig['Component']['Component'][0];
                $sComponentAlias  = $aConfig['Component']['Component'][1];
                $aComponentParams = isset($aConfig['Component']['Params']) ? $aConfig['Component']['Params'] : array();
                
                // Проверка доступа
                $this->checkActionAccess('Config');
                
                // load config
                $aComponentParams['Data'] = Manager::getInstance()->getModule($this->sModuleAlias)->config();
                
                if(!empty($_POST))
                {
                    //echo print_r($_POST, true);die();
                    $aResult = array();
                    
                    
                    if(Manager::getInstance()->getModule($this->sModuleAlias)->saveConfig($_POST)) 
                    {
                        $aResult['success'] = true;
                    } else {
                        $aResult['success'] = false;
                    }
                    
                    
                    header("Content-type: application/json");
                    echo json_encode($aResult);
                    Core::app()->hardEnd();
                    
                    
                } else {
                    $mResult = $this->component($sComponentModule, $sComponentAlias, $aComponentParams);
                }

            } else {
                $mResult = false;
            }
        }

        return $mResult;
    }
}