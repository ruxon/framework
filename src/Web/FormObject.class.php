<?php

abstract class FormObject extends Ruxon
{
    protected $errors = array();

    protected $sModuleAlias = '';

    public function validate()
    {
        $validator = new Validator();
        $aData = $this->export();
        foreach ($this->fields() as $alias => $field)
        {
            if (!empty($field['Validation']))
            {
                $val_group = new ValidationRulesGroup();

                foreach ($field['Validation'] as $v_type => $v_params)
                {
                    if ($v_type == 'Required' && $v_params === false) {
                        continue;
                    }

                    if (!is_array($v_params)) $v_params = array($v_params);
                    $class_name = 'Validation'.$v_type.'Rule';
                    $all_params = array_merge(array($alias, @$field['Title']), $v_params);
                    $val_group->add(new $class_name($all_params));
                }

                $validator->addGroup($val_group);
            }
        }

        if (($res = $validator->forceCheck($aData)) === true)
        {
            return true;
        } else {
            $this->errors = $res;
        }

        return false;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function fieldTitle($field)
    {
        if (!empty($this->fields()[$field])) {
            return $this->fields()[$field]['Title'];
        }

        return false;
    }

    public function t($category, $message, $params = [], $language = null)
    {
        return Core::app()->t($category, $message, $params, $language, 'ruxon/modules/'.$this->sModuleAlias.'/messages');
    }
}