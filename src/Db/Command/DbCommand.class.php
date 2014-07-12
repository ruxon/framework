<?php

class DbCommand
{
    protected $db;
    protected $statement;
    protected $query;
    protected $params;
    
    public function __construct($db, $query) 
    {
        $this->db = $db;
        $this->query = $query;
        $this->statement = $db->getPdo()->prepare($this->query);
    }
    
    public function bindParam ($parameter , &$variable, $data_type = PDO::PARAM_STR, $length = null, $driver_options = null )
    {
        if ($length === null)
        {
            $this->statement->bindParam($parameter, $variable, $data_type);
        } 
        else if ($driver_options === null)
        {
            $this->statement->bindParam($parameter, $variable, $data_type, $length);
        } 
        else 
        {
            $this->statement->bindParam($parameter, $variable, $data_type, $length, $driver_options);
        }
        
        return $this;
    }
    
    public function query()
    {
        return $this->statement->execute();
    }
    
    public function fetchArray($fetchType = PDO::FETCH_ASSOC)
    {
        $this->statement->execute();
        return $this->statement->fetchAll($fetchType);
    }

    public function fetchRow($fetchType = PDO::FETCH_ASSOC)
    {
        $this->statement->execute();
        return $this->statement->fetch($fetchType);
    }

    public function fetchCell($col = 0)
    {
        $this->statement->execute();
        return $this->statement->fetchColumn($col);
    }
}