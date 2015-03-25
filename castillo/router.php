<?php

require_once 'utils.php';
require_once 'page.php';

class ErrorPage extends Page {
    public function __construct($directory) {
        parent::__construct($directory);
        $this->__name__ = 'error';
        $this->__template__ = 'error';
    }
}

class Router {

    public static function locatePage($site, $location) {
        // split and remove emtpy parts
        $parts = array_filter(split("/", $location), 'strlen'); 
        $current_page = $site;
        foreach ($parts as $part) {
            $part = normalize_identifier($part);
            $next_page = $current_page->page($part);
            if (!$next_page) {                
                error_log('Castillo: No page for \''.$location.'\''); 
                return new ErrorPage($site->directory());
            }
            $current_page = $next_page;
        }
        return $current_page;
    }

}

?>