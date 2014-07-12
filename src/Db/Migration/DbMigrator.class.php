<?php

abstract class DbMigrator
{
    protected $_dbConnection;
    
    protected $_module;
    
    protected $_currentVersion;
    
    protected $_versions = array();
    
    protected $_path;
    
    public function __construct($module, $connection = 'default')
    {
        $this->_module = $module;
        $this->_dbConnection = $connection;
        
        $this->init();
    }
    
    public function generateMigration($tableName, $mig_path = false, $index = 0)
    {
        $aResult = array();
        $columns = $this->getDbConnection()->fetchArray("SHOW COLUMNS FROM #__".$tableName);
        
        if (count($columns))
        {
            foreach ($columns as $column) 
            {
                $tmp = array(
                    'Type' => $column['Key'] == 'PRI' ? 'pk' : $column['Type'],
                );
                
                if ($column['Key'] && $column['Key'] != 'PRI')
                    $tmp['Key'] = $column['Key'];
                
                if ($column['Default'] != "")
                    $tmp['Default'] = $column['Default'];
                 
                 
                $aResult[$tableName]['Fields'][$column['Field']] = $tmp;
            }
        }
        
        $rows = $this->getDbConnection()->fetchArray('SELECT * FROM #__'.$tableName);
        if (count($rows))
        {
            foreach ($rows as $row)
            {
                $tmp = array();
                foreach ($row as $k => $col)
                {
                    $tmp[$k] = $col;
                }
                $aResult[$tableName]['Insert'][] = $tmp;
            }
        }
        
        if (!$mig_path)
            $mig_path = RX_PATH.'/ruxon/modules/'.$this->_module.'/migrations';
        
        if (!is_dir($mig_path))
            mkdir ($mig_path, 0777, true);
        
        $fileName = $index . "_" . $tableName . ".inc.php";
        $data = "<?php \n\nreturn ".var_export($aResult, true).";";
        
        file_put_contents($mig_path. '/' . $fileName, $data);
        
        $this->init();
        
        return true;
    }
    
    public function migrateTo($target = 'last') 
    {
        $from = null;
        $to = null;

        foreach ($this->_versions as $key => $version) {
            if ($version->id == $this->_currentVersion)
                $from = $key;
            if ($version->id == $target || ($target === 'last' && $key == count($this->_versions) - 1))
                $to = $key;
        }
        
        
        if ($to === null)
            $to = -1;

        if ($this->_currentVersion === null)
            $from = -1;

        if ($from === null)
            $from = -1;
        
        if ($to > $from) {
            for ($i = $from + 1; $i <= $to; $i++)
                $this->apply($i, 'up');
        }

        if ($from > $to){
            for ($i = $from; $i > $to; $i--) {
                $this->apply($i, 'down');
            }
        }
    }
    
    protected function apply($version_key, $direction) 
    {
        $version = $this->_versions[$version_key];
        $target_key = $direction == 'up' ? $version_key : ($version_key - 1);
        $target = @$this->_versions[$target_key];

        $current_schema = $this->getVersionSchema($this->_currentVersion);
        $target_schema = $this->getVersionSchema(@$target->id);
        
        if (property_exists($version, 'className'))
        {
            // Class
            $migration = new $version->className;

            if ($direction == 'up')
            {
                $migration->safeUp();
            } else {
                $migration->safeDown();
            }

        } else {

            // Simple
            $rules = $version->migration;

            if ($direction == 'down')
                $rules = array_reverse($rules);

            foreach ($rules as $table => $columns) {

                $data_updates = array();

                if (isset($columns['Insert'])) {
                    $data_updates['Insert'] = $columns['Insert'];
                    unset($columns['Insert']);
                }

                if (isset($columns['Update'])) {
                    $data_updates['Update'] = $columns['Update'];
                    unset($columns['Update']);
                }

                //Core::p($data_updates);

                /*if (!empty($data_updates) && $direction == 'down')
                    $this->updateTableData($table, $data_updates);
                */

                if ($columns == 'Drop' && $direction == 'up') {
                    $this->dropTable($table);
                    continue;
                }

                if ($columns == 'Drop' && $direction == 'down') {
                    $this->createTable($table, $target_schema[$table]);

                    if (!empty($data_updates)) {
                        $this->updateTableData($table, $data_updates);
                    }
                    continue;
                }

                $renamed = is_array($columns) && isset($columns['Rename']);
                if (!$renamed && $direction == 'down' && !isset($target_schema[$table])) {
                    $this->dropTable($table);
                    continue;
                }

                if ($direction == 'down' && is_array($columns))
                    $columns = array_reverse($columns);

                $target_table = $table;

                if ($renamed) {
                    if ($direction == 'up') {
                        $this->renameTable($table, $columns['Rename']);
                        $target_table = $columns['Rename'];
                    } else {
                        $this->renameTable($columns['Rename'], $table);
                    }
                    unset($columns['Rename']);
                }


                if (!$renamed && !isset($current_schema[$table])) {
                    $this->createTable($table, $target_schema[$target_table]);

                    if (!empty($data_updates) && $direction == 'up') {
                        $this->updateTableData($table, $data_updates);
                    }
                    continue;
                }

                $columns = $columns['Fields'];
                foreach ($columns as $name => $def) {

                    if ($direction == 'up' && $def == 'Drop')
                        continue;

                    if ($direction == 'down' && $def == 'Drop') {
                        $columns[$name] = $target_schema[$target_table][$name];
                        $columns[$name]['create'] = true;
                        continue;
                    }

                    $renamed = is_array($def) && isset($def['name']);

                    if (!$renamed && $direction == 'down' && !isset($target_schema[$target_table][$name])) {
                        $columns[$name] = 'Drop';
                        continue;
                    }

                    $target_name = $name;
                    $columns[$name] = array();

                    if (!isset($current_schema[$table][$name]))
                        $columns[$name]['create'] = true;

                    if ($renamed) {
                        if ($direction == 'up') {
                            $columns[$name]['name'] = $def['name'];
                            $target_name = $def['name'];
                        } else {
                            $columns[$def['name']]['name'] = $name;
                            unset($columns[$name]);
                            $name = $def['name'];
                        }
                    }
                    if (isset($target_schema[$target_table][$target_name]))
                        $columns[$name] = array_merge($columns[$name], $target_schema[$target_table][$target_name]);
                }

                if (!empty($columns))
                    $this->alterColumns($target_table, $columns);

                if (!empty($data_updates) && $direction == 'up') {
                    $this->updateTableData($table, $data_updates);
                }
            }
        }

        $this->_currentVersion = (int)@$target->id;

        Manager::getInstance()->getModule($this->_module)->saveDbRevision($this->_currentVersion);
        
        return true;
    }
    
