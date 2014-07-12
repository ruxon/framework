<?php

class Action
{
    protected $oController = false;

    protected $sAction = false;

    protected $aParams = array();

    public function __construct($oController, $sAction, $aParams = array())
    {
        $this->oController = $oController;
        $this->sAction = $sAction;
        $this->aParams = $aParams;
    }

    public function run()
    {
        $sMethod = 'action'.$this->sAction;
       
        if (method_exists($this->oController, $sMethod)) {

            $oResult = call_user_func_array(array($this->oController, $sMethod), array($this->aParams));

            return $oResult;
        }

        return false;
    }
}