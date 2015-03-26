<?php

require_once 'core.php';
require_once 'file.php';
require_once 'blueprint.php';

class Page extends ValueCollection{
    public function __construct($directory) {
        parent::__construct();
        $this->__parent__ = null;
        $this->__directory__ = $directory;
        $this->__name__ = normalize_identifier(pathinfo($directory, PATHINFO_FILENAME));
        $this->__pages__ = new ValueCollection();
        $this->__files__ = new ValueCollection();
        $this->__template__ = 'default';
    }

    public function parent() {
        return $this->__parent__;
    }

    public function url() {
        if (is_null($this->__parent__))
            return $this->__name__;
        else
            return path_combine($this->__parent__->url(), $this->__name__);
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
        switch (pathinfo($filename, PATHINFO_EXTENSION)) {
            case 'txt':
                $this->addYaml($filename);
                break;

            case 'jpeg':
                $info = $this->addFileInfo($filename);
                $info->__append(array('url' => $filename));
                break;

            case 'info':
                $without_ext = pathinfo($filename, PATHINFO_FILENAME);
                $info = $this->addFileInfo($without_ext);
                $yaml_content = Spyc::YAMLLoad(Path::below(Path::$content, path_combine($this->__directory__, $filename)));
                $info->__append($yaml_content);
                break;                
        }
    }

    private function addFileInfo($filename) {
        $fileinfo = new File($this->__directory__, $filename);
        return array_get_or_create($this->__files__->__items__, $fileinfo->name(), function() use ($fileinfo) {return $fileinfo;});
    }

    public function addPage($page) {
        return array_get_or_create($this->__pages__->__items__, $page->name(), function() use ($page) {return $page;});
    }

    private function addYaml($filename) {
        $this->__template__ = normalize_identifier(pathinfo($filename, PATHINFO_FILENAME));
        $data = Blueprint::get($this->__template__)->parseFile(Path::below(Path::$content, path_combine($this->__directory__, $filename)));
        $this->__append($data);
    }

    public static function fromDirectory($dir) {
        $iter = new DirectoryIterator(Path::below(Path::$content, $dir));
        $page = new Page($dir);
        foreach ($iter as $item) {
            if ($item->isDot())
                continue;
            if ($item->isDir()) {
                $subpage = Page::fromDirectory(path_combine($dir, $item->getFilename()));
                $subpage->__parent__ = $page;
                $page->addPage($subpage);
            } else {
                $page->addFile($item->getFilename());
            }
        }
        return $page;
    }
}

?>