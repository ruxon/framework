<?php

interface SimpleObjectInterface extends ArrayAccess, Iterator
{
    function get($sName, $aParams = array());
    function set($sName, $sValue);
    function reset();
    function export();
    function import($aValues);
}