<?php

require_once __DIR__.'/../vendors/spyc.php';
require_once __DIR__.'/../vendors/underscore.php';
require_once __DIR__.'/../utils.php';

$action = array_get($_GET, 'action', '');
$category = array_get($_GET, 'category', '');
$file = array_get($_GET, 'file', '');


function readFiles($base, $dir) {
    $iter = new DirectoryIterator(path_combine($base, $dir));
    $result = array();
    foreach ($iter as $item) {
        if ($item->isDot())
            continue;
        $name = $item->getFilename();
        if ($item->isDir()) {
            array_push($result, array(
                'type' => 'directory',
                'name' => $name, 
                'children' => readFiles($base, path_combine($dir, $name))
            ));
        } else {
            array_push($result, array(
                'type' => 'file',
                'name' => $name,
                'content' => Spyc::YAMLLoad(path_combine($base, $dir, $name))));
        }
    }
    return $result;
}

if ($action == 'list') {

    $result = array('content' => array(
                                    'type' => 'directory',
                                    'name' => '', 
                                    'children' => readFiles(Path::$content, '')),
                    'blueprints' => array(
                                    'type' => 'directory',
                                    'name' => '', 
                                    'children' => readFiles(Path::$blueprints, ''))
                );

    echo json_encode($result, JSON_UNESCAPED_SLASHES);
}

if ($action == 'read') {

    $path = Path::below(Path::$root, path_combine($category, $file));

    if (is_file($path)) {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($ext, ['txt', 'info'])) {
            header("Content-Type: text/plain");
            readfile($path);
        }
    }
}

?>