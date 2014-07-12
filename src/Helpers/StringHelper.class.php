<?php

class StringHelper
{
	public static function replace($sSearch, $sReplace, $sSubject)
	{
		return str_replace($sSearch, $sReplace, $sSubject);
	}

    public static function substr($sString, $nStart = 0, $nMaxLen = 150)
	{
		$aEndWords = array('.', '!', '?', ' ');

		$nLen = strlen($sString);
		if ($nLen <= $nMaxLen) {
			return $sString;
		} else {
			for ($i = $nMaxLen - 1; $i > 0; $i--) {
				if (in_array($sString{$i}, $aEndWords) !== false) {
					return substr($sString, $nStart, $i).'...';
				}
			}

			return $sString;
		}
	}

    public static function translit($str)
    {
        $str = strtolower($str);
        
        $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё" => "e", "ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", 
        " "=> "_", "."=> "", "/"=> "_", "!" => "", "," => "", '"' => "", "'" => "", "-", "_", "(" => "", ")" => "", "&" => "", ":" => "", "+" => "", "#" => "", "№" => "", "«" => "", "»" => "", "%" => ""
        );
        return strtr($str,$tr);
    }
    
    public static function generatePassword($max = 10)
    {
        $chars="1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $size=strLen($chars)-1; 
        $password=null; 
        while($max--)
            $password.=$chars[rand(0,$size)]; 
        
        return $password;
    }

    public static function numb ($number, $one, $two, $five)
    {
        if (($number - $number % 10) % 100 != 10) {
            if ($number % 10 == 1) {
                $result = $one;
            } elseif ($number % 10 >= 2 && $number % 10 <= 4) {
                $result = $two;
            } else {
                $result = $five;
            }
        } else {
            $result = $five;
        }
        return $result;

    }
}