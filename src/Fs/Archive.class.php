<?php

class Archive
{
    public static function createArchive($files = array(), $destination = '', $overwrite = false) 
    {
        if (file_exists($destination) && !$overwrite) {
            return false;
        }

        $valid_files = array();

        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        
        if (count($valid_files)) {
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }

            foreach ($valid_files as $file) {
                $zip->addFile($file, str_replace(RX_PATH, "", $file));
            }
            $zip->close();

            return file_exists($destination);
        } else {
            return false;
        }
    }
    
    public static function recListFiles($from = '.', $files = array(), $root = false, $core = false, $static = false)
    {
        if(!is_dir($from))
            return false;

        if($dh = opendir($from))
        {
            while( false !== ($file = readdir($dh)))
            {
                if($file == '.' || $file == '..') continue;
                if ($root && ($file == 'backups' || $file == '.DS_Store' || $file == '.git' || $file == '.idea' || $file == 'installed.lock' || $file == 'assets')) continue;
                if ($root && !$core && ($file == 'ruxon' || $file == 'admin' || $file == 'index.php' || $file == '.htaccess'  || $file == 'robots.txt' || $file == 'sitemap.xsl')) continue;
                if ($root && !$static && ($file == 'uploads')) continue;

                $path = $from . '/' . $file;
                if( is_dir($path))
                    $files += self::recListFiles($path, $files, false);
                else
                    $files[] = $path;
            }
            closedir($dh);
        }

        return $files;
    }
}