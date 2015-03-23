<?php

define('__REWRITTEN_PATH__', 'q');


define('__YAML_NAME__', 'page.yaml');

require_once 'vendors/spyc.php';

function path_combine() {
    return join(DIRECTORY_SEPARATOR, func_get_args());
}

function get(&$var, $default=null) {
    return isset($var) ? $var : $default;
}


class ObjArray extends stdClass implements ArrayAccess {

    protected function embedArray($data) {
        foreach($data as $key => $val) {
            if (is_array($val))
                $val = ObjArray::fromArray($val);
            if (!isset($this->{$key}))
                $this->{$key} = $val;
        }
    }

    public static function fromArray($data = array()) {
        $result = new ObjArray();
        $result->embedArray($data);
        return $result;
    }

    public function __call($method, $arguments) {
        if (null ===  self::$nullInstance)
             self::$nullInstance = new ObjArray();
        return isset($this->$method) ? $this->$method : self::$nullInstance;
    }

    public function toArray() {
        return (array)$this;
    }

    public function __toString() {
        ob_start();
        var_dump($this);
        $result = ob_get_clean();
        return $result;
    }
    static private $nullInstance = null;

    public function offsetSet($offset, $value) {
    }

    public function offsetUnset($offset) {
    }

    public function offsetExists($offset) {
        return isset($this->$offset);
    }

    public function offsetGet($offset) {
        return isset($this->$offset) ? $this->$offset : null;
    }
}

class File extends ObjArray {
    public function __construct($file) {
        echo "creating File: " . $file . '<br />';
        $this->__file__ = $file;
    }

    public function addFile($filename) {
        echo "adding file " . $filename . '<br />';
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'yaml') {
            $yaml_content = Spyc::YAMLLoad(path_combine(__root__, $yaml));
            $this->embedArray($yaml_content);
        }
    }
}

function escapename($filename) {
    return str_replace('.', '_', $filename);
}

class Page extends ObjArray {
    public function __construct($directory) {
        echo "creating Page: " . $directory . '<br />';
        $this->__root__ = $directory;
        $this->name = escapename(pathinfo($directory, PATHINFO_FILENAME));
    }

    public function addFile($filename) {
        echo "adding file " . $filename . '<br />';
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'yaml') {
            $yaml_content = Spyc::YAMLLoad(path_combine($this->__root__, $filename));
            var_dump($yaml_content);
            $this->embedArray($yaml_content);
        }

        if ($ext == 'jpeg') {
            $info = $this->addFileInfo($filename);
            $info->embedArray(array('url'=>$filename));
        }

        if ($ext == 'info') {
            $without_ext = pathinfo($filename, PATHINFO_FILENAME);
            $info = $this->addFileInfo($without_ext);

            $yaml_content = Spyc::YAMLLoad(path_combine($this->__root__, $filename));
            $info->embedArray($yaml_content);
        }
    }

    private function addFileInfo($filename) {
        $escaped = escapename($filename);
        echo "adding info for " . $escaped . '<br />';
        $this->embedArray(array($escaped => new File($filename)));
        return $this->{$escaped};
    }

    public function addPage($page) {
        echo "adding page " . $page->name . '<br />';
        $this->embedArray(array($page->name => $page));        
    }
}

$root_path = realpath(path_combine(__DIR__, '..'));
if ($root_path == FALSE)
    exit("What the heck?!");


# $Data = spyc_load_file('spyc.yaml');
# $Data = Spyc::YAMLLoad('spyc.yaml');

function create_page($dir) {
    // open handler for the directory
    $iter = new DirectoryIterator($dir);

    $page = new Page($dir);

    foreach( $iter as $item ) {
        // make sure you don't try to access the current dir or the parent
        if (!$item->isDot()) {
            if( $item->isDir() ) {
                $subpage = create_page(path_combine($dir, $item->getFilename()));
                $page->addPage($subpage);
            } else {
                // print files
                $page->addFile($item->getFilename());
            }
        }
    }
    return $page;
}


$site = create_page(path_combine($root_path, 'content'));
echo "<hr />";
echo $site->home()->page_jpeg()->description();
echo "<hr />";
echo $site['home'];
echo "<hr />";
echo $site;
echo "<hr />";
exit();


$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(path_combine($root_path, 'content'), 
        FilesystemIterator::CURRENT_AS_FILEINFO
      | FilesystemIterator::SKIP_DOTS ), 
    RecursiveIteratorIterator::SELF_FIRST );

foreach ( $iterator as $path ) {
    if ($path->isDir()) {
        echo (string)$path . '<br />';
    }
}

echo "<hr />";

if (isset($_GET[__REWRITTEN_PATH__])):

    $location = $_GET[__REWRITTEN_PATH__];
    $full_path = path_combine($root_path, 'content');


    $full_path = realpath($full_path);

    if ($full_path == FALSE)
        exit("Invalid path");

    if (strncmp($full_path, $root_path, strlen($root_path)) != 0)
        exit("Invalid path");



    $files = (scandir($full_path));

    foreach ($files as $file) {
        echo $file . " : " . is_dir(path_combine($full_path, $file)) . '<br />';
    }

    var_dump($files);



    exit();

    $p = new Page($full_path);

    $yaml = Spyc::YAMLLoad($full_path);

    var_dump($yaml);

    echo "<br />";

    $yaml = ObjArray::fromArray($yaml);

    var_dump($yaml);
    echo "<br />";
    echo get($yaml->foadoad(), 'default');

    echo "<br />";
    echo get($yaml->foo(), 'default');

    echo "<br />";
    echo get($yaml->fooad()->bar(), 'default');

    echo "<br />";
    echo get($yaml->fooad()->barad(), 'default');

    echo "<br />";
    echo get($yaml->foadoad(), 'default');

    foreach($yaml->bar()->baz() as $value) {
        echo $value . '<br />';
    }
    #$myfile = fopen($full_path, "r") or exit("Unable to open file!");
    #$content = fread($myfile,filesize($full_path));
    #fclose($myfile);


    $files = array_filter(scandir($root_path), 'is_file');
    var_dump($files);

endif




?>