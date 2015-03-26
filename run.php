<?php
// router.php
if (preg_match('/^\/castillo\/panel\/static\//', $_SERVER["REQUEST_URI"])) {
    return false;
} else if (preg_match('/^\/panel\/?$/', $_SERVER["REQUEST_URI"])) {
    include_once 'castillo/panel/index.php';
} else if (preg_match('/^\/panel\/api([\/\?#].*)?$/', $_SERVER["REQUEST_URI"])) {
    include_once 'castillo/panel/api.php';
} else if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else { 
    include_once 'index.php';
}
?>