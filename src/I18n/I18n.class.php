<?php

class I18n extends ToolkitBase
{
    public $translations = [];

    public function translate($category, $message, $params = [], $language = null, $basePath = null)
    {
        if ($language === null) {
            $language = Config::i()->getLang();
        }

        $messageSource = $this->getMessageSource($category);
        $translate = $messageSource->translate($category, $message, $language, $basePath);

        return $this->format($translate, $params, $language);
    }

    public function format($message, $params, $language)
    {
        $params_new = [];
        $matches = [];

        preg_match_all("/\{[A-z0-9]+\}/isU", $message, $matches);

        if (count($matches[0])) {
            foreach ($matches[0] as $k => $val) {
                $message = str_replace($val, "{".$k."}", $message);
                $val_search = str_replace("{", "", str_replace("}", "", $val));
                $params_new[$k] = $params[$val_search];

            }
        }

        return MessageFormatter::formatMessage($language, $message, $params_new);
    }

    protected function getMessageSource($category = null)
    {
        if (isset($this->translations['*'])) {
            $source = $this->translations['*'];
            if ($source instanceof MessageSource) {
                return $source;
            } else {

                return $this->translations['*'] = new $source['class'];
            }
        }
    }
}