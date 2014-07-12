<?php

class SimpleController extends Ruxon
{
    protected $aConfig = array();

    public function filters()
    {
        return array();
    }

    public function run($sAction, $aParams = array())
    {
        $sMethod = 'action'.$sAction;

        if (method_exists($this, $sMethod)) {

            $aFilters = $this->filters();

            $aActionFilters = array();
            if (is_array($aFilters) && count($aFilters)) {
                foreach ($aFilters as $key => $actions) {
                    if (is_array($actions)) {
                        foreach ($actions as $action) {
                            if (strcasecmp($sAction, $action) === 0) {
                                $aActionFilters[] = $key;
                                break;
                            }
                        }
                    } else if (strcasecmp($sAction, $actions) === 0){
                        $aActionFilters[] = $key;
                    }
                }

                if (count($aActionFilters)) {

                    $oFilterChain = new FilterChain();

                    foreach ($aActionFilters as $filter) {

                        $sFilterClass = $filter.'Filter';

                        $oFilterChain->registerFilter(new $sFilterClass);
                    }

                    $oFilterChain->process();
                }
            }


            $oAction = new Action($this, $sAction, $aParams);

            return $oAction->run();

        } else {
            $this->redirect('/site/404');
        }       
    }
}