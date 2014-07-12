<?php

/**
 *  BackendController: default controller for backend part of web application
 * 
 * @package Module
 * @subpackage Controller
 * @version 6.1
 */
class BackendController extends Controller
{
    /**
     * Actions filters
     * 
     * @return array 
     */
    public function filters()
    {
        return array(
            'AdminAccess' => array('Index', 'List', 'Create', 'Update', 'Delete', 'Detail', 'SimpleList'),
            'AjaxOnly' => array('Index', 'List', 'Create', 'Update', 'Delete', 'Detail', 'SimpleList')
        );
    }
    
    /**
     * Index action: default action for the controller
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionIndex($aParams = array())
    {
        return $this->view('Index');
    }

    public function actionUpload($aParams = array())
    {
        $upload = new UploadHandler(array(
            'upload_dir' => Toolkit::getInstance()->fileStorage->bucket('images')->getPath().'/',
            'upload_url' => Toolkit::getInstance()->fileStorage->bucket('images')->getUrl().'/',
            'script_url' => Toolkit::getInstance()->request->getUrl(),
            'accept_file_types' => '/\.(gif|jpe?g|png|swf)$/i'
        ));
        
        Core::app()->hardEnd();
        //return $this->view('Index');
    }
    
    /**
     * List action: list of elements
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionList($aParams = array())
    {
        $aConfig = $this->config('List');
        $limit = 10;
        
        $aFindParams = array();
        
        if (count($aParams) && $this->sMapperAlias) 
        {
            foreach ($aParams as $key=> $param)
            {
                if ($this->mapper()->isRealFieldExists($key))
                {
                    $aFindParams['Criteria'][$key] = $param;
                }
            }
        }
        
        $page = isset($_GET['page']) ? intval($_GET['page']) - 1 : 0;
        
        $aFindParams['Limit'] = $limit;
        $aFindParams['Offset'] = $page * $limit;
        
        $sort_field = isset($_GET['sort_field']) ? $_GET['sort_field'] : 'Id';
        $sort_direction = isset($_GET['sort_direction']) ? $_GET['sort_direction'] : 'ASC';
        
        $aFindParams['Order'] = array();
        //echo '<pre>', print_r($aFindParams, true), '</pre>'; die();
        
        if (isset($aConfig['Component']['FindParams'])) {
            $aFindParams = ArrayHelper::merge($aFindParams, $aConfig['Component']['FindParams']);
        }
        
        if (!count($aFindParams['Order']))
        {
            $aFindParams['Order'] = array($sort_field => $sort_direction);
        }
        
        $mResult = false;

        if ($aConfig) {
            if (!isset($aConfig['View']) || (isset($aConfig['View']) && $aConfig['View'] != false)) {
                $mResult = $this->view(array(), $aConfig['View']);
            } else if (isset($aConfig['Component'])) {
                $sComponentModule = $aConfig['Component']['Component'][0];
                $sComponentAlias  = $aConfig['Component']['Component'][1];
                $aComponentParams = isset($aConfig['Component']['Params']) ? $aConfig['Component']['Params'] : array();
                
                // Проверка доступа
                $this->checkActionAccess('List');
                
                $aComponentParams['Data'] = $this->getListData($aFindParams);
                $count = $this->getListDataCount($aFindParams);
                
                $oPagination = new Pagination($count);
                $oPagination->setPageSize($aFindParams['Limit']);
                $oPagination->applyLimit(@$aFindParams['Criteria']);
                $aComponentParams['Pagination'] = $oPagination;
                        
                $mResult = $this->component($sComponentModule, $sComponentAlias, $aComponentParams);

            } else {
                $mResult = false;
            }
        } else {
            $mResult = $this->view('List');
        }

        return $mResult;
    }

    /**
     * Create action: create an element
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionCreate($aParams = array())
    {
        $aConfig = $this->config('Create');
        
        $mResult = false;

        if ($aConfig) {
            if (!isset($aConfig['View']) || (isset($aConfig['View']) && $aConfig['View'] != false)) {
                $mResult = $this->view(array(), $aConfig['View']);
            } else if (isset($aConfig['Component'])) {
                $sComponentModule = $aConfig['Component']['Component'][0];
                $sComponentAlias  = $aConfig['Component']['Component'][1];
                $aComponentParams = isset($aConfig['Component']['Params']) ? $aConfig['Component']['Params'] : array();
                
                $model = $this->getCreateObject();
                
                // Проверка доступа
                $this->checkActionAccess('Create', $model);
                
                if ($this->sMapperAlias && count($aParams)) 
                {
                    foreach ($aParams as $key=> $param)
                    {
                        $field = $this->mapper()->getFieldByRealName($key);
                        call_user_func_array(array($model, 'set'), array($field, $param));
                    }
                }
                
                $aComponentParams['Data'] = $model;
                
                if(!empty($_POST))
                {
                    //echo print_r($_POST, true);die();
                    $aResult = array();
                    
                    //echo print_r($model->save(), true);die();
                    $model = $this->saveCreateObject();
                    if (!$model->isDirty() || !($model instanceof Object)) 
                    {
                        $this->afterCreateEvent($model);
                        
                        $aResult['update_url'] = $this->getUpdateUrlForObject($model);
                        $aResult['success'] = true;
                    } else {
                        $aResult['success'] = false;
                        $aResult['errors'] = $model->getErrors();
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
        } else {
            $mResult = $this->view('Create');
        }

        return $mResult;
    }
    
    public function afterCreateEvent($object) {}

    /**
     * Update action: update an element
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionUpdate($aParams = array())
    {
        $aConfig = $this->config('Update');
        
        $mResult = false;

        if ($aConfig) {
            if (!isset($aConfig['View']) || (isset($aConfig['View']) && $aConfig['View'] != false)) {
                $mResult = $this->view(array(), $aConfig['View']);
            } else if (isset($aConfig['Component'])) {
                $sComponentModule = $aConfig['Component']['Component'][0];
                $sComponentAlias  = $aConfig['Component']['Component'][1];
                $aComponentParams = isset($aConfig['Component']['Params']) ? $aConfig['Component']['Params'] : array();
                
                $model = $this->getUpdateObject($aParams);
                
                // Проверка доступа
                $this->checkActionAccess('Update', $model);
                
                $aComponentParams['Data'] = $model;
                
                if(!empty($_POST))
                {
                    
                    $aResult = array();
                    $aUploadNames = array();
          
                    $model = $this->saveUpdateObject($aParams);
                    //Core::p($model); die();
                    if(!$model->isDirty() || !($model instanceof Object)) 
                    {
                        /*if (!empty($_FILES))
                        {
                            // upload files
                            $model->uploadFiles();
                        }
                        
                        if ($main_image) {
                            $model->main_image = Yii::app()->fileStorage->bucket('images')->saveUploadedFile($model, false);
                        }*/

