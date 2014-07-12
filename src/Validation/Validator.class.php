<?php

class Validator
{
	protected $aValidators = array();

    public function addRule(ValidationRule $rule)
    {
        $oGroup = new ValidationRulesGroup;
        $oGroup->add($rule);
        $this->aValidators[] = $oGroup;
        
        return true;
    }
    
    public function addRules($rules)
    {
        $oGroup = new ValidationRulesGroup;
        foreach ($rules as $rule) {
            $oGroup->add($rule);
        }
        $this->aValidators[] = $oGroup;
        
        return true;
    }
    
    public function addGroup(ValidationRulesGroup $oGroup)
	{
		$this->aValidators[] = $oGroup;

		return true;
	}

	public function forceCheck($aElementData)
	{
		$aErrors = array();

		foreach ($this->aValidators as $group) {

            if ($group->count()) {
                foreach ($group as $itm) {
                    $rs = '';
                    if ($itm instanceof ValidationCompareRule) {
                        if (isset($aElementData[$itm->getAlias()]) && isset($aElementData[$itm->getAlias2()])) {
                            $rs = $itm->check($aElementData[$itm->getAlias()], $aElementData[$itm->getAlias2()]);
                        } else {
                            $rs = true;
                        }
                    } elseif ($itm instanceof ValidationUniqRule || $itm instanceof ValidationAliasRule) {
                        $nElementId = isset($aElementData['Id']) ? (int)$aElementData['Id']: 0;
                        $rs = $itm->check($aElementData[$itm->getAlias()], $nElementId);
                    } else {
                        if (isset($aElementData[$itm->getAlias()])) {
                            $rs = $itm->check($aElementData[$itm->getAlias()]);
                        } else {
                            $rs = true;
                        }
                    }

                    if ($rs !== true) {
                        $aErrors[] = $rs;
                        break;
                    }
                }
            }
		}
        
		if (count($aErrors) > 0) {
			return $aErrors;
		}

		return true;
	}
}

?>
