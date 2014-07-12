<?php

interface SimpleCollectionInterface extends Iterator, Countable, ArrayAccess
{
    public function toArray();
}