<?php

date_default_timezone_set('Europe/Berlin');

function path_combine() {
    return join(DIRECTORY_SEPARATOR, func_get_args());
}

class Path {
    public static $root;
    public static $content;
    public static $templates;
    public static $blueprints;
    public static $snippets;
    public static function init() {
        self::$root = realpath(path_combine(__DIR__, '..'));
        self::$content = realpath(path_combine(self::$root, 'content'));
        self::$templates = realpath(path_combine(self::$root, 'templates'));
        self::$blueprints = realpath(path_combine(self::$root, 'blueprints'));
        self::$snippets = realpath(path_combine(self::$root, 'snippets'));
    }

    public static function below($base, $path) {
        $base = realpath($base);
        $path = realpath(path_combine($base, $path));
        if (strncmp($path, $base, strlen($base) != 0))
            return '';
        return $path;
    }
}

Path::init();

function array_get($array, $key, $default = null){
    if (isset($array[$key]) || array_key_exists($key, $array))
        return $array[$key];
    else
        return $default;
}

function array_get_or_create(&$array, $key, $creator) {
    if (!isset($array[$key]) && !array_key_exists($key, $array))
    {
        $array[$key] = $creator();
    }
    return $array[$key];
}

function parse_date($time) {
    $formats = [
    '!Y-m-dTH:i:sP',
    '!Y-m-dTH:i:sO',
    '!Y-m-dTH:i:s',
    '!Y-m-d H:i:sP',
    '!Y-m-d H:i:sO',
    '!Y-m-d H:i:s',
    '!d.m.Y H:i:s',
    '!j.n.Y H:i:s',
    '!Y-m-d H:i',
    '!d.m.Y H:i',
    '!j.n.Y H:i',
    '!Y-m-d',
    '!d.m.Y',
    '!j.n.Y'];

    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $time);
        if (DateTime::getLastErrors()["error_count"]==0)
            return $date;
    }
    return null;
}

function parse_boolean($value) {
    $value = strtolower(trim($value));
    return in_array($value, ['true', 'yes', '1']);
}

function normalize_identifier($filename) {
    return strtolower(str_replace('.', '_', $filename));
}

?>