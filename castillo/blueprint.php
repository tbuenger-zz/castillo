<?php

require_once 'utils.php';
require_once 'core.php';

abstract class FieldType {

    public function __construct($type) {
        $this->type = $type;
    }

    abstract public function parse($value);

    public function parseFile($filepath) {     
        return $this->parse(Spyc::YAMLLoad($filepath));
    }

    public function init($blueprint) {
        $this->label = array_get($blueprint, 'label', '');
        $this->description = array_get($blueprint, 'description', '');
        $this->default = $this->parse(array_get($blueprint, 'default', null));
    }

}

class EmptyFieldType extends FieldType {
    public function __construct() {
        parent::__construct('');
    }

    public function parse($value) {
        return array();
    }
}

class TextField extends FieldType {
    public function parse($value) {
        return is_null($value) ? '' : $value;
    }
}

class NumberField extends FieldType {
    public function parse($value) {
        return is_null($value) ? 0 : intval($value);
    }
}

class DateField extends FieldType {
    public function parse($value) {
        return is_null($value) ? Datetime::createFromFormat('!', '') : parse_date($value);
    }
}

class CheckboxField extends FieldType {
    public function parse($value) {
        return is_null($value) ? FALSE : parse_boolean($value);
    }
}

class CompoundField extends FieldType {

    public function init($blueprint) {
        parent::init($blueprint);
        $this->fields = array_get($blueprint, 'fields', array());
        foreach ($this->fields as $fieldname => &$subblueprint)
            $subblueprint = FieldFactory::create($subblueprint);
    }

    public function parse($value) {
        if (is_null($value))
            return new ValueCollection();

        $result = array();
        foreach ($this->fields as $key => $field_type)
            $result[$key] = $field_type->default;

        foreach ($value as $key => $subvalue)
            if ($field_type = array_get($this->fields, $key, null))
                $result[$key] = $field_type->parse($subvalue);

        return new ValueCollection($result);
    }

}

class ListField extends FieldType {

    public function init($blueprint) {
        parent::init($blueprint);
        $this->item_type = new CompoundField('item');
        $this->item_type->init($blueprint);
    }

    public function parse($value) {
        if (is_null($value))
            return [];
        return __::map($value, function($x){return $this->item_type->parse($x);});
    }

}

abstract class FieldFactory {

    private static $fieldTypes = array(
        'text' => 'TextField',
        'date' => 'DateField',
        'checkbox' => 'CheckboxField',
        'number' => 'NumberField',
        'list' => 'ListField',
        'selectbox' => 'TextField',
        'url' => 'TextField',
        'textarea' => 'TextField',
        'mail' => 'TextField',
        'info' => 'TextField',
        );

    public static function create($blueprint, $type=null) {
        $typename = array_get($blueprint, 'type', null);
        $type = array_get(self::$fieldTypes, $typename, $type);
        if (is_null($type))
            exit('Unknown field type: '.$typename);
        $field = new $type($typename);
        $field->init($blueprint);
        return $field;
    }

}

abstract class Blueprint {

    private static $blueprints = array();

    public static function init() {
        foreach (new DirectoryIterator(Path::$blueprints) as $file) {
            if ($file->isFile()) {
                $name = normalize_identifier($file->getBasename('.yaml'));
                $yaml = Spyc::YAMLLoad($file->getPathname());
                self::$blueprints[$name] = FieldFactory::create($yaml, 'CompoundField');
            }
        }
    }

    public static function get($name) {
        return array_get(self::$blueprints, $name, new EmptyFieldType());
    }
}

?>