<?php

class Manager
{
    protected $oDbManager = false;

    protected $oModulesManager = false;

    protected $oMappersManager = false;

    protected $oCacheManager = false;

    private static $instance = false;

    private function __construct()
    {
        $this->oDbManager = new ManagerDbDriver();
        $this->oModulesManager = new ManagerModulesDriver();
        $this->oMappersManager = new ManagerMappersDriver();
        $this->oCacheManager = new ManagerCacheDriver();
    }

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$instance = new Manager();
        }

        return self::$instance;
    }

    public function getDb($sKey = false)
    {
        if ($sKey) {
            $db = $this->oDbManager->get($sKey);

            if (!$db) {
                $val = Config::i()->getDbById($sKey);

                if ($val)
                {
                    $db = new Db($val['ConnectionString'], $val['Username'], $val['Password'], $val['Params']);
                    $db->open();

                    $this->oDbManager->add($db, $sKey);

                    return $db;
                }

                return false;
            }

            return $db;
        } else {
            return $this->oDbManager;
        }
    }

    public function getModule($sKey = false)
    {
        if ($sKey) {
            return $this->oModulesManager->get($sKey);
        } else {
            return $this->oModulesManager;
        }
    }

    public function setModule($sKey, $oModule)
    {
        return $this->oModulesManager->add($oModule, $sKey);
    }

    public function getMapper($sKey)
    {
        try {
            if (!$this->oMappersManager->exists($sKey)) {

                if (!class_exists($sKey))
                    throw new RxException('Class '.$sKey.' not found!');


                    $this->setMapper($sKey, new $sKey);
            }
        } catch (RxException $exc) {
            echo $exc->getMessage();
            Core::p($exc->getTraceAsString());
        }
        

        return $this->oMappersManager->get($sKey);
    }

    public function setMapper($sKey, ObjectMapper $oMapper)
    {
        return $this->oMappersManager->add($oMapper, $sKey);
    }    

    public function getCache($sKey = false)
    {
        if ($sKey) {
            return $this->oCacheManager->get($sKey);
        } else {
            return $this->oCacheManager;
        }
    }
}