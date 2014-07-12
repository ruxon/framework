<?php

class Pagination
{
    protected $pageSize = 10;
    
    protected $criteria;
    
    protected $itemsCount;
    
    protected $currentPage = null;
    
    protected $pageVar = 'page';
    
    public function __construct($count)
    {
        $this->itemsCount = $count;
        $this->getCurrentPage();
    }
    
    public function setPageSize($size)
    {
        $this->pageSize = $size;
        
        return true;
    }
    
    public function getPageSize()
    {
        return $this->pageSize;
    }
    
    public function applyLimit($criteria)
    {
        $this->criteria = $criteria;
        
        return true;
    }
    
    public function setCurrentPage($value)
    {
        $this->currentPage = $value;
        $_GET[$this->pageVar] = $value + 1;
    }
    
    public function getCurrentPage()
    {
        if ($this->currentPage === null)
        {
            if(isset($_GET[$this->pageVar]))
            {
                $this->currentPage = (int) $_GET[$this->pageVar];
            } 
            else
            {
                $this->currentPage = 1;
            }
        }
        
        return $this->currentPage;
    }
    
    public function getPageCount()
    {
        return (int)(($this->itemsCount + $this->pageSize - 1) / $this->pageSize);
    }
    
    public function getOffset()
    {
        return $this->getCurrentPage() * $this->getPageSize();
    }
}