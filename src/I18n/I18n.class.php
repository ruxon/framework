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
        $matches = [];

        preg_match_all("/\{[A-z0-9]+\}/isU", $message, $matches);

        if (count($matches[0])) {
            foreach ($matches[0] as $k => $val) {
                $message = str_replace($val, "{".$k."}", $message);
            }
        }

        return MessageFormatter::formatMessage($language, $message, $params);
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