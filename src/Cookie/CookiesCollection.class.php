<?php

class CookiesCollection extends SimpleCollection
{
    public function __construct($aCookies)
    {
        if (count($aCookies)) {
            foreach ($aCookies as $key => $itm) {
                $this->add(new Cookie($key, $itm), $key);
            }
        }
    }
}