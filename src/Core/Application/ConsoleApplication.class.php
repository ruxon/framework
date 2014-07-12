<?php

class ConsoleApplication extends Application
{
     protected $sAppName = 'ConsoleApplication';

    private static $instance = false;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new ConsoleApplication();
        }

        return self::$instance;
    }

    public function process()
    {
        $params = Toolkit::i()->request->getParams();

        if (count($params) < 2)
        {
            echo "Please specify module and command\n";
        } else {

            $module = $params[0];
            $controller = $params[1];

            Core::import('Modules.'.$module);

            $sController = $module.$controller.'Command';
            $sControllerFull = 'ruxon\modules\\'.$module.'\commands\\'.$sController;

            if (class_exists($sControllerFull)) {
                $oController = new $sControllerFull;
            } else {
                $oController = new $sController;
            }

            unset($params[0]);
            unset($params[1]);
            $par = array_values($params);
            $action_params = array();
            if (count($par))
            {
                foreach ($par as $k => $val)
                {
                    if (substr_count($val, "=") > 0)
                    {
                        $tmp = explode("=", $val);
                        $action_params[$tmp[0]] = trim($tmp[1]);
                    } else {
                        $action_params[] = $val;
                    }
                }
            }

            $oController->run('Execute', $action_params);
        }
    }
}