<?php


abstract class DbMigration
{
    protected $dbConnection = 'default';

    abstract public function up();
    abstract public function down();

    public function safeUp()
    {
        $this->getDbConnection()->beginTransaction();

        try {
            $this->up();

            $this->getDbConnection()->commit();
        }
        catch(Exception $e)
        {
            echo "Exception: ".$e->getMessage()."\n";
            $this->getDbConnection()->rollback();

            return false;
        }
    }

    public function safeDown()
    {
        $this->getDbConnection()->beginTransaction();

        try {
            $this->down();

            $this->getDbConnection()->commit();
        }
        catch(Exception $e)
        {
            echo "Exception: ".$e->getMessage()."\n";

            $this->getDbConnection()->rollback();

            return false;
        }
    }

    protected function createTable($table, $columns)
    {
        $query = "CREATE TABLE {$this->quote("#__".$table)}( ";

        $first = true;
        foreach ($columns as $name => $definition) {
            if (!$first)
                $query.= " ,";
            $first = false;

            // hack for multiple primary keys
            if ($name == 'primary_key')
            {
                foreach ($definition['Keys'] as $k => $val)
                {
                    $definition['Keys'][$k] = "`".$val."`";
                }
                $query .= "PRIMARY KEY (".implode(",", $definition['Keys']).")";
            } else {
                $query.= "{$this->quote($name)} " . $this->columnDefinition($definition);
            }
        }

        $query.= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
        $this->getDbConnection()->query($this->getDbConnection()->parsePrefix($query));
    }

    protected function dropTable($table) {
        $this->getDbConnection()->query($this->getDbConnection()->parsePrefix("DROP TABLE {$this->quote("#__".$table)}"));
    }

    protected function renameTable($table, $name)
    {
        $this->getDbConnection()->query($this->getDbConnection()->parsePrefix("ALTER TABLE {$this->quote($table)} RENAME TO $name"));
    }

    protected function alterColumns($table, $columns)
    {
        foreach ($columns as $name => $def)
        {
            if ($def == 'Drop') {
                $this->getDbConnection()->query($this->getDbConnection()->parsePrefix("ALTER TABLE {$this->quote("#__".$table)} DROP COLUMN {$this->quote($name)}"));
                continue;
            }

            if (isset($def['create'])) {
                $this->getDbConnection()->query($this->getDbConnection()->parsePrefix("ALTER TABLE {$this->quote("#__".$table)} ADD COLUMN {$this->quote($name)} {$this->columnDefinition($def)} "));
                continue;
            }

            $new_name = isset($def['name']) ? $def['name'] : $name;
            $this->getDbConnection()->query($this->getDbConnection()->parsePrefix("ALTER TABLE {$this->quote("#__".$table)} CHANGE COLUMN {$this->quote($name)} {$this->quote($new_name)} {$this->columnDefinition($def, false)} "));
            continue;
        }
    }

    protected function columnDefinition($def, $with_keys = true)
    {
        $type = $def['Type'];
        if ($type == 'pk') {
            $def['Type'] = 'int(10)';
            $def['Primary'] = true;
        }

        $str = strtoupper($def['Type']) . " ";
        if ($type == 'enum') {
            $options = '';
            foreach ($def['Options'] as $key => $val) {
                if ($key > 0)
                    $options.= ',';
                $options.="'$val'";
            }
            $str.="($options) ";
        }

        if (isset($def['Size']))
            $str.= "({$def['Size']}) ";

        if (!empty($def['NotNull']) && empty($def['Primary']))
            $str.= "NOT NULL ";

        if (isset($def['Default']))
            $str.= "DEFAULT {$def['Default']} ";

        if ($type == 'pk')
            $str.= "AUTO_INCREMENT ";

        if (!empty($def['Primary']) && $with_keys)
            $str.= "PRIMARY KEY ";

        return $str;
    }

    protected function quote($str)
    {
        return $this->getDbConnection()->quoteColumnName($str);
    }

    protected function updateTableData($table, $data)
    {
        if (isset($data['Insert']))
        {
            foreach ($data['Insert'] as $insert)
            {
                $this->insert($table, $insert);
            }
        }

        if (isset($data['Update']))
        {
            foreach ($data['Update'] as $insert)
            {
                $this->update($table, $insert, $insert['id']);
            }
        }
    }

    protected function insert($table, $data)
    {
        $oQuery = new DbUpdater(DbUpdater::TYPE_INSERT, $table, 't', $this->dbConnection);
        $oQuery->setElement($data);
        return $oQuery->insert();
    }

    protected function update($table, $data, $pk)
    {
        $oQuery = new DbUpdater(DbUpdater::TYPE_UPDATE, $table, 't', $this->dbConnection);
        $oQuery->setElement($data);
        $oCriteria = new CriteriaElement('id', Criteria::EQUAL, $pk);
        $oQuery->addCriteria($oCriteria->renderWhere());
        $oQuery->update();
    }

    protected function getDbConnection()
    {
        return Manager::getInstance()->getDb($this->dbConnection);
    }
}