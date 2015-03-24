<?php

require_once 'core.php';
require_once 'file.php';

class Page extends ObjArray {
    public function __construct($directory) {
        $this->__directory__ = $directory;
        $this->__name__ = escapename(pathinfo($directory, PATHINFO_FILENAME));
        $this->__pages__ = array();
        $this->__files__ = array();
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

    public function page($name = FALSE) {
        return array_get($this->__pages__, $name, null);
    }

    public function file($name = FALSE) {
        return array_get($this->__files__, $name, null);
    }

    public function template() {
        return $this->__template__;
    }

    public function addFile($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'yaml') {
            $yaml_content = Spyc::YAMLLoad(path_combine($this->__directory__, $filename));
            $this->embedArray($yaml_content);
            $this->__template__ = escapename(pathinfo($filename, PATHINFO_FILENAME));
        }

        if ($ext == 'jpeg') {
            $info = $this->addFileInfo($filename);
            $info->embedArray(array('url'=>$filename));
        }

        if ($ext == 'info') {
            $without_ext = pathinfo($filename, PATHINFO_FILENAME);
            $info = $this->addFileInfo($without_ext);

            $yaml_content = Spyc::YAMLLoad(path_combine($this->__directory__, $filename));
            $info->embedArray($yaml_content);
        }
    }

    private function addFileInfo($filename) {
        $new_file = new File($this->__directory__, $filename);
        return array_get_or_create($this->__files__, $new_file->name(), function() use ($new_file) {return $new_file;});
    }

    public function addPage($page) {
        return array_get_or_create($this->__pages__, $page->name(), function() use ($page) {return $page;});

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