                        $aResult['success'] = true;
                    } else {
                        $aResult['success'] = false;
                        $aResult['errors'] = $model->getErrors();
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
        } else {
            $mResult = $this->view('Update');
        }

        return $mResult;
    }

    /**
     * Delete action: delete an element
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionDelete($aParams = array())
    {
        $aConfig = $this->config('Delete');

        $mResult = false;

        if ($aConfig) {
            if (!isset($aConfig['View']) || (isset($aConfig['View']) && $aConfig['View'] != false)) {
                $mResult = $this->view(array(), $aConfig['View']);
            } else if (isset($aConfig['Component'])) {
                $sComponentModule = $aConfig['Component']['Component'][0];
                $sComponentAlias  = $aConfig['Component']['Component'][1];
                $aComponentParams = isset($aConfig['Component']['Params']) ? $aConfig['Component']['Params'] : array();
                
                $object = $this->getUpdateObject($aParams);
                
                // Проверка доступа
                $this->checkActionAccess('Delete', $object);
                
                $this->deleteObject($aParams);
                
                $aComponentParams['ObjectParams'] = $aParams;
                $aComponentParams['ObjectData'] = $object->export();
                
                $mResult = $this->component($sComponentModule, $sComponentAlias, $aComponentParams);

            } else {
                $mResult = false;
            }
        } else {
            $mResult = $this->view('Delete');
        }

        return $mResult;
    }
    
    public function afterDeleteEvent($data) {}

    /**
     * Detail action: show element details
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionDetail($aParams = array())
    {
        return $this->view('Detail');
    }

    /**
     * SimpleList action: simple elements list
     * 
     * @param array $aParams array of input params
     * @return ActionResponse 
     */
    public function actionSimpleList($aParams = array())
    {
        $aResult = array();
        $items = $this->mapper()->find(array(
            'Criteria' => array(
                'Name' => array(
                    'Type' => Criteria::LIKE,
                    'Value' => '%'.$_GET['term'].'%'
                )
            )
        ));

        if ($items->count()) {
            foreach ($items as $itm) {
                $aResult[] = array(
                    'id' => $itm->getId(),
                    'title' => $itm->getName(),
                    'image' => $itm->getGallery()->getCoverThumbUrl(50, true, 50)
                );
            }
        }

        header("Content-type: application/json");
        echo json_encode($aResult);
        Core::app()->hardEnd();
    }
    
    public function getListData($aParams = array())
    {
        return $this->mapper()->find($aParams);
    }
    
    public function getListDataCount($aParams = array())
    {
        return $this->mapper()->count($aParams);
    }
    
    public function getCreateObject($aParams = array())
    {
        return $this->mapper()->create();
    }
    
    public function saveCreateObject($aParams = array())
    {
        $model = $this->getCreateObject($aParams);
        $model->import($_POST);
        $model->save();
        
        return $model;
    }
    
    public function getUpdateObject($aParams = array())
    {
        return $this->mapper()->findById($aParams['Id']);
    }
    
    public function saveUpdateObject($aParams = array())
    {
        $model = $this->getUpdateObject($aParams);
        $model->import($_POST);
        $model->save();
        
        return $model;
    }
    
    public function deleteObject($aParams = array())
    {
        $model = $this->mapper()->findById($aParams['Id']);
        $data = $model->export();
        $model->delete();
        $this->afterDeleteEvent($data);
        
        return true;
    }
    
    public function getUpdateUrlForObject($model)
    {
        $module = $this->sModuleAlias;
        $controller = $this->sControllerAlias;
        $module = substr(str_replace(array("A", "B", "C", "D", "E", "F", "J", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"), array("_a", "_b", "_c", "_d", "_e", "f", "_g", "_h", "_i", "_j", "_k", "_l", "_m", "_n", "_o", "_p", "_q", "_r", "_s", "_t", "_u", "_v", "_w", "_x", "_y", "_z"), $module), 1);
        $controller = substr(str_replace(array("A", "B", "C", "D", "E", "F", "J", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"), array("_a", "_b", "_c", "_d", "_e", "f", "_g", "_h", "_i", "_j", "_k", "_l", "_m", "_n", "_o", "_p", "_q", "_r", "_s", "_t", "_u", "_v", "_w", "_x", "_y", "_z"), $controller), 1);

        return '/'.$module.'/'.$controller.'/update/'.$model->getId();
    }
}