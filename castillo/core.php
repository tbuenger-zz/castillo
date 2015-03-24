<?php

require_once 'utils.php';

class UndefinedValue {

    public function __call($method, $arguments) {
        return $this;
    }

    public function __toString() {
        return '[undefined]';
    }

}

class ValueCollection implements IteratorAggregate {

    protected function __append($data) {
        if (is_a($data, 'ValueCollection'))
            return $this->__append($data->__items__);
        foreach($data as $key => $val) {
            if (is_array($val))
                $val = new ValueCollection($val);

            $this->__items__[$key] = $val;
        }
        
    }

    public function __construct($data = array()) {
        $this->__items__ = array();
        $this->__append($data);
    }

    public function __call($method, $arguments) {
        if (isset($this->$method))
            return $this->$method;
        if (isset($this->__items__[$method]))
            return $this->__items__[$method];
        return new UndefinedValue();
    }

    /*
    public function __toString() {
        $result = '{';
        foreach($this->__items__ as $key => $val)
        {
            $result .= $key . ' => ';
            if (is_a ($val, 'DateTime'))
            {
                $result .= $val->format('c'); 
            }
            else
            {
                $result .= $val;
            }
            $result .= '; ';
        }
        return rtrim($result, '; ').'}';
    }
    */
 
    public function toArray()
    {
        return $this->__items__;
    }
 
    public function getIterator()
    {
        return new ArrayIterator($this->__items__);
    }

    public function count() {
        return count($this->__items__);
    }

    public function reverse() {
        return new ValueCollection(array_reverse($this->__items__));
    }

    public function sortBy($key) {
        if (is_string($key))
        {
            if ($key[0] == '-')
                return $this->sortBy(substr($key, 1))->reverse();
            $key = function($x) use ($key) {return $x->$key();};
        }
        return new ValueCollection(__::sortBy($this->__items__, $key));
    }

    public function filter($condition) {
        return new ValueCollection(array_filter($this->__items__, $condition));
    }


}

?>
