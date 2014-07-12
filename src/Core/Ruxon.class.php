<?php

class Ruxon extends SimpleObject
{
    use Eventable;
    
    protected $aBehaviors = array();

    public function set($sField, $mData)
    {
        $this->aData[$sField] = $mData;

        return true;
    }

    public function get($sField, $aParams = array())
    {
        return (isset($this->aData[$sField]) ? $this->aData[$sField] : false);
    }
    
    public function attachBehavior($alias, $behavior)
    {
        $beh = new $behavior;
        $beh->init();
        if ($beh->attach($this)){
            $this->aBehaviors[$alias] = $beh;
            
            return true;
        }
        
        return false;
    }
    
    public function detachBehavior($alias)
    {
        if (isset($this->aBehaviors[$alias])) 
        {
            $behavior = $this->aBehaviors[$alias];
            $behavior->detach();
            unset($this->aBehaviors[$alias]);
            
            return true;
        }
        
        return false;
    }
    
    public function attachBehaviors($behaviors)
    {
        if (count($behaviors)) 
        {
			foreach ($behaviors as $alias => $behavior) 
            {
				$this->attachBehavior($alias, $behavior);
			}
		}
    }
    
    public function detachBehaviors()
    {
        if (count($this->aBehaviors)) {
            foreach ($this->aBehaviors as $alias => $behavior) 
            {
				$this->detachBehavior($alias);
			}
        }
    }
    
    public function getBehavior($alias)
    {
        if (isset($this->aBehaviors[$alias])) 
        {
            return $this->aBehaviors[$alias];
        }
        
        return false;
    }
    
    public function getBehaviors()
    {
        return $this->aBehaviors;
    }
    
    public function __call($sMethod, $aParams = array())
    {
        $aParams = !count($aParams) ? array() : $aParams[0];
		$sName = substr($sMethod, 3);
        
        $res = false;

        // getter
		if (strpos($sMethod, 'get') === 0) {
			$res = $this->get($sName, $aParams);
            
        // setter
		} elseif (strpos($sMethod, 'set') === 0) {
            $res = $this->set($sName, $aParams);
            
        // attach event handler
		} elseif (strpos($sMethod, 'on') === 0) {
            $res = $this->on(substr($sMethod, 2), $aParams);
		} 
        
		if (count($this->aBehaviors) && !$res)
        {
            return $this->getInBehaviors($sMethod);
        } else if ($res) {
            return $res;
        }
    }
    
    public function getInBehaviors($sMethod)
    {
        foreach ($this->aBehaviors as $behavior)
        {
            if (method_exists($behavior, $sMethod))
            {
                return call_user_func(array($behavior, $sMethod));
            }
        }
        
        return false;
    }
    
    public function raiseBehaviorEvent($sMethod)
    {
        if (count($this->aBehaviors))
        {
            foreach ($this->aBehaviors as $behavior)
            {
                if (method_exists($behavior, $sMethod))
                {
                    call_user_func(array($behavior, $sMethod));
                }
            }
        }
        
        return true;
    }
    
    public function getDbConnection($alias = 'default')
    {
        return Manager::getInstance()->getDb($alias);
    }
    
    public function component($sModuleAlias, $sComponentAlias, $aParams = array())
    {
        Core::import('Components.'.$sModuleAlias.'.'.$sComponentAlias);


        $sFullClassName = 'ruxon\modules\\'.$sModuleAlias.'\components\\'.$sComponentAlias.'\classes\\'.$sComponentAlias.'Component';
        $sClassName = $sModuleAlias.$sComponentAlias.'Component';

        $oComponent = class_exists($sFullClassName) ? new $sFullClassName : new $sClassName;
        $oComponent->init($aParams);

        $oComponent->run();

        $bLayout = ($this->sLayout === false ? false : true);

        $oResponse = new ActionResponse($bLayout);

        $oResponse->setHtml($oComponent->fetch());

        return $oResponse;
    }
    
    public function widget($sModuleAlias, $sComponentAlias, $aParams = array())
    {
        echo $this->component($sModuleAlias, $sComponentAlias, $aParams)->getHtml();
    }
}