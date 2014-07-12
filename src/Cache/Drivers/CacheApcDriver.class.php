<?php

class CacheApcDriver extends CacheDriver
{
    public function add($sKey, $mValue, $nExpire = false) {}
    public function set($sKey, $mValue, $nExpire = false) {}
    public function get($sKey) {}
    public function delete($sKey) {}
    public function flush() {}
    public function increment($sKey, $nValue = 1) {}
    public function decrement($sKey, $nValue = 1) {}
}