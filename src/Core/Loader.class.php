<?php

class Loader
{
    public static function import($mPath)
    {
        $aPath = array();
        if (is_array($mPath)) {
            $aPath = $mPath;
        } else {
            $aPath = explode(".", $mPath);
        }

        $sPath = '';
        $sFile = '';
        if (isset($aPath[0])) {
            switch($aPath[0]) {

                // Модули
                case 'Modules':

                    if (!isset($_ENV['RUXON_REGISTRY_MODULES'][$aPath[1]])) {
                        $sModuleAlias = $aPath[1];

                        $module = Core::app()->getModuleById($sModuleAlias);

                        $sBasePath = !empty($module['BasePath']) ? $module['BasePath'] : 'ruxon/modules/'.$sModuleAlias;

                        $aModuleInfo = self::loadConfigFile(realpath(RX_PATH.'/'.$sBasePath), 'module');

                        if (!empty($aModuleInfo))
                        {
                            if (!is_dir(RX_PATH.'/runtime/cache/'))
                                mkdir (RX_PATH.'/runtime/cache/', 0777, true);

                            if (!RUXON_DEBUG && file_exists(RX_PATH.'/runtime/cache/module_'.$sModuleAlias.'.classes.php') && filemtime(RX_PATH.'/runtime/cache/module_'.$sModuleAlias.'.classes.php') >= time() - 3600)
                            {
                                $aFiles = include(RX_PATH.'/runtime/cache/module_'.$sModuleAlias.'.classes.php');
                            } else {
                                $aFiles = rx_glob($sBasePath);
                                file_put_contents(RX_PATH.'/runtime/cache/module_'.$sModuleAlias.'.classes.php', "<?php\nreturn ".var_export($aFiles, true).";");
                            }

                            if (count($aFiles)) {

                                $namespace = !empty($module['BasePath']) ? 'ruxon\modules\\'.$sModuleAlias : null;
                                $namespace_replace = !empty($module['BasePath']) ? realpath($module['BasePath']) : null;

                                foreach ($aFiles as $file) {
                                    self::require_file($file, true, true, $namespace, $namespace_replace);
                                }
                            }

                            if (isset($aModuleInfo['RequiredModules']) && is_array($aModuleInfo['RequiredModules']) && count($aModuleInfo['RequiredModules']) > 0) {
                                foreach ($aModuleInfo['RequiredModules'] as $package) {
                                    if (!isset($_ENV['RUXON_REGISTRY_MODULES'][$package])) {
                                        self::import('Modules.'.$package);
                                    }
                                }
                            }

                            $_ENV['RUXON_REGISTRY_MODULES'][$sModuleAlias] = 1;

                            $sClassName = $sModuleAlias.'Module';
                            $classNameWithNamespaces = '\ruxon\modules\\'.$sModuleAlias.'\classes\\'.$sClassName;

                            Manager::getInstance()->setModule($sModuleAlias, class_exists($classNameWithNamespaces) ? new $classNameWithNamespaces : new $sClassName);
                        }
                    }

                    break;

                // Дополнения
                case 'Extensions':
                    if (!isset($_ENV['RUXON_REGISTRY_EXTENSIONS'][$aPath[1]])) {
                        for ($i = 1; $i < count($aPath); $i++) {
                            $sPath .= '/'.$aPath[$i];
                        }
                        $sBasePath = 'ruxon/extensions'.$sPath;
                        $sFile = '/extension.inc.php';

                        $aResult = self::loadConfigFile($sBasePath, 'extension');

                        if ($aResult !== false) {

                            $cache_file = RX_PATH.'/runtime/cache/extension_'.$aPath[1].'.classes.php';

                            if (!RUXON_DEBUG && file_exists($cache_file) && filemtime($cache_file) >= time() - 3600)
                            {
                                $aFiles = include($cache_file);
                            } else {
                                $aFiles = rx_glob($sBasePath);
                                file_put_contents($cache_file, "<?php\nreturn ".var_export($aFiles, true).";");
                            }

                            if (count($aFiles)) {
                                foreach ($aFiles as $file) {
                                    self::require_file($file, true, true);
                                }
                            }
                            $_ENV['RUXON_REGISTRY_EXTENSIONS'][$aPath[1]] = 1;
                        } else {
                            throw new RxException('Файл "'.$sBasePath.$sPath.$sFile.'" не может быть загружен.');
                        }
                    }
                    break;

                // Компоненты
                case 'Components':

                    // Подключаем компонент
                    if (count($aPath) == 3) {
                        if (!isset($_ENV['RUXON_REGISTRY_COMPONENTS'][$aPath[1]][$aPath[2]])) {
                            $sModuleAlias = $aPath[1];
                            $sComponentAlias  = $aPath[2];

                            $module = Core::app()->getModuleById($sModuleAlias);

                            $sBasePath = !empty($module['BasePath']) ? $module['BasePath'].'/components/'.$sComponentAlias : 'ruxon/modules/'.$sModuleAlias.'/components/'.$sComponentAlias;

                            $aModelInfo = self::loadConfigFile(RX_PATH.'/'.$sBasePath, 'component');

                            if (!is_dir(RX_PATH.'/runtime/cache/'))
                                mkdir (RX_PATH.'/runtime/cache/', 0777, true);


                            $cache_file = RX_PATH.'/runtime/cache/component_'.$sModuleAlias.'_'.$sComponentAlias.'.classes.php';

                            if (!RUXON_DEBUG && file_exists($cache_file) && filemtime($cache_file) >= time() - 3600)
                            {
                                $aFiles = include($cache_file);
                            } else {
                                $aFiles = rx_glob($sBasePath);
                                file_put_contents($cache_file, "<?php\nreturn ".var_export($aFiles, true).";");
                            }

                            if (count($aFiles)) {
                                $namespace = !empty($module['BasePath']) ? 'ruxon\modules\\'.$sModuleAlias : null;
                                $namespace_replace = !empty($module['BasePath']) ? realpath($module['BasePath']) : null;

                                foreach ($aFiles as $file) {
                                    self::require_file($file, true, true, $namespace, $namespace_replace);
                                }
                            }
                        }
                    }

                    break;
            }
        }

        return true;
    }

