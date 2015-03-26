<?php

define('__PATH_PARAM__', 'q');

require_once 'vendors/spyc.php';
require_once 'vendors/underscore.php';

require_once 'utils.php';
require_once 'page.php';
require_once 'router.php';

function snippet($snippet_name) {
    include Path::below(Path::$snippets, $snippet_name.'php');
}

class Castillo
{

    private static function loadSite() {
       return Page::fromDirectory('');
    }

    private static function loadPage($site, $location) {
        error_log('Castillo: Accessing \''.$location.'\''); 
        return Router::locatePage($site, $location);
    }

    private static function loadTemplate($template) {
        $filepath = Path::below(Path::$templates, $template.'.php');

        if (empty($filepath)){
            error_log('Castillo: No template named \''.$template.'\''); 
            return 'error.php';
        }

        error_log('Castillo: Loaded template \''.$template.'\''); 

        return $filepath;
    }

    private static function location() {
        return explode('?', $_SERVER['REQUEST_URI'])[0];
    }

    public static function render(){
        Blueprint::init();
        $site = static::loadSite();
        $page = static::loadPage($site, self::location());
        include static::loadTemplate($page->template());
    }
        
}


?>