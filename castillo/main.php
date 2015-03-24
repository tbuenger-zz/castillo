<?php

define('__PATH_PARAM__', 'q');

require_once 'vendors/spyc.php';
require_once 'vendors/underscore.php';

require_once 'utils.php';
require_once 'page.php';
require_once 'router.php';

class Castillo
{
    public static $root_path;

    public function __construct() {
        static::$root_path = (path_combine(__DIR__, '..'));
    }

    private static function loadSite() {
       return Page::fromDirectory(path_combine(static::$root_path, 'content'));
    }

    private static function loadPage($site, $location) {
        error_log('Castillo: Accessing \''.$location.'\''); 
        return Router::locatePage($site, $location);
    }

    private static function loadTemplate($template) {
        $filename = $template.'.php';
        $filepath = realpath(path_combine(static::$root_path, 'templates', $filename));

        if (empty($filepath)){
            error_log('Castillo: No template named \''.$filename.'\''); 
            return 'error.php';
        }

        error_log('Castillo: Loaded template \''.$filename.'\''); 

        return $filepath;
    }

    public function render(){
        $site = static::loadSite();
        $page = static::loadPage($site, $_SERVER["PATH_INFO"]);
        include static::loadTemplate($page->template());
    }
        
}


?>