    public static function loadFramework()
    {
        $sBasePath = 'ruxon/framework/src';

        $cache_file = RX_PATH.'/runtime/cache/framework.classes.php';

        if (!is_dir(RX_PATH.'/runtime/cache/'))
            mkdir (RX_PATH.'/runtime/cache/', 0777, true);

        if (!RUXON_DEBUG && file_exists($cache_file) && filemtime($cache_file) >= time() - 3600)
        {
            $aFiles = include($cache_file);
        } else {
            $aFiles = rx_glob($sBasePath);
            file_put_contents($cache_file, "<?php\nreturn ".var_export($aFiles, true).";");
        }

        if (count($aFiles)) {
            foreach ($aFiles as $file) {
                self::require_file($file, true);
            }
        }

        return true;
    }

    public static function require_file($sPath, $bAbs = false, $bWithNamespace = false, $namespace = null, $namespace_replace = null)
    {
        $aResult = false;

        $sFileName = basename($sPath);
        $aFileName = explode(".", $sFileName);
        if (isset($aFileName[1]) && ($aFileName[1] == 'class' || $aFileName[1] == 'interface' || $aFileName[1] == 'trait'))
        {
            if (!isset($_ENV['RUXON_AUTOLOAD_CLASSES'][$aFileName[0]]))
            {
                if ($bAbs) {
                    $_ENV['RUXON_AUTOLOAD_CLASSES'][$aFileName[0]] = $sPath;
                } else {
                    $_ENV['RUXON_AUTOLOAD_CLASSES'][$aFileName[0]] = RX_PATH . '/' . $sPath;
                }
            }

            if ($bWithNamespace)
            {
                if ($namespace === null || $namespace_replace === null) {
                    $key = substr($sPath, strpos($sPath, "/ruxon/") + 1);
                    $key = substr($key, 0, strrpos($key, ".class.php"));
                    $key = str_replace("/", "\\", $key);
                } else {
                    $key = str_replace($namespace_replace, $namespace, realpath($sPath));
                    $key = substr($key, 0, strrpos($key, ".class.php"));
                    $key = str_replace("/", "\\", $key);
                }

                if (!isset($_ENV['RUXON_AUTOLOAD_CLASSES'][$key]))
                {
                    if ($bAbs) {
                        $_ENV['RUXON_AUTOLOAD_CLASSES'][$key] = $sPath;
                    } else {
                        $_ENV['RUXON_AUTOLOAD_CLASSES'][$key] = RX_PATH . '/' . $sPath;
                    }
                }
            }

            return true;
        } else {
            if ($bAbs) {
                $sFullPath = $sPath;
            } else {
                $sFullPath = RX_PATH.'/'.$sPath;
            }
            if (!$aResult = include($sFullPath)) {
                if (!is_array($aResult)) {
                    throw new RxException('Файл "'.RX_PATH.'/'.$sPath.'" не может быть загружен.');
                }
            }
        }

        return $aResult;
    }

    public static function loadConfigFile($path, $file)
    {
        $data = false;

        if (file_exists($path.'/'.$file.'.json')) {
            $data = (array) json_decode(file_get_contents($path.'/'.$file.'.json'), true);
        } else if (file_exists($path.'/'.$file.'.inc.php'))
        {
            $data = include($path.'/'.$file.'.inc.php');
        }

        return $data;
    }
}