    protected function updateTableData($table, $data) 
    {
        if (isset($data['Insert']))
        {
            foreach ($data['Insert'] as $insert) 
            {
                $oQuery = new DbUpdater(DbUpdater::TYPE_INSERT, $table, 't', $this->_dbConnection);
                $oQuery->setElement($insert);
                $oQuery->insert();
            }   
        }
        
        if (isset($data['Update']))
        {
            foreach ($data['Update'] as $insert) 
            {
                $oQuery = new DbUpdater(DbUpdater::TYPE_UPDATE, $table, 't', $this->_dbConnection);
                $oQuery->setElement($insert);
                $oCriteria = new CriteriaElement('id', Criteria::EQUAL, $insert['id']);
                $oQuery->addCriteria($oCriteria->renderWhere());
                $oQuery->update();
            }   
        }
    }
    
    public function getVersionSchema($target) 
    {
        if ($target == null || $target == -1)
            return array();
        
        $schema = array();
        foreach ($this->_versions as $version) {

            if (property_exists($version, 'migration') && is_array($version->migration) && count($version->migration))
            {
                foreach ($version->migration as $table => $columns) {
                    if ($columns == 'Drop') {
                        unset($schema[$table]);
                        continue;
                    }

                    if (!isset($schema[$table]))
                        $schema[$table] = array();

                    if (isset($columns['Insert']))
                        unset($columns['Insert']);

                    if (isset($columns['Update']))
                        unset($columns['Update']);

                    if (isset($columns['Rename'])) {
                        echo($columns['Rename']);
                        $schema[$columns['Rename']] = $schema[$table];
                        unset($schema[$table]);
                        $table = $columns['Rename'];
                        unset($columns['Rename']);
                    }
                    foreach ($columns['Fields'] as $column => $def) {
                        if ($def == 'Drop') {
                            unset($schema[$table][$column]);
                            continue;
                        }
                        if (!isset($schema[$table][$column]))
                            $schema[$table][$column] = array();
                        $schema[$table][$column] = array_merge($schema[$table][$column], $def);
                        if (is_array($def) && isset($def['name'])) {
                            $schema[$table][$def['name']] = $schema[$table][$column];
                            unset($schema[$table][$column]);
                            unset($schema[$table][$def['name']]['name']);
                        }
                    }
                }
            }
            if ($target == $version->id)
                break;
        }
        
        return $schema;
    }
    
    protected abstract function dropTable($table);
    protected abstract function renameTable($table, $name);
    protected abstract function alterColumns($table, $columns);
    protected abstract function createTable($table, $columns);
    
    protected function init()
    {
        Core::import('Modules.'.$this->_module);
        $this->_path = RX_PATH.'/ruxon/modules/'.$this->_module.'/migrations';

        if ($this->getDbConnection()->tableExists('main_module')) {
            $this->_currentVersion = (int) Manager::getInstance()->getModule($this->_module)->dbRevision();
        } else {
            $this->_currentVersion = 0;
        }

        $this->_versions = array();
        
        if (is_dir($this->_path))
        {
            $files = scandir($this->_path);
            natsort($files);
            foreach ($files as $file) {
                if ($file[0] == '.' || $file[0] == '..')
                    continue;

                $info = pathinfo($file);


                $tmpData = array(
                    'id' => substr($info['filename'], 0, strpos($info['filename'], "_")),
                    'name' => $info['filename']
                );

                if (strrpos($info['filename'], ".class") !== false)
                {
                    // Class
                    include_once($this->_path . '/' . $file);

                    $tmpClassName = substr($file, strpos($file, "_") + 1);
                    $tmpData['className'] = str_replace(".class.php", "", $tmpClassName);

                } else {
                    // Simple
                    $tmpData['migration'] = include($this->_path . '/' . $file);
                }

                $this->_versions[] = (object) $tmpData;
            }
        }
    }


    protected function getDbConnection()
    {
        return Manager::getInstance()->getDb($this->_dbConnection);
    }
}