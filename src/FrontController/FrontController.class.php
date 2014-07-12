<?php

class FrontController
{
    private static $instance = false;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new FrontController();
        }

        return self::$instance;
    }

    public function getSite()
    {
        return method_exists(Core::app(), 'getSite') ? Core::app()->getSite() : false;
    }

    public function getSiteId()
    {
        return method_exists(Core::app(), 'getSite') ? $this->getSite()->getId() : false;
    }

    public function getPage()
    {
        return method_exists(Core::app(), 'getPage') ? Core::app()->getPage() : false;
    }

    public function getPageId()
    {
        return method_exists(Core::app(), 'getPage') ? $this->getPage()->getId() : false;
    }
}