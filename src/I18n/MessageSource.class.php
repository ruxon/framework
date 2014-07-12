<?php

class MessageSource
{
    /**
     * Перевод строки
     *
     * @param $category категория
     * @param $message сообщение
     * @param $language язык, на который переводим
     * @param $basePath путь к файлам перевода
     * @return string|bool
     */
    public function translate($category, $message, $language, $basePath)
    {
        $messages = $this->loadMessages($category, $language, $basePath);

       // Core::p($category);

        if ($language == Config::i()->getDefaultLang() && empty($messages[$message])) {
            return $message;
        } elseif (!empty($messages[$message])) {
            return $messages[$message];
        } else {
            return $message;
        }

        return false;
    }

    protected function loadMessages($category, $language, $basePath)
    {
        return [];
    }
}