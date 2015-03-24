<?php

require_once 'core.php';

class File extends ValueCollection {
    public function __construct($directory, $file) {
        parent::__construct();
        $this->__directory__ = $directory;
        $this->__file__ = $file;
        $this->__name__ = normalize_identifier(pathinfo($directory, PATHINFO_FILENAME));
    }

    public function name() {
        return $this->__name__;
    }

    public function url() {
        return path_combine($this->__directory__, $this->__file__);
    }
}

?>