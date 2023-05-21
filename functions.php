<?php

/**
 * Получить миллионы
 */
function mln (int $v):int
{
    $v = abs($v);
    if($v > 999)
    {
        $v = 999;
    }
    return intval( $v . '000' . '000' );
}

/**
 * Получить миллиарды
 */
function mlrd (int $v):int
{
    $v = abs($v);
    if($v > 999)
    {
        $v = 999;
    }
    return intval( $v . '000' . '000' . '000' );
}

function head (array $arr, int $size = 5): array
{
    $size = abs($size);
    return array_slice($arr, 0, $size, true);
}

function tail (array $arr, int $size = 5): array
{
    $size = abs($size);
    $size = $size - ($size * 2);
    return array_slice($arr, $size, null, true);
}

/**
 * заменяем все "неверные" черточки.
 */
function replaceDash (string $str):string
{
    $notDash = ['‐','−','‒','⁃','–','—','―','᠆','‑','֊','⹃','➖','ᱼ',];
    return str_replace($notDash, '-', $str);
}

/**
 * Заменяем все возможные переносы строк и табуляцию на пробел.
 */
function removeEOL (string $v, string $replace = ' '):string
{
    return str_replace(["\n", "\r", "\r\n", "\t"], $replace, $v);
}

/**
 * заменяем несколько пробелов подряд на один.
 */
function replaceDoubleSpace (string $v):string
{
    return preg_replace('#\ {2,}#', ' ', $v);
}

/**
 * аналог strip_tags() только с указанием на что заменять
 */
function stripTags (string $v, string $replace = ' '):string
{
    return preg_replace('#<.*?>#', $replace, $v);
}

/**
 * sha1($value, false)
 */
function sha_ (string $value): string
{
    return sha1($value, false);
}

/**
 * пример вывода "dir/ad/ft/" OR "ad/ft/"
 *
 * @param string $value значения которое будет служить хешом длу папок.
 * @param string $dir главная папка.
 * @param integer $depth глубина вложений.
 * @param boolean $only_return_path true если создавать папки не нужно
 * @param boolean $dont_hash_value true если хешировать value не нужно.
 */
function createFolderTree (string $value, string $dir = '', int $depth = 1, bool $only_return_path = false, bool $dont_hash_value = false):false|string
{
    $value = $dont_hash_value ? $value : sha_($value);

    $dirs = [
        $dir
    ];
    $start = 0;

    foreach(range(1, $depth) as $lv)
    {
        array_push($dirs, substr($value, $start, 2));
        $start += 2;
    }

    $dirs = array_filter($dirs, fn($a) => $a !== '');
    
    $path = implode(DIRECTORY_SEPARATOR, $dirs);
    if(!$only_return_path)
    {
        if (!is_dir($path))
        {
            if (!mkdir($path, 0755, true))
            {
                return false;
            }
        }
    }
    
    return $path . '/';
}

function arrayToCSV (array $arr, string $separator = ';', string $enclosure = '', string $eol = PHP_EOL, string $voids = '-'): string
{
    $res = [];
    foreach ($arr as $k => $fields)
    {
        $fields = am($fields, function($field) use ($separator, $enclosure, $voids)
        {
            // удаляем переносы строк
            $field = trim(removeEOL($field));
            // заменяем &quot; > "
            $field = html_entity_decode($field);
            // удаляем $separator
            $field = str_replace($separator, '', $field);
            // оборачиваем значение в $enclosure
            $field = $enclosure . $field . $enclosure;
            // если значение пустая строка тогда заменяем на $voids
            $field = (trim($field) == '') ? $voids : $field;
            return $field;
        });
        
        // обьединяем массив используя $separator
        $res[] = implode($separator, $fields);

        unset($arr[$k]);
    }

    $res = implode($eol, $res);
    return trim($res);
}

/**
 * По умолчанию все констаты активны
 * json_encode с JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_FORCE_OBJECT
 *
 * @param boolean $JPR JSON_PRETTY_PRINT
 * @param boolean $JUU JSON_UNESCAPED_UNICODE
 * @param boolean $JFO JSON_FORCE_OBJECT
 */
function jsonEncode (array $arr, bool $JPR = true, bool $JUU = true, bool $JFO = true):string
{
	return json_encode($arr,
	($JPR ? JSON_PRETTY_PRINT : 0)|($JUU ? JSON_UNESCAPED_UNICODE : 0)|($JFO ? JSON_FORCE_OBJECT : 0)
	);
}

function jsonDecode (string $json):array
{
    return json_decode($json, true);
}

/**
 * Удалить указанный последний символ.
 * Если его нет, тогда возвращаем без изменений.
 */
