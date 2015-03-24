<?php

require_once 'utils.php';

class ObjValue {
    public function __construct($value) {
        $this->value = $value;
    }

    public function asDate() {
        return parse_date($this->value);
    }

    public function asInt() {
        return intval($this->value);
    }

    public function __toString() {
        return $this->value;
    }
}

class ObjArray extends stdClass implements ArrayAccess {

    protected function embedArray($data) {
        foreach($data as $key => $val) {
            if (is_array($val))
                $val = ObjArray::fromArray($val);
            else
                $val = new ObjValue($val);

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

?>
