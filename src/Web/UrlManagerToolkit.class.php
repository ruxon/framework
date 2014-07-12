<?php

class UrlManagerToolkit extends ToolkitBase
{
    const URL_FORMAT_GET = 'get';
    const URL_FORMAT_PATH = 'path';

    public $urlFormat = 'get';

    public $params = array();

    public function parseUrlParams($url)
    {
        if ($this->urlFormat === self::URL_FORMAT_PATH) {
            $exploded = explode("/", $url);

            if (count($exploded)) {
                $params = array();
                $key = false;
                foreach ($exploded as $val) {
                    if ($key === false) {
                        $key = $val;
                    } else {
                        $params[$key] = $_GET[$key] = $_REQUEST[$key] = $val;
                        $key = false;
                    }
                }

                $this->params = $params;

                return $params;
            }
        }
    }

    public function createUrl($route, $params = array())
    {
        $routeJ = array();
        $url = '';
        if (!empty($route))
        {
            if (!is_array($route)) $route = array($route);
            foreach ($route as $val) {
                if ($p = trim($val)) {
                    $routeJ[] = $p;
                }
            }

            $url = implode('/', $routeJ);
        }

        if ($this->urlFormat === self::URL_FORMAT_PATH) {
            $paramsJ = array();
            if (!empty($params)) {
                foreach ($params as $key => $val)
                {
                    if (is_array($val))
                    {
                        $v_tmp = array();
                        foreach ($val as $v_key => $v_val) {
                            $v_tmp[] = $key."[".$v_key."]/".$v_val;
                        }
                        $paramsJ[] = implode("/", $v_tmp);
                    } else {
                        $paramsJ[] = "$key/$val";
                    }
                }

                if (!empty($paramsJ)) {
                    $url .= '/'. implode('/', $paramsJ);
                }
            }
        } else {
            $paramsJ = array();
            if (!empty($params)) {
                foreach ($params as $key => $val)
                {
                    if (is_array($val))
                    {
                        $v_tmp = array();
                        foreach ($val as $v_key => $v_val) {
                            $v_tmp[] = $key."[".$v_key."]=".$v_val;
                        }
                        $paramsJ[] = implode("&", $v_tmp);
                    } else {
                        $paramsJ[] = "$key=$val";
                    }

                }

                if (!empty($paramsJ)) {
                    $url .= '?' . implode('&', $paramsJ);
                }
            }
        }

        return strpos($url, "/") === 0 ? $url : '/' . $url;
    }

    public function getParams()
    {
        $params = $_GET;
        $url = substr($_SERVER['REQUEST_URI'], 1);
        if (strrpos($url, "?") !== false) {
            $url = substr($url, 0, strrpos($url, "?"));
        }
        unset($params['path']);
        unset($params[$url]);

        return $params;
    }
}