<?php

class FileHelper 
{
    public static function copyDirectory($src, $dst, $mode, $level)
    {
        if (!is_dir($dst))
            mkdir($dst, $mode, true);
        
        $folder=opendir($src);
        while(($file=readdir($folder))!==false)
        {
            if($file==='.' || $file==='..')
                continue;
            
            $path = $src."/".$file;
            $isFile = is_file($path);
            if($isFile)
            {
                copy($path,$dst."/".$file);
            }
            elseif($level)
                self::copyDirectory($path, $dst."/".$file, 0777, $level-1);
        }
        
        closedir($folder);
    }
    
    public static function md5Directory($dir)
    {
        if (!is_dir($dir))
        {
            return false;
        }

        $filemd5s = array();
        $d = dir($dir);

        while (false !== ($entry = $d->read()))
        {
            if ($entry != '.' && $entry != '..')
            {
                 if (is_dir($dir.'/'.$entry))
                 {
                     $filemd5s[] = self::md5Directory($dir.'/'.$entry);
                 }
                 else
                 {
                     $filemd5s[] = md5_file($dir.'/'.$entry);
                 }
             }
        }
        $d->close();
        
        return md5(implode('', $filemd5s));
    }
}