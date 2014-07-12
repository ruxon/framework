<?php


class PhpMessageSource extends MessageSource
{
    protected $messages = [];

    protected function loadMessages($category, $language, $basePath)
    {

        if (empty($this->messages[$basePath][$category])) {

            $this->messages[$basePath][$category] = \Loader::loadConfigFile(RX_PATH.'/'.$basePath.'/'.$language, $category);
        }

        return $this->messages[$basePath][$category];
    }
} 