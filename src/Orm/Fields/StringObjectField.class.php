<?php

class StringObjectField extends ObjectField
{
    public function get(Object $oObject)
    {
        return $oObject->simpleGet($this->getAlias());
    }

    public function set(Object $oObject, $mValue)
    {
        $mValue = preg_replace("/&amp;(#[0-9]+|[a-z]+);/i", "&$1;", htmlspecialchars($mValue, ENT_NOQUOTES));
        
        return $oObject->simpleSet($this->getAlias(), $mValue);
    }
}