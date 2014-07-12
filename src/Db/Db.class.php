<?php

/**
 * Db
 *
 * @package Db
 * @version 6.0
 */
class Db
{
    protected $sDsn;

    protected $sUsername;

    protected $sPassword;

    protected $bIsActive;

    protected $aParams = array();

    protected $_pdo;

    protected $_builder;
    
    protected $_count_queries = 0;
    
    protected $_execution_time = 0;
    
    protected $_queries = array();

    /**
     * Constructor
     *
     * @param string $sDsn Connection string
     * @param string $sUsername Username
     * @param string $sPassword Password
     * @param array $aParams Connection params
     */
    public function __construct($sDsn, $sUsername, $sPassword, $aParams = array())
    {
        $this->sDsn = $sDsn;
        $this->sUsername = $sUsername;
        $this->sPassword = $sPassword;
        $this->bIsActive = false;
        $this->aParams = $aParams;

        $sBuilderClassName = ucfirst(strtolower($this->getDriverName())).'DbBuilder';
        $this->_builder = new $sBuilderClassName;
        $this->_builder->init($this->aParams);
    }

    public function getDriverName()
    {
        return mb_substr($this->sDsn, 0, mb_strpos($this->sDsn, ":"));
    }

    public function getPdo()
    {
        return $this->_pdo;
    }

    public function open()
    {
        $this->_pdo = new PDO($this->sDsn, $this->sUsername, $this->sPassword, array());

        $this->bIsActive = true;
        
        $this->init();

        $this->_execution_time = microtime(true);
        return true;
    }
    
    public function createCommand($sQuery)
    {
        if ($this->bIsActive) 
        {
            return new DbCommand($this, $this->parsePrefix($sQuery)); 
        }
        
        return false;
    }

    public function query($mQuery)
    {
        if (is_object($mQuery)) {
            $sQuery = $this->_builder->execute(call_user_func(array($mQuery, 'toArray')));
        } else {
            $sQuery = $mQuery;
        }
        
        if ($this->bIsActive) 
        {
            try
            {
                $this->_queries[] = $sQuery;
                $res = $this->getPdo()->query($this->parsePrefix($sQuery));
                $this->_count_queries++;

                return $res;
            }
            catch (Exception $o) 
            {
                if (RUXON_DEBUG)
                {
                    echo 'DB Query error: '.$o->getMessage();
                    echo '<br />';
                    echo 'Query: '.$sQuery;
                }
                
            }
        }
        
        return false;
    }

    public function fetchArray($mQuery, $fetchType = PDO::FETCH_ASSOC)
    {
        $aResult = array();

        $res = $this->query($mQuery);
        
        if ($res)
        {
            $res->setFetchMode($fetchType);

            while($row = $res->fetch()) {
                array_push($aResult, $row);
            }
        }
        
        return $aResult;
    }

    public function fetchRow($mQuery, $fetchType = PDO::FETCH_ASSOC)
    {
        $aResult = array();

        $res = $this->query($mQuery);
        if ($res)
        {
            $res->setFetchMode($fetchType);

            while($row = $res->fetch()) {
                $aResult = $row;
                break;
            }
        }

        return $aResult;
    }

    public function fetchCell($mQuery)
    {
        $mResult = false;

        $res = $this->query($mQuery);
        $res->setFetchMode(PDO::FETCH_BOTH);

        if ($row = $res->fetch()) {
            $mResult = $row[0];
        }

        return $mResult;
    }

    public function insert($mQuery)
    {
        $this->query($mQuery);
        
        return $this->getLastInsertId();
    }

    public function update($mQuery)
    {
        return $this->query($mQuery);
    }

    public function delete($mQuery)
    {
        return $this->query($mQuery);
    }

    public function close()
    {
        $this->_pdo = null;
        $this->_builder = null;
        $this->bIsActive = false;

        return true;
    }
    
    public function getLastInsertId()
    {
        if ($this->bIsActive) {
            return $this->getPdo()->lastInsertId();
        }
        
        return false;
    }
    
    public function getCountQuery()
    {
        return $this->_count_queries;
    }
    
    public function getQueryExecutionTime()
    {
        $end = microtime(true);
        
        return ($end - $this->_execution_time);
    }
    
    public function getQueriesLog()
    {
        return $this->_queries;
    }
    
    public function quoteColumnName($name)
    {
        return '`'.$name.'`';
    }
    
    public function backup($tables = '*')
    {
        //Получаем все таблицы
        if($tables == '*')
        {
            $tables = array();
            $tables_input = $this->fetchArray('SHOW TABLES', PDO::FETCH_BOTH);
            foreach ($tables_input as $row)
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }
        
        $return = "";
        //print_r($tables);die();

        foreach($tables as $table)
        {
            $return.= 'DROP TABLE IF EXISTS '.$table.';';
            $row2 = $this->fetchRow('SHOW CREATE TABLE '.$table, PDO::FETCH_BOTH);
            $return.= "\n\n".$row2[1].";\n\n";

            $rows = $this->fetchArray('SELECT * FROM '.$table);
            foreach ($rows as $k=>$row)
            {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                $j = 0;
                foreach ($row as $alias => $cell)
                {
                    $cell = addslashes($cell);
                    $cell = str_replace("\n","\\n",$cell);
                    if (isset($cell)) { $return.= '"'.$cell.'"' ; } 
                    else { $return.= '""'; }
                    if ($j<(count($row)-1)) { $return.= ','; }
                    $j++;
                }
                $return.= ");\n";
            }
            
            $return.="\n\n\n";
        }

        return $return;
    }

    public function tableExists($table)
    {
        try {
            $result = $this->getPdo()->query($this->parsePrefix("SELECT 1 FROM #__".$table." LIMIT 1"));
        } catch (Exception $e) {
            return false;
        }
        return $result !== false;
    }

    protected function init()
    {
        $this->getPdo()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->query("SET NAMES ".$this->aParams['Encoding']);

        return true;
    }
    
    public function parsePrefix($sSql)
	{
		return str_replace("#__", $this->aParams['Prefix'], $sSql);
	}

    public function beginTransaction()
    {
        return $this->_pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->_pdo->commit();
    }

    public function rollback()
    {
        return $this->_pdo->rollback();
    }

}