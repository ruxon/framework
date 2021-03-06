<?php

class DateTimeObjectField extends ObjectField
{
    public function get(Object $oObject)
    {
        return $oObject->simpleGet($this->getAlias());
    }

    public function set(Object $oObject, $mValue)
    {
        return $oObject->simpleSet($this->getAlias(), $mValue);
    }
}