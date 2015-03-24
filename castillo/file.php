<?php

require_once 'core.php';

class File extends ObjArray {
    public function __construct($directory, $file) {
        $this->__directory__ = $directory;
        $this->__file__ = $file;
        $this->__name__ = escapename(pathinfo($directory, PATHINFO_FILENAME));
    }

    public function name() {
        return $this->__name__;
    }

    public function url() {
        return path_combine($this->__directory__, $this->__file__);
    }
}

?>