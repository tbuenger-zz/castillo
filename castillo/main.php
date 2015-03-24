<?php

define('__PATH_PARAM__', 'q');

require_once 'vendors/spyc.php';
require_once 'vendors/underscore.php';


require_once 'utils.php';
require_once 'page.php';
require_once 'router.php';


$root_path = realpath(path_combine(__DIR__, '..'));

$site = Page::fromDirectory(path_combine($root_path, 'content'));

$location = array_get($_GET, __PATH_PARAM__, '');

echo 'location: ', $location.'<br />';

$page = Router::locatePage($site, $location);

echo 'page: ', $page->name().'<br />';

echo "searching for template: ". $page->template() ."<br />";

$template_path = realpath(path_combine($root_path, 'templates', $page->template().'.php'));
if (empty($template_path)){
    exit('<html>Error</html>');
}

echo 'template path: ', $template_path.'<br />';

require $template_path;

?>