function delLastChar (string $str, string $char, int $countRemove = 1):string
{
    foreach(range(0, $countRemove) as $v)
    {
        $lastChar = mb_substr($str, -1, null, 'UTF-8');
        if($lastChar == $char)
        {
            $str = mb_substr($str, 0, -1, 'UTF-8');
        }
        else
        {
            return $str;
        }
    }
    return $str;
}

/**
 * Удалить указанный последний символ без учета регистра.
 * Если его нет, тогда возвращаем без изменений.
 */
function delLastChari (string $str, string $char, int $countRemove = 1):string
{
    $str = sttolower($str);
    $char = sttolower($char);
    foreach(range(0, $countRemove) as $v)
    {
        $lastChar = mb_substr($str, -1, null, 'UTF-8');
        if($lastChar == $char)
        {
            $str = mb_substr($str, 0, -1, 'UTF-8');
        }
        else
        {
            return $str;
        }
    }
    return $str;
}

function xmlToArray(string $xml):array|false
{
    $object = @simplexml_load_string($xml);
    if($object === false)
    {
        return false;
    }
    return json_decode(json_encode($object), 1);
}

/**
 * Кириллицу в латиницу
 */
function translit (string $str): string
{
    $str = mb_strtolower($str,'UTF-8');
    $str = preg_replace('#[^А-ЯЁа-яёa-zA-Z\ 0-9]#u','',$str);
    $arr = [
    'а' => 'a',   'б' => 'b',   'в' => 'v',
    'г' => 'g',   'д' => 'd',   'е' => 'e',
    'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
    'и' => 'i',   'й' => 'y',   'к' => 'k',
    'л' => 'l',   'м' => 'm',   'н' => 'n',
    'о' => 'o',   'п' => 'p',   'р' => 'r',
    'с' => 's',   'т' => 't',   'у' => 'u',
    'ф' => 'f',   'х' => 'h',   'ц' => 'c',
    'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
    'ь' => '',  'ы' => 'y',   'ъ' => '',
    'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
    ' ' => '_'
    ];
    return strtr($str, $arr);
}

/**
 * Удаляет все что не входит в раскладку стандартной клавиатуры. Например U+2009 u+00a0
 */
function deleteUnknownChar (string $str):string
{
    $str = preg_replace('#[\\s]#u', ' ', $str);
	$str = preg_replace('#[\\n]#u', "\n", $str);
	$str = preg_replace('#[\\t]#u', "\t", $str);
	return preg_replace('#[^А-ЯЁа-яёa-zA-Z0-9\!\@\"\`\~\#\№\$\;\%\:\^\&\?\*\(\)\-\_\+\=\|\{\}\[\]\,\.\<\>\'\\s\/\\\\t\\n]#u', '', $str);
}

function fgc (string $path)
{
    return file_get_contents($path);
}

function fpc (string $path, string $content, int $flags = 0)
{
    return file_put_contents($path, $content, $flags);
}

/**
 * Бьет массив на указанное количество частей
 */
function arrayChunkPartition (array &$list, int $countParts ): array
{
    $listlen = count( $list );
    $partlen = floor( $listlen / $countParts );
    $partrem = $listlen % $countParts;
    $partition = [];
    $mark = 0;
    for ($px = 0; $px < $countParts; $px++)
    {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice( $list, $mark, $incr );
        $mark += $incr;
    }
    return $partition;
}

function htmlDecode ( ?string $str ):string
{
	if(is_null($str))
	{
		return '';
	}
	return htmlspecialchars ($str);
}

/**
 * Проверяет если ли в массиве указанный ключ
 */
function existKey (string $searchKey, array $arr ): bool
{
    return ( isset($arr[$searchKey]) ? true :
        ( isset($arr[0][$searchKey]) ? true : false )
    );
}

function arr_search ($needle, array &$arr, string $key)
{
   return array_search($needle, array_column($arr, $key));
}

function deCompress (string $str): string
{
    $str = base64_decode($str, true);
    if($str === false) return '';
    $str = gzuncompress($str);
    if($str === false) return '';
    return $str;
}

function enCompress (string $str, int $level = 9): string
{
   return base64_encode(gzcompress($str, $level));
}

function isСlosure($t)
{
    return $t instanceof \Closure;
}

/**
 * return current($arr);
 */
function up (array $arr):mixed
{
    return current($arr);
}

function am (array $arr, callable $fn): array
{
	return array_map($fn, $arr);
}

/**
 * убрать все кроме цифр
 */
function onlyInt(string $str): string
{
	return preg_replace('#[^0-9]#ms', '', $str);
}

