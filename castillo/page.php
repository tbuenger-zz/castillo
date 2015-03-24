<?php

require_once 'core.php';
require_once 'file.php';
require_once 'blueprint.php';

class Page extends ValueCollection{
    public function __construct($directory) {
        parent::__construct();
        $this->__directory__ = $directory;
        $this->__name__ = normalize_identifier(pathinfo($directory, PATHINFO_FILENAME));
        $this->__pages__ = new ValueCollection();
        $this->__files__ = new ValueCollection();
        $this->__template__ = 'default';
    }

    public function name() {
        return $this->__name__();
    } 

    public function pages() {
        return $this->__pages__();
    }

    public function files() {
        return $this->__files__();
    }

    public function page($name) {
        return $this->__pages__->$name();
    }

    public function file($name) {
        return $this->__files__->$name();
    }

    public function template() {
        return $this->__template__;
    }

    public function addFile($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'yaml') {
            $this->addYaml($filename);
        }

        if ($ext == 'jpeg') {
            $info = $this->addFileInfo($filename);
            $info->__append(array('url'=>$filename));
        }

        if ($ext == 'info') {
            $without_ext = pathinfo($filename, PATHINFO_FILENAME);
            $info = $this->addFileInfo($without_ext);

            $yaml_content = Spyc::YAMLLoad(path_combine($this->__directory__, $filename));
            $info->__append($yaml_content);
        }
    }

    private function addFileInfo($filename) {
        $new_file = new File($this->__directory__, $filename);
        return array_get_or_create($this->__files__->__items__, $new_file->name(), function() use ($new_file) {return $new_file;});
    }

    public function addPage($page) {
        return array_get_or_create($this->__pages__->__items__, $page->name(), function() use ($page) {return $page;});

    }

    private function addYaml($filename) {
        $yaml_content = Spyc::YAMLLoad(path_combine($this->__directory__, $filename));
        $this->__template__ = normalize_identifier(pathinfo($filename, PATHINFO_FILENAME));

        // read blueprint from template and parse then yaml
        $blueprint = Blueprint::read($this->__template__);

        
        $this->__append($blueprint->parse($yaml_content));
    }

    public static function fromDirectory($dir) {
        // open handler for the directory
        $iter = new DirectoryIterator($dir);

        $page = new Page($dir);

        foreach( $iter as $item ) {
            // make sure you don't try to access the current dir or the parent
            if (!$item->isDot()) {
                if( $item->isDir() ) {
                    $subpage = Page::fromDirectory(path_combine($dir, $item->getFilename()));
                    $page->addPage($subpage);
                } else {
                    // print files
                    $page->addFile($item->getFilename());
                }
            }
        }
        return $page;
    }
}

?>