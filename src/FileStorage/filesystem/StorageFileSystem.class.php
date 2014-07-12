<?php

class StorageFileSystem extends StorageBase
{
    public $buckets;
    
    protected $_buckets;
    
    /**
     * Initialization storage system
     */
    public function init()
    {
        if (count($this->buckets))
        {
            foreach ($this->buckets as $alias => $bucket) 
            {
                $this->_buckets[$alias] = new BucketFileSystem(
                    $bucket['basePath'],
                    $bucket['baseUrl'],
                    isset($bucket['filePermission']) ? $bucket['filePermission'] : 0777
                );
            }
        }
    }
    
    /**
     *  Return object of bucket
     * 
     * @param string $name Bucket's name
     * @return PiBucketFileSystem|false
     */
    public function bucket($name)
    {
        if ($this->exists($name))
        {
            return $this->_buckets[$name];
        }
        
        return false;
    }
    
    /**
     * Check bucket exists
     * 
     * @param string $name
     * @return boolean
     */
    public function exists($name)
    {
        return isset($this->_buckets[$name]);
    }
    
}