/**
 * Ожидание в миллисекундах. значение 100 = 0.1 секунде.
 */
function msleep(int $v): void
{
    usleep( (1000*$v) );
}

function isJson(string $str):bool
{
	if(isInt($str))
	{
		return false;
	}
	json_decode($str);
	return json_last_error() === JSON_ERROR_NONE;
}

function isInt ($i): bool
{
	if(is_null($i)
	|| is_bool($i)
	|| is_array($i)
	|| is_object($i))
	{
		return false;
	}
	if(preg_match('#^0$#', $i))
	{
		return true;
	}
	if(preg_match('#^\-?[1-9][0-9]{0,}$#', $i))
	{
		return true;
	}
	return false;
}

function subst (string $string, int $offset, ?int $length = null):string
{
    return mb_substr($string, $offset, $length, 'UTF-8');
}

/**
 * mb_strpos($str, $find, 0, 'UTF-8')
 */
function stpos (string $str, string $find, int $offset = 0):int|false
{
    return mb_strpos($str, $find, $offset, 'UTF-8');
}
function stipos (string $str, string $find, int $offset = 0):int|false
{
    return mb_stripos($str, $find, $offset, 'UTF-8');
}

function sttolower (string $str):string
{
    return mb_strtolower($str, 'UTF-8');
}

function sttoupper (string $str):string
{
    return mb_strtoupper($str, 'UTF-8');
}

function stlen (string $str):int
{
    return mb_strlen($str, 'UTF-8');
}

/**
 * вконце скрипты выводит время выполнения скрипта.
 * Данную функцию лучше выводить вверху.
 */
function timeRun (): void
{
    if(class_exists('St_timeRun') === false)
    {
        Class St_timeRun
        {
            public static $__start;
            public static $__trash;
            
            public function __destruct()
            {
                echo PHP_EOL . 'Время выполнения скрипта: '.round(microtime(true) - St_timeRun::$__start, 3)." сек.\n";
            }
        }
        St_timeRun::$__start = microtime(true);
    }
    St_timeRun::$__trash = new St_timeRun;
}

/**
 * Модифицированный print_r
 * @param string $desc Заголовок для вывода.
 */
function dd ($mixed = null, string $desc = 'print'): void
{
    $info = debug_backtrace();
	$line = implode(' < ', array_column($info, 'line'));
	$vLine = file( $info[0]['file'] );
    $fLine = $vLine[ $info[0]['line'] - 1 ];

    $trace = [];
    foreach($info as $file)
    {
        if(isset($file['file']))
        {
            $trace[] = pathinfo($file['file'])['filename'];
        }
        else
        {
            $trace[] = 'undefined file';
        }
    }
    $trace = implode(' < ', $trace);

    echo '------------ INFO ------------' . PHP_EOL;
    echo 'Trace of files: ' . $trace . PHP_EOL;
	echo 'Line code: [' . trim($fLine) . ']' . PHP_EOL;
	echo 'Line number: ' . $line . PHP_EOL;
    echo '------------ ' . sttoupper($desc) . ' ------------' . PHP_EOL;
	if(is_array($mixed) || is_object($mixed))
    {
        print_r($mixed);
    }
    else
    {
        var_dump($mixed);
    }
	echo PHP_EOL;
	echo '------------ END ------------' . PHP_EOL . PHP_EOL;
}
/**
 * Модифицированный print_r с exit();
 * @param string $desc Заголовок для вывода.
 */
function dde ($mixed = null, string $desc = 'print'): void
{
	if($mixed === null)
	{
		exit('dde( NULL )');
	}
    $info = debug_backtrace();
	$line = implode(' < ', array_column($info, 'line'));
	$vLine = file( $info[0]['file'] );
    $fLine = $vLine[ $info[0]['line'] - 1 ];

    $trace = [];
    foreach($info as $file)
    {
        if(isset($file['file']))
        {
            $trace[] = pathinfo($file['file'])['filename'];
        }
        else
        {
            $trace[] = 'undefined file';
        }
    }
    $trace = implode(' < ', $trace);

    echo '------------ INFO ------------' . PHP_EOL;
    echo 'Trace of files: ' . $trace . PHP_EOL;
	echo 'Line code: [' . trim($fLine) . ']' . PHP_EOL;
	echo 'Line number: ' . $line . PHP_EOL;
    echo '------------ ' . sttoupper($desc) . ' ------------' . PHP_EOL;
    if(is_array($mixed) || is_object($mixed))
    {
        print_r($mixed);
    }
    else
    {
        var_dump($mixed);
    }
	echo PHP_EOL;
	echo '------------ END ------------ EXIT' . PHP_EOL . PHP_EOL;
	exit();
}