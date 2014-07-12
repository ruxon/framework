<?php

class TestWebApplication extends WebApplication
{
    protected $sAppName = 'TestWebApplication';

    private static $instance = false;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new TestWebApplication();
        }

        return self::$instance;
    }
}